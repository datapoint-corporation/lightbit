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

use \Lightbit\Data\Collections\StringMap;

use \Lightbit\Data\Collections\IStringMap;
use \Lightbit\Http\IHttpContext;
use \Lightbit\Http\IHttpRoute;

/**
 * HttpAction.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpAction implements IHttpAction
{
	/**
	 * The argument list.
	 *
	 * @var array
	 */
	private $argumentList;

	/**
	 * The argument map.
	 *
	 * @var array
	 */
	private $argumentMap;

	/**
	 * The controller.
	 *
	 * @var IHttpController
	 */
	private $controller;

	/**
	 * The context.
	 *
	 * @var IHttpContext
	 */
	private $context;

	/**
	 * The route.
	 *
	 * @var IHttpRoute
	 */
	private $route;

	/**
	 * Constructor.
	 *
	 * @param IHttpContext $context
	 *	The action context.
	 *
	 * @param IHttpContext $context
	 *	The action route.
	 *
	 * @param array $tokenMap
	 *	The action
	 */
	public function __construct(IHttpContext $context, IHttpRoute $route, array $argumentMap)
	{
		$this->argumentMap = ($argumentMap ?? []);
		$this->context = $context;
		$this->route = $route;
	}

	/**
	 * Gets the context.
	 *
	 * @return IHttpContext
	 *	The context.
	 */
	public function getContext() : IHttpContext
	{
		return $this->context;
	}

	/**
	 * Gets the controller.
	 *
	 * @return IHttpController
	 *	The controller.
	 */
	public function getController() : IHttpController
	{
		return ($this->controller ?? ($this->controller = HttpControllerProvider::getInstance()->getController($this)));
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

	public function run() : void
	{
		$this->getController()->{$this->route->getControllerMethodName()}(...array_values($this->argumentMap));
	}
}
