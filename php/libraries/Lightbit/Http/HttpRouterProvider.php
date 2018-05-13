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

use \Lightbit\CommandOutOfSyncException;
use \Lightbit\Http\HttpRouter;
use \Lightbit\Http\HttpRouterFactory;

/**
 * HttpRouterFactory.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class HttpRouterProvider implements IHttpRouterProvider
{
	/**
	 * The instance.
	 *
	 * @var HttpRouterProvider
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return HttpRouterProvider
	 * 	The instance.
	 */
	public static final function getInstance() : HttpRouterProvider
	{
		return (self::$instance ?? (self::$instance = new HttpRouterProvider()));
	}

	/**
	 * The http router.
	 *
	 * @var IHttpRouter
	 */
	private $httpRouter;

	/**
	 * The http router factory.
	 *
	 * @var IHttpRouterFactory
	 */
	private $httpRouterFactory;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->httpRouterFactory = new HttpRouterFactory();
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 * 	The http router.
	 */
	public function getHttpRouter() : IHttpRouter
	{
		return ($this->httpRouter ?? ($this->httpRouter = $this->getHttpRouterFactory()->createHttpRouter()));
	}

	/**
	 * Gets the http router factory.
	 *
	 * @return IHttpRouterFactory
	 * 	The http router factory.
	 */
	public final function getHttpRouterFactory() : IHttpRouterFactory
	{
		return $this->httpRouterFactory;
	}

	/**
	 * Gets the http router factory.
	 *
	 * @throws CommandOutOfSyncException
	 * 	Thrown if the router instance is already in use by the application,
	 * 	rendering the given router factory useless.
	 *
	 * @param IHttpRouterFactory $httpRouterFactory
	 * 	The http router factory.
	 */
	public final function setHttpRouterFactory(IHttpRouterFactory $httpRouterFactory)
	{
		$this->httpRouterFactory = $httpRouterFactory;
	}
}
