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

use \Lightbit\Base\Action;
use \Lightbit\Base\Context;
use \Lightbit\Base\ControllerNotFoundRouteException;
use \Lightbit\Base\Application;
use \Lightbit\Base\ModuleNotFoundException;
use \Lightbit\Base\ModuleNotFoundRouteException;
use \Lightbit\Data\Caching\Cache;
use \Lightbit\Data\SlugManager;
use \Lightbit\Data\Sql\SqlConnection;
use \Lightbit\Globalization\MessageSource;
use \Lightbit\Exception;
use \Lightbit\Html\HtmlAdapter;
use \Lightbit\Html\HtmlDocument;
use \Lightbit\Http\HttpAssetManager;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Http\HttpQueryString;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpRouter;
use \Lightbit\Http\HttpSession;
use \Lightbit\Security\Cryptography\PasswordDigest;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Context
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
				'data.cache' => [ '@class' => Cache::class ],
				'data.cache.file' => [ '@class' => Cache::class ],
				'data.cache.memory' => [ '@class' => Cache::class ],
				'data.cache.network' => [ '@class' => Cache::class ],
				'data.slug.manager' => [ '@class' => SlugManager::class ],
				'data.sql.connection' => [ '@class' => SqlConnection::class ],
				'globalization.message.source' => [ '@class' => MessageSource::class ],
				'html.adapter' => [ '@class' => HtmlAdapter::class ],
				'html.document' => [ '@class' => HtmlDocument::class ],
				'http.asset.manager' => [ '@class' => HttpAssetManager::class ],
				'http.query.string' => [ '@class' => HttpQueryString::class ],
				'http.request' => [ '@class' => HttpRequest::class ],
				'http.response' => [ '@class' => HttpResponse::class ],
				'http.router' => [ '@class' => HttpRouter::class ],
				'http.session' => [ '@class' => HttpSession::class ],
				'security.cryptography.password.digest' => [ '@class' => PasswordDigest::class ],
			]
		);

		$this->setLocale('en-US');

		if ($configuration)
		{
			__object_apply($this, $configuration);
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

		return __asset_path_resolve_array
		(
			array_merge([ $this->getViewsBasePath(), LIGHTBIT_PATH . '/views/http' ]),
			'php',
			$this->httpErrorDocuments[$httpStatusCode]
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
		return __environment_debug_get();
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
			if (__environment_is_cli())
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
		__environment_debug_set($debug);
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

		if (__environment_is_cli())
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
