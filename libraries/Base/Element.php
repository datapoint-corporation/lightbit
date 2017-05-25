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

namespace Lightbit\Base;

use \Lightbit;
use \Lightbit\Base\IElement;
use \Lightbit\Base\Object;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpRouter;

/**
 * Element.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Element extends Object implements IElement
{
	/**
	 * Gets the application.
	 *
	 * @return IApplication
	 *	The application.
	 */
	public final function getApplication() : IApplication
	{
		return Lightbit::getApplication();
	}

	/**
	 * Gets the http query string.
	 *
	 * @return IHttpQueryString
	 *	The http query string.
	 */
	public final function getHttpQueryString() : IHttpQueryString
	{
		return Lightbit::getApplication()->getHttpQueryString();
	}

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public final function getHttpRequest() : IHttpRequest
	{
		return Lightbit::getApplication()->getHttpRequest();
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public final function getHttpRouter() : IHttpRouter
	{
		return Lightbit::getApplication()->getHttpRouter();
	}

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public final function getSlugManager() : ISlugManager
	{
		return Lightbit::getApplication()->getSlugManager();
	}

	/**
	 * Calls a method.
	 *
	 * @param string $method
	 *	The method name.
	 *
	 * @param array $arguments
	 *	The method arguments.
	 *
	 * @return mixed
	 *	The method result.
	 */
	public function __call(string $method, array $arguments) // : mixed
	{
		$application = Lightbit::getApplication();

		if (method_exists($application, $method))
		{
			return $application->{$method}(...$arguments);
		}

		return parent::__call($method, $arguments);
	}
}
