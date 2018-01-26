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

namespace Lightbit\Base;

use \Lightbit\Base\IComponent;
use \Lightbit\Base\IContext;
use \Lightbit\Html\IHtmlComposer;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\I18n\ILocale;
use \Lightbit\I18n\ILocaleManager;
use \Lightbit\I18n\IMessageSource;

/**
 * IElement.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IElement
{
	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext;

	/**
	 * Gets a component.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return IComponent
	 *	The component.
	 */
	public function getComponent(string $id) : IComponent;

	/**
	 * Gets the html composer.
	 *
	 * @return IHtmlComposer
	 *	The html composer.
	 */
	public function getHtmlComposer() : IHtmlComposer;

	/**
	 * Gets the html document.
	 *
	 * @return IHtmlDocument
	 *	The html document.
	 */
	public function getHtmlDocument() : IHtmlDocument;

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public function getHttpRequest() : IHttpRequest;

	/**
	 * Gets the http response.
	 *
	 * @return IHttpResponse
	 *	The http response.
	 */
	public function getHttpResponse() : IHttpResponse;

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter;

	/**
	 * Gets the locale.
	 *
	 * @return ILocale
	 *	The locale.
	 */
	public function getLocale() : ILocale;

	/**
	 * Gets the message source.
	 *
	 * @return IMessageSource
	 *	The message source.
	 */
	public function getMessageSource() : IMessageSource;
}