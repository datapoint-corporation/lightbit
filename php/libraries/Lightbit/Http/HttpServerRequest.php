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

final class HttpServerRequest implements IHttpRequest
{
	private static $instance;

	public static final function getInstance() : HttpServerRequest
	{
		return (self::$instance ?? (self::$instance = new HttpServerRequest()));
	}

	private $headers;

	private $host;

	private $method;

	private $queryString;

	private function __construct()
	{

	}

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

	public final function getQueryString() : IHttpQueryString
	{
		return HttpServerRequestQueryString::getInstance();
	}
}
