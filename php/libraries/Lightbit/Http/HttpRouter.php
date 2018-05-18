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

namespace Lightbit\Http;

use \Throwable;

use \Lightbit\Configuration\IConfiguration;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\IO\AssetManagement\AssetProvider;

/**
 * HttpRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRouter implements IHttpRouter
{
	/**
	 * The base uniform resource location.
	 *
	 * @var string
	 */
	private $absoluteBaseUrl;

	/**
	 * The base uniform resource location relative to the current host
	 * document root.
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * The configuration.
	 *
	 * @var IConfiguration
	 */
	private $configuration;

	/**
	 * The incomming request.
	 *
	 * @var IHttpRequest
	 */
	private $request;

	/**
	 * The routes.
	 *
	 * @var array
	 */
	private $routes;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->baseUrl = '/';
		$this->routes = [];
	}

	/**
	 * Sets a route.
	 *
	 * @param IHttpRoute $route
	 * 	The http route.
	 */
	public final function addRoute(IHttpRoute $route) : void
	{
		$this->routes[] = $route;
	}

	/**
	 * Sets a route list.
	 *
	 * @param array $routeList
	 * 	The http route list.
	 */
	public final function addRouteList(array $routeList) : void
	{
		foreach ($routeList as $i => $route)
		{
			$this->addRoute($route);
		}
	}

	/**
	 * Configure.
	 *
	 * @param IConfiguration $configuration
	 *	The configuration to accept from.
	 */
	public final function configure(IConfiguration $configuration) : void
	{
		$configuration->accept($this, [
			'lightbit.http.router.base' => 'setBaseUrl'
		]);
	}

	/**
	 * Gets the base uniform resource location.
	 *
	 * @return string
	 *	The base uniform resource location.
	 */
	public final function getAbsoluteBaseUrl() : string
	{
		if (!isset($this->absoluteBaseUrl))
		{
			$this->absoluteBaseUrl = ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) !== 'off')) ? 'https://' : 'http://');
			$this->absoluteBaseUrl .= ((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '127.0.0.1');
		}

		return $this->absoluteBaseUrl . $this->baseUrl;
	}

	/**
	 * Gets the base uniform resource location relative to the current host
	 * document root.
	 *
	 * @return string
	 *	The base uniform resource location.
	 */
	public final function getBaseUrl() : string
	{
		return $this->baseUrl;
	}

	/**
	 * Imports the routes from a script asset.
	 *
	 * @param string $asset
	 *	The asset identifier.
	 */
	public final function import(string $asset) : void
	{
		try
		{
			$routes = AssetProvider::getInstance()->getPhpAsset($asset)->include();

			foreach ($routes as $i => $route)
			{
				if (! ($route instanceof IHttpRoute))
				{
					throw new ErrorException(sprintf('Can not import route list, invalid element at position: "%s", of type "%s"', $i, gettype($route)));
				}

				$this->addRoute($route);
			}
		}
		catch (Throwable $e)
		{
			throw new HttpRouterException($this, sprintf('Can not import routes, unexpected error: "%s"', $asset), $e);
		}
	}

	/**
	 * Resolves an context to an action.
	 *
	 * @throws HttpRouteNotFoundException
	 *	Thrown when the given context can not be resolved through any of
	 *	the available routes.
	 *
	 * @param IHttpContext $context
	 *	The action context.
	 *
	 * @return IHttpAction
	 *	The action.
	 */
	public function resolve(IHttpContext $context) : IHttpAction
	{
		// We need to ensure the base url is removed from the route path,
		// or else patterns will never match.
		$path = '/' . trim($context->getRequest()->getPath(), '/') . '/';

		if (strlen($path) > ($i = strlen($this->baseUrl)))
		{
			$path = substr($path, ($i - 1));
		}

		else
		{
			$path = '/';
		}

		// Go through each route and make an attempt at matching and extracting
		// any relevant parameters from the path.
		foreach ($this->routes as $i => $route)
		{
			if ($action = $route->resolve($path, $_GET))
			{
				return $action;
			}
		}

		throw new HttpRouteNotFoundException(sprintf('Can not resolve from context, route not found: "%s"', $path));
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
}
