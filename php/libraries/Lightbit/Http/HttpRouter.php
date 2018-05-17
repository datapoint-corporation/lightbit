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

use \Lightbit\Configuration\IConfiguration;
use \Lightbit\Http\IHttpRouter;

/**
 * HttpRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRouter implements IHttpRouter
{
	/**
	 * The url.
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * The configuration.
	 *
	 * @var IConfiguration
	 */
	private $configuration;

	/**
	 * The routes.
	 *
	 * @var array
	 */
	private $routes;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->baseUrl = '/';
		$this->routes = [];
	}

	/**
	 * Sets a route.
	 *
	 * @param IHttpRoute $route
	 * 	The http route.
	 */
	public final function addRoute(IHttpRoute $route) : void
	{
		$this->routes[$route->getMethod()->getName()][$route->getPath()] = $route;
	}

	/**
	 * Sets a route list.
	 *
	 * @param array $routeList
	 * 	The http route list.
	 */
	public final function addRouteList(array $routeList) : void
	{
		foreach ($routeList as $i => $route)
		{
			$this->addRoute($route);
		}
	}

	/**
	 * Configure.
	 *
	 * @param IConfiguration $configuration
	 *	The configuration to accept from.
	 */
	public final function configure(IConfiguration $configuration) : void
	{
		$configuration->accept($this, [
			'lightbit.http.router.baseUrl' => 'setBaseUrl'
		]);
	}

	/**
	 * Gets the base url.
	 *
	 *
	 *
	 * @return string
	 *	The base url.
	 */
	public final function getBaseUrl() : string
	{
		return $this->baseUrl;
	}

	/**
	 * Resolves an incomming request.
	 *
	 * @param IHttpRequest $request
	 *	The incomming request.
	 *
	 * @return IAction
	 *	The action.
	 */
	public final function resolve(IHttpRequest $request) : IAction
	{

	}

	/**
	 * Sets the base uniform resource location.
	 *
	 * @param string $baseUrl
	 *	The base uniform resource location.
	 */
	public final function setBaseUrl(string $baseUrl) : void
	{
		$this->baseUrl = '/' . trim($baseUrl, '/');
	}
}
