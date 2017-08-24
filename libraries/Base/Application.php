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
use \Lightbit\Globalization\MessageSource;
use \Lightbit\Exception;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Html\HtmlAdapter;
use \Lightbit\Html\HtmlDocument;
use \Lightbit\Http\HttpAssetManager;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Http\HttpQueryString;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpSession;
use \Lightbit\Http\QueryStringHttpRouter;
use \Lightbit\IO\FileSystem\Alias;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Context implements IApplication
{
	/**
	 * The HTTP error documents.
	 *
	 * @type array
	 */
	private $httpErrorDocuments;

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

		$this->httpErrorDocuments = [ 'error-documents/default' ];

		$this->setComponentsConfiguration
		(
			[
				'data.cache' => [ '@class' => NoCache::class ],
				'data.cache.file' => [ '@class' => NoCache::class ],
				'data.cache.memory' => [ '@class' => NoCache::class ],
				'data.cache.network' => [ '@class' => NoCache::class ],
				'data.slug.manager' => [ '@class' => SlugManager::class ],
				'data.sql.connection' => [ '@class' => SqlConnection::class ],
				'globalization.message.source' => [ '@class' => MessageSource::class ],
				'html.adapter' => [ '@class' => HtmlAdapter::class ],
				'html.document' => [ '@class' => HtmlDocument::class ],
				'http.asset.manager' => [ '@class' => HttpAssetManager::class ],
				'http.query.string' => [ '@class' => HttpQueryString::class ],
				'http.request' => [ '@class' => HttpRequest::class ],
				'http.response' => [ '@class' => HttpResponse::class ],
				'http.router' => [ '@class' => QueryStringHttpRouter::class ],
				'http.session' => [ '@class' => HttpSession::class ]
			]
		);

		$this->setLocale('en-US');

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
	 * Gets the http error document path.
	 *
	 * @param int $httpStatusCode
	 *	The http status code.
	 *
	 * @return string
	 *	The http error document path.
	 */
	public final function getHttpErrorDocumentPath(int $httpStatusCode) : string
	{
		if (!isset($this->httpErrorDocuments[$httpStatusCode]))
		{
			$httpStatusCode = 0;
		}

		return (new Alias($this->httpErrorDocuments[$httpStatusCode]))->lookup
		(
			'php', 
			array_merge($this->getViewsBasePaths(), [ LIGHTBIT_PATH . '/views/http' ])
		);
	}

	/**
	 * Checks the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDebug() : bool
	{
		return Lightbit::isDebug();
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
	public final function resolve(?array $route) : Action
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
				$context = Action::getInstance()->getController()->getContext();
			}
			catch (\Exception $e) {}
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

		try
		{
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
		catch (\Throwable $e)
		{
			$this->throwable($e);
		}

		return 1;
	}

	/**
	 * Sets the debug flag.
	 *
	 * @param bool $debug
	 *	The debug flag.
	 */
	public final function setDebug(bool $debug) : void
	{
		Lightbit::setDebug($debug);
	}

	/**
	 * Sets the http error document.
	 *
	 * @param string $httpStatusCode
	 *	The http status code.
	 *
	 * @param string $httpErrorDocument
	 *	The http error document script file system alias.
	 */
	public final function setHttpErrorDocument(int $httpStatusCode, string $httpErrorDocument) : void
	{
		$this->httpErrorDocuments[$httpStatusCode] = $httpErrorDocument;
	}

	/**
	 * Sets the http error documents.
	 *
	 * @param array $httpErrorDocuments
	 *	The http error documents script file system alias, indexed by http
	 *	status code.
	 */
	public final function setHttpErrorDocuments(array $httpErrorDocuments) : void
	{
		foreach ($httpErrorDocuments as $httpStatusCode => $httpErrorDocument)
		{
			$this->setHttpErrorDocument($httpStatusCode, $httpErrorDocument);
		}
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

	/**
	 * Safely terminates the script execution after disposing of all
	 * application elements when an uncaught throwable object is found.
	 *
	 * @param Throwable $throwable
	 *	The throwable object.
	 */
	public final function throwable(\Throwable $throwable) : void
	{

		if (Lightbit::isCli())
		{
			// For Command Line Interfaces (CLI), output formatting is not supported
			// and the stack trace is simply dumped to the screen.
			do
			{
				echo get_class($throwable), ': ', $throwable->getMessage(), PHP_EOL;
				echo $throwable->getTraceAsString(), PHP_EOL;
				echo PHP_EOL;

				$throwable = $throwable->getPrevious();
			}
			while ($throwable);
		}
		else
		{
			// For HTTP environments, the applicable error response needs to be
			// generated according to the status code.
			$this->onHttpErrorResponse
			(
				(($throwable instanceof HttpStatusException) ? $throwable->getStatusCode() : 500), 
				$throwable
			);
		}

		try
		{
			$this->terminate(1);
		}
		catch (\Throwable $e)
		{
			exit(1);
		}
	}

	/**
	 * This method is invoked when an uncaught throwable is thrown after the
	 * application has been registered.
	 *
	 * The base implementation generates the applicable error response
	 * according to the current configuration.
	 *
	 * @param int $httpStatusCode
	 *	The HTTP status code.
	 *
	 * @param Throwable $throwable
	 *	The throwable object.
	 */
	protected function onHttpErrorResponse(int $httpStatusCode, \Throwable $throwable) : void
	{
		$document = $this->getHtmlDocument();
		$document->reset();
		
		$response = $this->getHttpResponse();
		$response->reset();
		$response->setStatusCode($httpStatusCode);

		(new View($this, $this->getHttpErrorDocumentPath($httpStatusCode)))->run([ 'httpStatusCode' => $httpStatusCode, 'throwable' => $throwable ]);
	}
}