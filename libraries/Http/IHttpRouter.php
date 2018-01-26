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

use \Lightbit\Routing\IRouter;
use \Lightbit\Routing\Route;

/**
 * IHttpRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpRouter extends IRouter
{
	/**
	 * Gets the address.
	 *
	 * @return string
	 *	The address.
	 */
	public function getAddress() : string;

	/**
	 * Gets the base url.
	 *
	 * @return string
	 *	The base url.
	 */
	public function getBaseUrl() : string;

	/**
	 * Gets the host.
	 *
	 * @return string
	 *	The host.
	 */
	public function getHost() : string;

	/**
	 * Gets the port.
	 *
	 * @return int
	 *	The port.
	 */
	public function getPort() : int;

	/**
	 * Gets the route.
	 *
	 * @return Route
	 *	The route.
	 */
	public function getRoute() : Route;

	/**
	 * Checks if the port is the default for the procotol.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isDefaultPort() : bool;

	/**
	 * Checks if it's the secure hypertext transport protocol.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isHttps() : bool;

	/**
	 * Creates a url.
	 *
	 * It begins by resolving the given route through the current active
	 * context, followed by the creation of a matching url.
	 *
	 * @param array $route
	 *	The route.
	 *
	 * @param bool $absolute
	 *	The absolute url flag.
	 *
	 * @return string
	 *	The result.
	 */
	public function url(array $route, bool $absolute = false) : string;
}