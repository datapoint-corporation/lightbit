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

use \ReflectionClass;
use \ReflectionMethod;

/**
 * HttpAction.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpAction implements IHttpAction
{
	/**
	 * The arguments.
	 *
	 * @var array
	 */
	private $arguments;

	/**
	 * The controller.
	 *
	 * @var IHttpController
	 */
	private $controller;

	/**
	 * The route.
	 *
	 * @var IHttpRoute
	 */
	private $route;

	/**
	 * Constructor.
	 *
	 * @param IHttpRoute $route
	 *	The action route.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 */
	public function __construct(IHttpRoute $route, array $parameters = null)
	{
		$this->route = $route;
		$this->parameters = $parameters;
	}

	public function getArguments() : array
	{
		if (!isset($this->arguments))
		{
			$this->arguments = [];

			foreach ($this->getControllerMethod()->getParameters() as $i => $parameter)
			{
				$this->arguments[$name = $parameter->getName()] = $this->parameters[$name];
			}
		}

		return $this->arguments;
	}

	/**
	 * Gets the controller.
	 *
	 * @return IHttpController
	 *	The controller.
	 */
	public function getController() : IHttpController
	{
		return ($this->controller ?? ($this->controller = HttpControllerProvider::getInstance()->getController($this, $this->route->getControllerClass())));
	}

	/**
	 * Gets the controller class.
	 *
	 * @return ReflectionClass
	 *	The controller class.
	 */
	public function getControllerClass() : ReflectionClass
	{
		return $this->route->getControllerClass();
	}

	/**
	 * Gets the controller method.
	 *
	 * @return ReflectionMethod
	 *	The controller method.
	 */
	public function getControllerMethod() : ReflectionMethod
	{
		return $this->route->getControllerMethod();
	}

	/**
	 * Gets the route.
	 *
	 * @return IHttpRoute
	 *	The route.
	 */
	public function getRoute() : IHttpRoute
	{
		return $this->route;
	}

	/**
	 * Run.
	 */
	public function run() : void
	{
		($this->getController())->{$this->route->getControllerMethodName()}(...$this->getArguments());
	}
}
