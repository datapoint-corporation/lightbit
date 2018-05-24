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
use \Lightbit\Application;
use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Http\HttpServer;

/**
 * HttpApplication.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpApplication
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
	 *
	 * @param IEnvironment $environment
	 *	The application environment.
	 *
	 * @param IHttpServer $server
	 *	The application server.
	 */
	private function __construct()
	{
		ConfigurationProvider::getInstance()->getConfiguration(
			'lightbit.http.application'
		)

		->accept($this, [
			'debug' => 'setDebug',
			'document_root_path' => 'setDocumentRootPath'
		]);
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
	 * Sets the document root path.
	 *
	 * @param string $documentRootPath
	 *	The document root path.
	 */
	public final function setDocumentRootPath(string $documentRootPath) : void
	{
		$this->documentRootPath = rtrim($documentRootPath, '/');
	}
}
