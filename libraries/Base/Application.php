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

use \Lightbit;
use \Lightbit\Base\Action;
use \Lightbit\Base\Context;
use \Lightbit\Base\ControllerNotFoundRouteException;
use \Lightbit\Base\IApplication;
use \Lightbit\Base\IContext;
use \Lightbit\Base\ModuleNotFoundException;
use \Lightbit\Base\ModuleNotFoundRouteException;
use \Lightbit\Data\Caching\NoCache;
use \Lightbit\Data\SlugManager;
use \Lightbit\Data\Sql\SqlConnection;
use \Lightbit\Exception;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Html\HtmlAdapter;
use \Lightbit\Html\HtmlDocument;
use \Lightbit\Http\HttpAssetManager;
use \Lightbit\Http\HttpQueryString;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpSession;
use \Lightbit\Http\QueryStringHttpRouter;

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
		parent::__construct(null, 'application', $path, null);

		$this->setComponentsConfiguration
		(
			[
				'data.cache' => [ '@class' => NoCache::class ],
				'data.cache.file' => [ '@class' => NoCache::class ],
				'data.cache.memory' => [ '@class' => NoCache::class ],
				'data.cache.network' => [ '@class' => NoCache::class ],
				'data.slug.manager' => [ '@class' => SlugManager::class ],
				'data.sql.connection' => [ '@class' => SqlConnection::class ],
				'html.adapter' => [ '@class' => HtmlAdapter::class ],
				'html.document' => [ '@class' => HtmlDocument::class ],
				'http.asset.manager' => [ '@class' => HttpAssetManager::class ],
				'http.query.string' => [ '@class' => HttpQueryString::class ],
				'http.request' => [ '@class' => HttpRequest::class ],
				'http.response' => [ '@class' => HttpRequest::class ],
				'http.router' => [ '@class' => QueryStringHttpRouter::class ],
				'http.session' => [ '@class' => HttpSession::class ]
			]
		);

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
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
		return $this->resolveContext($this, $route);
	}

	/**
	 * Resolves through a context.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @param array $route
	 *	The route.
	 *
	 * @return Action
	 *	The action.
	 */
	private function resolveContext(IContext $context, ?array $route) : Action
	{
		_resolveContext0:

		if (!isset($route))
		{
			$route = $context->getDefaultRoute();
		}

		else if (!isset($route[0]) || !$route[0])
		{
			$route = $context->getDefaultRoute() + $route;
		}

		$path = $route[0];
		$parameters = $route; 
		unset($parameters[0]);

		if ($path[0] == '/')
		{
			$context = Lightbit::getApplication();
			$path = substr($path, 1);
		}

		else if (strpos($path, '~/') === 0)
		{
			$action;

			try
			{
				$action = Action::getInstance();
			}
			catch (\Exception $e)
			{
				throw new Exception(sprintf('Route can not be relative, action not available: "%s"', $route[0]));
			}

			return $action->getController(substr($path, 2), $parameters);
		}

		else if (strpos($path, '@/') === 0)
		{
			$action;
			$path = substr($path, 2);

			try
			{
				$action = Action::getInstance();
			}
			catch (\Exception $e)
			{
				$action = $context;
			}
		}

		_resolveContext1:

		// If only a single token is present, the resolution becomes
		// pretty straight forward as it can only be done to another module.
		$i = strrpos($path, '/');

		if ($i === false)
		{
			try
			{
				$context = $context->getModule($path);
				$route = $context->getDefaultRoute() + $parameters;
				goto _resolveContext0;
			}
			catch (ModuleNotFoundException $e)
			{
				throw new ModuleNotFoundRouteException
				(
					$context,
					$route,
					$path,
					sprintf('Module not found: "%s", at context "%s"', $path, $context->getPrefix())
				);
			}
		}

		// If the controller id and action matches an existing controller
		// then we'll delegate the resolution to it.
		$controllerID = substr($path, 0, $i);
		$actionID = substr($path, $i + 1);

		if ($context->hasController($controllerID))
		{
			return $context->getController($controllerID)->resolve($actionID, $parameters);
		}

		// If all else fails, we'll make an attempt at resolving it recursively
		// through the child modules – a "goto" is used here purely for
		// performance.
		$moduleID = substr($path, 0, $i = strpos($path, '/'));

		try
		{
			$context = $context->getModule($moduleID);
		}
		catch (ModuleNotFoundException $e)
		{
			throw new ControllerNotFoundRouteException
			(
				$context,
				$route,
				$controllerID,
				sprintf('Controller not found: "%s", at context "%s"', $controllerID, $context->getPrefix())
			);
		}

		$path = substr($path, $i + 1);
		goto _resolveContext1;
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	public function run() : int
	{
		$result;

		if (Lightbit::isCli())
		{
			$result = $this->resolve($this->getDefaultRoute())->run();
		}
		else
		{
			$result = $this->getHttpRouter()->resolve()->run();
		}

		return (is_int($result) ? $result : 0);
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