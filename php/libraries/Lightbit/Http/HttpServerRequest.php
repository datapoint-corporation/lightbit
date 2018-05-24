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

use \Lightbit\Http\HttpServerRequestQueryString;
use \Lightbit\Http\IHttpRequest;

/**
 * HttpServerRequest.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpServerRequest implements IHttpRequest
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServerRequest
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServerRequest
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServerRequest
	{
		return (self::$instance ?? (self::$instance = new HttpServerRequest()));
	}

	/**
	 * The headers.
	 *
	 * @var string
	 */
	private $headers;

	/**
	 * The host.
	 *
	 * @var string
	 */
	private $host;

	/**
	 * The method.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * The query string.
	 *
	 * @var IHttpQueryString
	 */
	private $queryString;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the method.
	 *
	 * @return string
	 *	The method.
	 */
	public final function getMethod() : string
	{
		if (!isset($this->method))
		{
			if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
			{
				$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
			}
			else
			{
				throw new HttpRequestException($this, sprintf('Can not get the request method, not available: "%s"', $this->getUrl()));
			}
		}

		return $this->method;
	}

	/**
	 * Gets the query string.
	 *
	 * @return IHttpQueryString
	 *	The query string.
	 */
	public final function getQueryString() : IHttpQueryString
	{
		return HttpServerRequestQueryString::getInstance();
	}
}
