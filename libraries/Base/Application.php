<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2017 Datapoint — Sistemas de Informação, Unipessoal, Lda.
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

namespace Lightbit\Base;

use \Lightbit\Action;
use \Lightbit\Base\IApplication;
use \Lightbit\Base\IComponent;
use \Lightbit\Base\Context;
use \Lightbit\ControllerNotFoundRouteException;
use \Lightbit\Exception;
use \Lightbit\ModuleNotFoundRouteException;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Context implements IApplication
{
	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The application path.
	 *
	 * @param array $configuration
	 *	The application configuration.
	 */
	public function __construct(string $path, array $configuration = null)
	{
		parent::__construct(null, 'application', $path, $configuration);
	}

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array
	{
		return [ '/site/index' ];
	}

	/**
	 * Resolves a route.
	 *
	 * @param array $route
	 *	The route to resolve.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve(?array $route) : Action
	{
		if (!isset($route))
		{
			$route = $this->getDefaultRoute();
		}

		else if (!isset($route[0]))
		{
			$route = $this->getDefaultRoute() + $route;
		}

		$parameters = $route;
		unset($parameters[0]);

		if ($route[0][0] === '/')
		{
			return $this->resolveContext($this, $route, substr($route[0], 1), $parameters);
		}

		$action = Action::getInstance();

		if (!$action)
		{
			throw new Exception(sprintf('Context is not available for relative route resolution: "%s"', $route[0]));
		}

		if (strpos($route[0], '~/') === 0 && strpos($route[0], '/', 3) === false)
		{
			return $action->resolve(substr($route[0], 2), $parameters);
		}
		
		if (strpos($route[0], '@/') === 0)
		{
			$context = $action->getController()->getContext();

			return $this->resolveContext
			(
				$action->getController()->getContext(),
				$route,
				substr($route[0], 2),
				$parameters
			);
		}

		throw new Exception(sprintf('Route path format is not supported: "%s"', $route[0]));
	}

	/**
	 * Resolves through a context.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @param string $path
	 *	The route path, without prefixes.
	 *
	 * @param array $parameters
	 *	The route parameters.
	 *
	 * @return Action
	 *	The action.
	 */
	private function resolveContext(IContext $context, array $route, string $path, array $parameters) : Action
	{
		_resolveContext:

		$i = strrpos($path, '/');

		if ($i === false)
		{
			// Since a path separator is not present, this can only mean
			// we're trying to resolve to a default route of a module.
			if (!$context->hasModule($path))
			{
				throw new ModuleNotFoundRouteException($route, sprintf('Module not found: "%s"', $path));
			}

			return $this->resolveContextDefault($context, $route, $parameters);
		}

		// First match is done directly by controller name within the
		// context and, if it exists, we'll simply delegate resolution to it.
		$controllerID = substr($path, 0, $i);
		$actionID = substr($path, $i + 1);

		if ($context->hasController($controllerID))
		{
			return $context->getController($controllerID)->resolve($actionID, $parameters);
		}

		// If it gets here it means we must be referring to a module
		// defined within the context.
		$i = strpos($path, '/');
		$moduleID = substr($path, 0, $i);

		if (!$context->hasModule($moduleID))
		{
			// We're throwing a controller not found here just to let the
			// developer know that had a controller exist, which he probably
			// what he's looking to have, this exception would not have been thrown.
			throw new ControllerNotFoundRouteException($route, sprintf('Controller not found: "%s"', $controllerID));
		}

		// We're going back to the top using a goto statetement
		// which is a lot faster than recursion.
		$context = $context->getModule($moduleID);
		$path = substr($path, $i + 1);

		goto _resolveContext;
	}

	/**
	 * Resolves through a context default route.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @param array $parameters
	 *	The parameters.
	 *
	 * @return Action
	 *	The action.
	 */
	private function resolveContextDefault(IContext $context, array $route, array $parameters) : Action
	{
		$route = $context->getDefaultRoute() + $parameters;

		$path = $route[0];
		$parameters = $route;
		unset($parameters[0]);

		return $this->resolveContext($context, $path, $parameters);
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	public function run() : int
	{
		$this->resolve([ '/my-module/default/index' ]);

		return 0;
	}

	/**
	 * Safely terminates the script execution after disposing of all
	 * application elements.
	 *
	 * @param int $status
	 *	The script exit status code.
	 */
	public final function terminate(int $status = 0) : void
	{
		$this->dispose();
		exit($status);
	}
}