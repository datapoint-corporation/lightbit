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

use \Lightbit;
use \Throwable;

use \Lightbit\Application;
use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Environment;
use \Lightbit\Html\Rendering\HtmlViewProvider;
use \Lightbit\Http\HttpServer;
use \Lightbit\Http\HttpServerContext;
use \Lightbit\Http\HttpServerResponse;
use \Lightbit\Http\Routing\HttpRouterProvider;
use \Lightbit\RuntimeException;

/**
 * HttpApplication.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpApplication
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpApplication
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpApplication
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpApplication
	{
		return (self::$instance ?? (self::$instance = new HttpApplication()));
	}

	/**
	 * The document root path.
	 *
	 * @var string
	 */
	private $documentRootPath;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the document root path.
	 *
	 * @return string
	 *	The document root path.
	 */
	public final function getDocumentRootPath() : string
	{
		return ($this->documentRootPath ?? (
			$this->documentRootPath = $this->getServer()->getDocumentDirectoryPath()
		));
	}

	/**
	 * Gets the environment.
	 *
	 * @return Environment
	 *	The environment.
	 */
	public final function getEnvironment() : Environment
	{
		return Environment::getInstance();
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return LB_PATH_APPLICATION;
	}

	/**
	 * Gets the server.
	 *
	 * @return HttpServer
	 *	The server.
	 */
	public final function getServer() : HttpServer
	{
		return HttpServer::getInstance();
	}

	/**
	 * Checks the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDebug() : bool
	{
		return ($this->debug ?? ($this->debug = !Environment::getInstance()->isProduction()));
	}

	/**
	 * Sets the debug flag.
	 *
	 * @param bool $debug
	 *	The debug flag.
	 */
	public final function setDebug(bool $debug) : void
	{
		$this->debug = $debug;
	}

	/**
	 * Sets the document root path path.
	 *
	 * @param string $documentRootPath
	 *	The document root path.
	 */
	public final function setDocumentRootPath(string $documentRootPath) : void
	{
		$this->documentRootPath = rtrim($documentRootPath, '/');
	}

	/**
	 * Throwable.
	 *
	 * It is invoked if an uncaught throwable is detected during the
	 * application run procedure and should reset the response and
	 * generate the appropriate error document instead.
	 *
	 * @param Throwable $throwable
	 *	The throwable.
	 */
	private function throwable(Throwable $throwable) : void
	{
		$statusCode = 500;

		$statusDocument = HtmlViewProvider::getInstance()->getViewByPreference(
			('views://error-documents/' . $statusCode),
			('views://error-documents/default')
		);

		$response = HttpServerResponse::getInstance();
		$response->reset();
		$response->render($statusDocument, [
			'application' => $this,
			'statusCode' => 500,
			'statusMessage' => HttpStatusCodeMessageStaticProvider::getMessage(500),
			'throwable' => $throwable
		]);
	}

	/**
	 * Run.
	 *
	 * @return int
	 *	The exit status code.
	 */
	public final function run() : int
	{
		try
		{
			// Accept the application configuration from all registered
			// lightbit modules.
			ConfigurationProvider::getInstance()->getConfiguration(
				'lightbit.http.application'
			)

			->accept($this, [
				'debug' => 'setDebug'
			]);

			// Get the action.
			$action = HttpRouterProvider::getInstance()->getRouter()->resolve(
				HttpServerContext::getInstance()
			);

			$action->run();
		}
		catch (Throwable $e)
		{
			$this->throwable($e);
			return 1;
		}

		return 0;
	}
}
