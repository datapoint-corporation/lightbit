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

use \Lightbit;
use \Lightbit\Http\HttpRouterProvider;
use \Lightbit\Http\IHttpApplication;
use \Lightbit\Http\Runtime\RuntimeHttpContextProvider;

/**
 * HttpApplication.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class HttpApplication implements IHttpApplication
{
	/**
	 * The instance.
	 *
	 * @var HttpApplication
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return HttpApplication
	 * 	The instance.
	 */
	public static final function getInstance() : HttpApplication
	{
		return (self::$instance ?? (self::$instance = new HttpApplication()));
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 * 	The path.
	 */
	public final function getPath() : string
	{
		return LB_PATH_APPLICATION_DIRECTORY;
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	public final function run() : int
	{
		// Resolve and run.
		HttpRouterProvider::getInstance()->getRouter()->resolve(
			RuntimeHttpContextProvider::getInstance()->getContext()
		)

		->run();

		// Commit.
		Lightbit::getInstance()->commit();

		return 0;
	}
}
