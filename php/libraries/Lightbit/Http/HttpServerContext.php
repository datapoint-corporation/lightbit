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

use \Lightbit\Http\HttpServerRequest;
use \Lightbit\Http\HttpServerResponse;
use \Lightbit\Http\IHttpContext;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;

/**
 * HttpServerContext.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpServerContext implements IHttpContext
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServerContext
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServerContext
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServerContext
	{
		return (self::$instance ?? (self::$instance = new HttpServerContext()));
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the request.
	 *
	 * @return IHttpRequest
	 *	The request.
	 */
	public final function getRequest() : IHttpRequest
	{
		return HttpServerRequest::getInstance();
	}

	/**
	 * Gets the response.
	 *
	 * @return IHttpResponse
	 *	The response.
	 */
	public final function getResponse() : IHttpResponse
	{
		return HttpServerResponse::getInstance();
	}
}
