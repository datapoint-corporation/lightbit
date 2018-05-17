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

use \Lightbit\Http\IHttpContext;
use \Lightbit\Http\IHttpMessage;
use \Lightbit\Http\IHttpUserAgent;

/**
 * IHttpRequest.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpRequest extends IHttpMessage
{
	/**
	 * Gets the content.
	 *
	 * @return string
	 *	The content.
	 */
	public function getContent() : ?string;

	/**
	 * Gets the content length.
	 *
	 * @return int
	 *	The content length.
	 */
	public function getContentLength() : int;

	/**
	 * Gets the content type.
	 *
	 * @return string
	 *	The content type.
	 */
	public function getContentType() : ?string;

	/**
	 * Gets the context.
	 *
	 * @return IHttpContext
	 *	The context.
	 */
	public function getContext() : IHttpContext;

	/**
	 * Gets the cookies.
	 *
	 * @return array
	 *	The cookies.
	 */
	public function getCookies() : array;

	/**
	 * Gets the files.
	 *
	 * @return array
	 *	The files.
	 */
	public function getFiles() : array;

	/**
	 * Gets the form.
	 *
	 * @return IHttpForm
	 *	The form.
	 */
	public function getForm() : ?IHttpForm;

	/**
	 * Gets the method.
	 *
	 * @return IHttpMethod
	 *	The method.
	 */
	public function getMethod() : IHttpMethod;

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string;

	/**
	 * Gets the uniform resource location.
	 *
	 * @return string
	 *	The uniform resource location.
	 */
	public function getUrl() : string;

	/**
	 * Gets the user agent.
	 *
	 * @return IHttpUserAgent
	 *	The user agent.
	 */
	public function getUserAgent() : IHttpUserAgent;

	/**
	 * Gets the user languages.
	 *
	 * @return array
	 *	The user languages.
	 */
	public function getUserLanguages() : array;
}
