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

use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Http\IHttpRouter;

/**
 * HttpRouterProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpRouterProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpRouterProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpRouterProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpRouterProvider
	{
		return (self::$instance ?? (self::$instance = new HttpRouterProvider()));
	}

	/**
	 * The router.
	 *
	 * @var IHttpRouter
	 */
	private $router;

	/**
	 * The router factory.
	 *
	 * @var IHttpRouterFactory
	 */
	private $routerFactory;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		ConfigurationProvider::getInstance()->getConfiguration(
			'lightbit.http.router'
		)

		->accept($this, [
			'factory' => 'setRouterFactory'
		]);
	}

	/**
	 * Gets the router.
	 *
	 * @throws HttpRouterFactoryException
	 *	Thrown if the router fails to be created, regardless of the
	 *	actual reason, which should be defined in the exception chain.
	 *
	 * @return IHttpRouter
	 *	The router.
	 */
	public final function getRouter() : IHttpRouter
	{
		return ($this->router ?? ($this->router = $this->getRouterFactory()->createRouter()));
	}

	/**
	 * Gets the router factory.
	 *
	 * @return IHttpRouterFactory
	 *	The router factory.
	 */
	public final function getRouterFactory() : IHttpRouterFactory
	{
		return ($this->routerFactory ?? ($this->routerFactory = new HttpRouterFactory()));
	}

	/**
	 * Sets the router factory.
	 *
	 * @param IHttpRouterFactory $routerFactory
	 *	The router factory.
	 */
	public final function setRouterFactory(IHttpRouterFactory $routerFactory) : void
	{
		$this->router = null;
		$this->routerFactory = $routerFactory;
	}
}
