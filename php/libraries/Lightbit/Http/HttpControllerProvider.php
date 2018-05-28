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

use \Lightbit\Http\HttpControllerFactory;

use \Lightbit\Http\IHttpController;
use \Lightbit\Http\IHttpControllerFactory;

/**
 * HttpControllerProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpControllerProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpControllerProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpControllerProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpControllerProvider
	{
		return (self::$instance ?? (self::$instance = new HttpControllerProvider()));
	}

	/**
	 * The controller factory.
	 *
	 * @var IHttpControllerFactory
	 */
	private $controllerFactory;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets a controller.
	 *
	 * @throws HttpControllerFactoryException
	 *	Thrown if the controller creation fails.
	 *
	 * @param IHttpAction $action
	 *	The controller action.
	 *
	 * @return IHttpController
	 *	The controller.
	 */
	public final function getController(IHttpAction $action) : IHttpController
	{
		return $this->getControllerFactory()->createController($action);
	}

	/**
	 * Gets the controller factory.
	 *
	 * @return IHttpControllerFactory
	 *	The controller factory.
	 */
	public final function getControllerFactory() : IHttpControllerFactory
	{
		return ($this->controllerFactory ?? ($this->controllerFactory = new HttpControllerFactory()));
	}

	/**
	 * Sets the controller factory.
	 *
	 * @param IHttpControllerFactory $controllerFactory
	 *	The controller factory.
	 */
	public final function setControllerFactory(IHttpControllerFactory $controllerFactory) : void
	{
		$this->controllerFactory = $controllerFactory;
	}
}
