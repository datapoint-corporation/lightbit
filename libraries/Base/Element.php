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
use \Lightbit\Base\Action;
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IElement;
use \Lightbit\Data\ICache;
use \Lightbit\Data\IFileCache;
use \Lightbit\Data\IMemoryCache;
use \Lightbit\Data\INetworkCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Html\IHtmlAdapter;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;

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
	 * Gets the cache.
	 *
	 * @return ICache
	 *	The cache.
	 */
	public function getCache() : ICache
	{
		return $this->getContext()->getCache();
	}

	/**
	 * Gets a component.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return IComponent
	 *	The component.
	 */
	public final function getComponent(string $id) : IComponent
	{
		return $this->getContext()->getComponent($id);
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext
	{
		$action = Action::getInstance();

		if ($action)
		{
			return $action->getController()->getContext();
		}

		return Lightbit::getApplication();
	}

	/**
	 * Gets the file cache.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public function getFileCache() : IFileCache
	{
		return $this->getContext()->getFileCache();
	}

	/**
	 * Gets the html adapter.
	 *
	 * @return IHtmlAdapter
	 *	The html adapter.
	 */
	public function getHtmlAdapter() : IHtmlAdapter
	{
		return $this->getContext()->getHtmlAdapter();
	}

	/**
	 * Gets the html document.
	 *
	 * @return IHtmlDocument
	 *	The html document.
	 */
	public function getHtmlDocument() : IHtmlDocument
	{
		return $this->getContext()->getHtmlDocument();
	}

	/**
	 * Gets the http query string.
	 *
	 * @return IHttpQueryString
	 *	The http query string.
	 */
	public function getHttpQueryString() : IHttpQueryString
	{
		return $this->getContext()->getHttpQueryString();
	}

	/**
	 * Gets the http request component.
	 *
	 * @param IHttpRequest
	 *	The http request component.
	 */
	public function getHttpRequest() : IHttpRequest
	{
		return $this->getContext()->getHttpRequest();
	}

	/**
	 * Gets the http response component.
	 *
	 * @param IHttpResponse
	 *	The http response component.
	 */
	public function getHttpResponse() : IHttpResponse
	{
		return $this->getContext()->getHttpResponse();
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter
	{
		return $this->getContext()->getHttpRouter();
	}

	/**
	 * Gets the http session.
	 *
	 * @return IHttpSession
	 *	The http session.
	 */
	public function getHttpSession() : IHttpSession
	{
		return $this->getContext()->getHttpSession();
	}

	/**
	 * Gets the memory cache.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public function getMemoryCache() : IMemoryCache
	{
		return $this->getContext()->getMemoryCache();
	}

	/**
	 * Gets the network cache.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public function getNetworkCache() : INetworkCache
	{
		return $this->getContext()->getNetworkCache();
	}

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public function getSlugManager() : ISlugManager
	{
		return $this->getContext()->getSlugManager();
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
		$context = $this->getContext();

		if (method_exists($context, $method))
		{
			return $context->{$method}(...$arguments);
		}

		if (!($context instanceof IApplication))
		{
			$context = $this->getApplication();

			if (method_exists($context, $method))
			{
				return $context->{$method}(...$arguments);
			}
		}

		return parent::__call($method, $arguments);
	}
}
