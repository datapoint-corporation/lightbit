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

namespace Lightbit\Routing;

use \ReflectionClass;
use \ReflectionException;

use \Lightbit;
use \Lightbit\Base\ControllerNotFoundContextException;
use \Lightbit\Base\IContext;
use \Lightbit\Base\ModuleNotFoundContextException;
use \Lightbit\Data\Parsing\BooleanParser;
use \Lightbit\Data\Parsing\FloatParser;
use \Lightbit\Data\Parsing\IntegerParser;
use \Lightbit\Data\Parsing\ObjectParser;
use \Lightbit\Data\Parsing\Parser;
use \Lightbit\Data\Parsing\ParserException;
use \Lightbit\Data\Parsing\SerialObjectParserException;
use \Lightbit\Data\Parsing\SlugObjectParserException;
use \Lightbit\Data\Parsing\StringParser;
use \Lightbit\Exception;
use \Lightbit\Routing\Action;
use \Lightbit\Routing\ActionNotFoundRouteException;
use \Lightbit\Routing\ControllerNotFoundRouteException;
use \Lightbit\Routing\ModuleNotFoundRouteException;

/**
 * Route.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Route
{
	/**
	 * The context.
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * The defaults.
	 *
	 * @var array
	 */
	private $defaults;

	/**
	 * The parameters.
	 *
	 * @var array
	 */
	private $parameters;

	/**
	 * The virtual path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The prefix.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The route context.
	 *
	 * @param array $route
	 *	The route description.
	 */
	public function __construct(IContext $context, ?array $route)
	{
		// If the route array is not set or its path is not set,
		// we will build it based on the context default route.
		if (!isset($route))
		{
			$route = $context->getDefaultRoute();
		}

		else if (!isset($route[0]))
		{
			$route = $context->getDefaultRoute() + $route;
		}

		// The route map must define the virtual path at the zero index,
		// as a non-empty string.
		if (!isset($route[0]) || !is_string($route[0]) || !$route[0])
		{
			throw new Exception(sprintf('Can not construct route, path is missing at index zero'));
		}

		$matches;

		// The route virtual path must be validated through the current
		// experession
		if (!preg_match('%^(@|\\/|~)((\\/([a-z][a-z0-9]+(\\-[a-z][a-z0-9]+)*))+)$%', $route[0], $matches))
		{
			throw new Exception(sprintf('Can not construct route, path format is incorrect at index zero: "%s"', $route[0]));
		}

		$this->context = $context;
		$this->defaults = [];
		$this->prefix = ($matches[1] ? $matches[1] : null);
		$this->path = substr($matches[2], 1);
		$this->parameters = array_diff_key($route, [ null ]);
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext
	{
		return $this->context;
	}

	/**
	 * Gets the parameters.
	 *
	 * @param bool $compact
	 *	The compact parameters flag.
	 *
	 * @return array
	 *	The parameters.
	 */
	public function getParameters(bool $compact = false) : array
	{
		$result = $this->parameters;

		if ($compact)
		{
			foreach ($this->defaults as $i => $parameter)
			{
				unset($result[$parameter]);
			}
		}

		return $result;
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Gets the prefix.
	 *
	 * @return string
	 *	The prefix.
	 */
	public function getPrefix() : ?string
	{
		return $this->prefix;
	}

	/**
	 * Checks if the route is absolute.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAbsolute() : bool
	{
		return ($this->prefix && $this->prefix === '/');
	}

	/**
	 * Resolves the route to an action.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve() : Action
	{
		$i;
		$context;
		$controller;
		$route = $this;

		$lightbit = Lightbit::getInstance();

		__LIGHTBIT_ROUTING_ROUTE__RESOLVE:
		$prefix = $route->prefix;
		$path = $route->path;

		// Set the initial resolution context according to the route
		// prefix, go recursively from there on.
		switch ($prefix)
		{
			case '@':
				$context = $route->getContext();
				break;

			case '/':
				$context = $lightbit->getApplication();
				break;

			case '~':
				$controller = $lightbit->getAction()->getController();
				$context = $controller->getContext();
				$path = $this->path;
				$id = $this->path;
				goto __LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_ACTION;
				break;
		}

		__LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_CONTEXT:

		// If it's a multi-token path, the first step is to check wether
		// or not a matching controller exists and, if so, delegate the
		// resolution to it.
		if ($i = strrpos($path, '/'))
		{
			try
			{
				$controller = $context->getController(substr($path, 0, $i));
			}
			catch (ControllerNotFoundContextException $e) 
			{
				goto __LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_CONTEXT_FALLBACK;
			}

			$method;
			$id = substr($path, $i + 1);

			__LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_ACTION:
			$reference = $controller->getActionMethodName($id);

			try
			{
				$method = (new ReflectionClass($controller))->getMethod($reference);
			}
			catch (ReflectionException $e)
			{
				throw new ActionNotFoundRouteException($route, sprintf('Can not resolve route, action not found: "%s"', $id), $e);
			}

			if ($method->isStatic() || !$method->isPublic())
			{
				throw new ActionNotFoundRouteException($route, sprintf('Can not resolve route, action method signature mismatch: "%s"', $id));	
			}

			$arguments = [];
			$argument = '?';

			try
			{
				foreach ($method->getParameters() as $i => $parameter)
				{
					$argument = $parameter->getName();

					if (isset($route->parameters[$argument]))
					{
						if ($type = $parameter->getType())
						{
							$arguments[] = ($route->parameters[$argument] = Parser::getInstance($type->getName())->parse($route->parameters[$argument]));
						}
						else
						{
							$arguments[] = $route->parameters[$argument];
						}

						continue;
					}

					$this->defaults[] = $argument;

					if ($parameter->isOptional())
					{
						$arguments[] = ($route->parameters[$argument] = $parameter->getDefaultValue());
						continue;
					}

					if ($parameter->allowsNull())
					{
						$arguments[] = ($route->parameters[$argument] = null);
						continue;
					}

					throw new MissingActionParameterRouteException($this, sprintf('Can not bind missing action parameter: "%s"', $argument));
				}
			}
			catch (SerialObjectParserException $e)
			{
				throw new SerialActionParameterRouteException($this, sprintf('Can not unserialize action parameter: "%s"', $argument), $e);
			}
			catch (SlugObjectParserException $e)
			{
				throw new SlugActionParameterRouteException($this, sprintf('Can not unslugify action parameter: "%s"', $argument), $e);
			}
			catch (ParserException $e)
			{
				throw new ActionParameterRouteException($this, sprintf('Can not parse action parameter: "%s"', $argument), $e);
			}
			

			return new Action($controller, $id, $reference, $arguments);
		}

		__LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_CONTEXT_FALLBACK:

		// Since it's not, we can only fallback to the next context within
		// the token chain.
		if ($i = strpos($path, '/'))
		{
			try
			{
				$context = $context->getModule(substr($path, 0, $i));
				$path = substr($path, $i + 1);
				goto __LIGHTBIT_ROUTING_ROUTE__RESOLVE__FROM_CONTEXT;
			}
			catch (ModuleNotFoundContextException $e) {}
		}
		else
		{
			try
			{
				$context = $context->getModule($path);
				$route = new Route($context, $context->getDefaultRoute());
				goto __LIGHTBIT_ROUTING_ROUTE__RESOLVE;
			}
			catch (ModuleNotFoundContextException $e) {}
		}

		__LIGHTBIT_ROUTING_ROUTE__THROW:
		if ($i = strrpos($path, '/'))
		{
			throw new ControllerNotFoundRouteException($route, sprintf('Can not resolve route, controller not found: "%s"', substr($path, 0, $i)));
		}

		throw new ModuleNotFoundRouteException($route, sprintf('Can not resolve route, module not found: "%s"', $path));
	}

	/**
	 * Converts the route to an array.
	 *
	 * @return array
	 *	The result.
	 */
	public function toArray() : array
	{
		return [ $this->prefix . '/' . $this->path ] + $this->parameters;	
	}
}