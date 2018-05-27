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
use \Lightbit\Environment;
use \Lightbit\RuntimeException;
use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Html\HtmlViewProvider;
use \Lightbit\Http\HttpServer;
use \Lightbit\Http\HttpServerResponse;

class HttpApplication
{
	private static $instance;

	public static final function getInstance() : HttpApplication
	{
		return (self::$instance ?? (self::$instance = new HttpApplication()));
	}

	private $documentRootPath;

	private function __construct()
	{

	}

	public final function getDocumentRootPath() : string
	{
		return ($this->documentRootPath ?? (
			$this->documentRootPath = $this->getServer()->getDocumentDirectoryPath()
		));
	}

	public final function getEnvironment() : Environment
	{
		return Environment::getInstance();
	}

	public final function getPath() : string
	{
		return LB_PATH_APPLICATION;
	}

	public final function getServer() : HttpServer
	{
		return HttpServer::getInstance();
	}

	public final function isDebug() : bool
	{
		return ($this->debug ?? ($this->debug = !Environment::getInstance()->isProduction()));
	}

	public final function setDebug(bool $debug) : void
	{
		$this->debug = $debug;
	}

	public final function setDocumentRootPath(string $documentRootPath) : void
	{
		$this->documentRootPath = rtrim($documentRootPath, '/');
	}

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

			echo 'OK';
		}
		catch (Throwable $e)
		{
			$this->throwable($e);
			return 1;
		}

		return 0;
	}
}
