<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2018 Datapoint — Sistemas de Informação, Unipessoal, Lda.
// https://www.datapoint.pt/
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.
// -----------------------------------------------------------------------------

namespace Lightbit\Http\Routing;

use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Data\Filtering\FilterParseException;
use \Lightbit\Data\Filtering\FilterProvider;
use \Lightbit\Http\Routing\HttpAction;
use \Lightbit\Http\Routing\HttpRouterContextPathException;
use \Lightbit\Http\Routing\HttpRouterContextPathTokenParseException;
use \Lightbit\Http\Routing\HttpRouterContextQueryStringParameterNotSetException;
use \Lightbit\Http\Routing\HttpRouterContextQueryStringParameterParseException;
use \Lightbit\Http\HttpServer;

use \Lightbit\Http\IHttpContext;
use \Lightbit\Http\Routing\IHttpRoute;
use \Lightbit\Http\Routing\IHttpRouter;

/**
 * HttpRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpRouter implements IHttpRouter
{
	/**
	 * The base uniform resource location.
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * The route list.
	 *
	 * @var array
	 */
	private $routeList;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->routeList = [];

		ConfigurationProvider::getInstance()->getConfiguration(
			'lightbit.http.router'
		)

		->accept($this, [
			'base_url' => 'setBaseUrl',
			'route_list' => 'addRouteList'
		]);
	}

	/**
	 * Sets an additional route.
	 *
	 * @param IHttpRoute $route
	 *	The route.
	 */
	public final function addRoute(IHttpRoute $route) : void
	{
		$this->routeList[] = $route;
	}

	/**
	 * Sets an additional route list.
	 *
	 * @param array $routeList
	 *	The route list.
	 */
	public final function addRouteList(array $routeList) : void
	{
		foreach ($routeList as $i => $route)
		{
			$this->addRoute($route);
		}
	}

	public final function createUrl(string $method, string $controllerClassName, string $controllerMethodName, array $argumentMap = null)
	{
		return $this->createUrlByRoute(
			$this->getRoute(
				$method,
				$controllerClassName,
				$controllerMethodName
			),

			$argumentMap
		);
	}

	public function createUrlByRoute(IHttpRoute $route, array $argumentMap = null) : string
	{
		$queryStringParameterMap = ($argumentMap ?? []);
		$pathTokenMap = [];

		// We first go through each path token and move it out from the
		// query string parameter map.
		foreach ($route->getPathTokenList() as $i => $pathToken)
		{
			if (isset($queryStringParameterMap[$pathToken]))
			{
				$pathTokenMap[$pathToken] = $queryStringParameterMap[$pathToken];
				unset($queryStringParameterMap[$pathToken]);
				continue;
			}

			throw new HttpRouterException($this, sprintf(
				'Can not create route url, missing path token in argument map: "%s"',
				$pathToken
			));
		}

		// Format the route according to the path pattern and the existing
		// path tokens.
		$url = $this->getBaseUrl();
		$url .= substr($route->formatPath($pathTokenMap), 1);

		if ($queryStringParameterMap)
		{
			$url .= '?' . http_build_query($queryStringParameterMap, '_', '&', PHP_QUERY_RFC1738);
		}

		return $url;
	}

	/**
	 * Gets the base uniform resource location.
	 *
	 * @return string
	 *	The base uniform resource location.
	 */
	public final function getBaseUrl() : string
	{
		return ($this->baseUrl ?? ($this->baseUrl = HttpServer::getInstance()->getDocumentDirectoryUrl()));
	}

	/**
	 * Sets the base uniform resource location.
	 *
	 * @param string $baseUrl
	 *	The base uniform resource location.
	 */
	public final function setBaseUrl(string $baseUrl) : void
	{
		if ($baseUrl = trim($baseUrl, '/'))
		{
			$this->baseUrl = '/' . $baseUrl . '/';
		}
		else
		{
			$this->baseUrl = '/';
		}
	}

	/**
	 * Gets a route.
	 *
	 * @throws HttpRouterRouteNotSetException
	 *	Thrown if no route exists matching the given constraints.
	 *
	 * @param string $method
	 *	The route method.
	 *
	 * @param string $controllerClassName
	 *	The route controller class name.
	 *
	 * @param string $controllerMethodName
	 *	The route controller method name.
	 *
	 * @return IHttpRoute
	 *	The route.
	 */
	public final function getRoute(string $method, string $controllerClassName, string $controllerMethodName) : IHttpRoute
	{
		foreach ($this->routeList as $i => $route)
		{
			if ($controllerClassName === $route->getControllerClassName()
				&& $controllerMethodName === $route->getControllerMethodName()
				&& $route->hasMethod($method))
			{
				return $route;
			}
		}

		throw new HttpRouterRouteNotSetException($this, sprintf(
			'Can not get route, not set: method "%s", controller "%s", method "%s"',
			$method,
			$controllerClassName,
			$controllerMethodName
		));
	}

	public final function resolve(IHttpContext $context) : IHttpAction
	{
		$request = $context->getRequest();
		$method = $request->getMethod();
		$path = $request->getPath();

		// We have to ensure the base url is present in the given request path
		// and, if not, this context is not applicable to this application.
		$base = $this->getBaseUrl();
		$baseLength = strlen($base);
		$pathLength = strlen($path);

		if ($pathLength < $baseLength || !(strtolower($base) === strtolower(substr($path, 0, $baseLength))))
		{
			throw new HttpRouterContextPathException($this, $context, sprintf(
				'Can not resolve context, base path mismatch: "%s"',
				$path
			));
		}

		$path = substr($path, $baseLength - 1);

		// Go through the existing routes, matching each one against the
		// request path and build the action based on the first match.
		foreach ($this->routeList as $i => $route)
		{
			if ($route->match($method, $path, $tokenMap))
			{
				$argumentMap = [];
				$queryStringParameterMap = $request->getQueryString()->toArray();

				// Since we have the route, we have to filter or create the
				// token and argument maps.
				foreach ($route->getControllerMethod()->getParameters() as $i => $parameter)
				{
					$name = $parameter->getName();

					if ($argument = ($tokenMap[$name] ?? $queryStringParameterMap[$name] ?? null))
					{
						if ($constraint = $parameter->getType())
						{
							try
							{
								$argumentMap[$name] = FilterProvider::getInstance()->getFilter($constraint->__toString())->transform($argument);
							}
							catch (FilterParseException $e)
							{
								if (isset($tokenMap[$name]))
								{
									throw new HttpRouterContextPathTokenParseException(
										$this,
										$context,
										sprintf(
											'Can not resolve path token, parsing failure: "%s"',
											$name
										),
										$e
									);
								}

								throw new HttpRouterContextQueryStringParameterParseException(
									$this,
									$context,
									sprintf(
										'Can not resolve query string parameter, parsing failure: "%s"',
										$name
									),
									$e
								);
							}
						}
					}
					else if ($parameter->isOptional())
					{
						$argumentMap[$name] = $parameter->getDefaultValue();
					}
					else if ($parameter->allowsNull())
					{
						$argumentMap[$name] = null;
					}
					else
					{
						throw new HttpRouterContextQueryStringParameterNotSetException($this, $context, sprintf(
							'Can not resolve query string parameter, parsing failure: "%s"',
							$name
						));
					}
				}

				return new HttpAction(
					$context,
					$route,
					$argumentMap
				);
			}
		}

		throw new HttpRouterContextRouteNotSet($this, $context, sprintf(
			'Can resolve context route, not set: "%s %s"',
			$context->getRequest()->getMethod(),
			rtrim($context->getRequest()->getPath(), '/')
		));
	}
}
