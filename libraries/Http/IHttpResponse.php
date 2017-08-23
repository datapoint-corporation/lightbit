<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2017 Datapoint — Sistemas de Informação, Unipessoal, Lda.
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

use \Lightbit\Http\IHttpMessage;

/**
 * IHttpResponse.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpResponse extends IHttpMessage
{
	/**
	 * Clears the active output buffers, headers and status code.
	 *
	 * If content has already been sent and, as such, the response can not
	 * be reset, an exception is thrown.
	 */
	public function reset() : void;

	/**
	 * Sets an header content.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param string $content
	 *	The header content.
	 *
	 * @param bool $replace
	 *	The header replace flag.
	 */
	public function setHeader(string $header, string $content, bool $replace = true) : void;

	/**
	 * Sets the status code.
	 *
	 * @param int $statusCode
	 *	The status code.
	 */
	public function setStatusCode(int $statusCode) : void;
}