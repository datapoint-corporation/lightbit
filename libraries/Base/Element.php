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

use \Lightbit\Base\Object;

use \Lightbit\Base\IAction;
use \Lightbit\Base\IApplication;
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Base\IEnvironment;
use \Lightbit\Base\IModule;
use \Lightbit\Base\IView;
use \Lightbit\Data\ICache;
use \Lightbit\Data\IFileCache;
use \Lightbit\Data\IMemoryCache;
use \Lightbit\Data\IMessageSource;
use \Lightbit\Data\INetworkCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Globalization\ILocale;
use \Lightbit\Html\IHtmlAdapter;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpAssetManager;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;
use \Lightbit\Security\Cryptography\IPasswordDigest;

/**
 * Element.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Element extends Object implements IElement
{
	/**
	 * The context.
	 *
	 * @type IContext;
	 */
	private $context;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The element context.
	 */
	protected function __construct(?IContext $context)
	{
		$this->context = ($context ?? __context());
	}

	/**
	 * Gets the action.
	 *
	 * @return IAction
	 *	The action.
	 */
	public final function getAction() : IAction
	{
		return __action();
	}

	/**
	 * Gets the application.
	 *
	 * @return IApplication
	 *	The application.
	 */
	public final function getApplication() : IApplication
	{
		return __application();
	}

	/**
	 * Gets the cache.
	 *
	 * @return ICache
	 *	The cache.
	 */
	public function getCache() : ICache
	{
		return $this->context->getCache();
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
		return $this->context->getComponent($id);
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : IContext
	{
		return $this->context;
	}

	/**
	 * Gets the environment.
	 *
	 * @return IEnvironment
	 *	The environment.
	 */
	public function getEnvironment() : IEnvironment
	{
		return $this->context->getEnvironment();
	}

	/**
	 * Gets the file cache.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public function getFileCache() : IFileCache
	{
		return $this->context->getFileCache();
	}

	/**
	 * Gets the html adapter.
	 *
	 * @return IHtmlAdapter
	 *	The html adapter.
	 */
	public function getHtmlAdapter() : IHtmlAdapter
	{
		return $this->context->getHtmlAdapter();
	}

	/**
	 * Gets the html document.
	 *
	 * @return IHtmlDocument
	 *	The html document.
	 */
	public function getHtmlDocument() : IHtmlDocument
	{
		return $this->context->getHtmlDocument();
	}

	/**
	 * Gets the http asset manager.
	 *
	 * @return IHttpAssetManager
	 *	The http asset manager.
	 */
	public function getHttpAssetManager() : IHttpAssetManager
	{
		return $this->context->getHttpAssetManager();
	}

	/**
	 * Gets the http query string.
	 *
	 * @return IHttpQueryString
	 *	The http query string.
	 */
	public function getHttpQueryString() : IHttpQueryString
	{
		return $this->context->getHttpQueryString();
	}

	/**
	 * Gets the http request component.
	 *
	 * @param IHttpRequest
	 *	The http request component.
	 */
	public function getHttpRequest() : IHttpRequest
	{
		return $this->context->getHttpRequest();
	}

	/**
	 * Gets the http response component.
	 *
	 * @param IHttpResponse
	 *	The http response component.
	 */
	public function getHttpResponse() : IHttpResponse
	{
		return $this->context->getHttpResponse();
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter
	{
		return $this->context->getHttpRouter();
	}

	/**
	 * Gets the http session.
	 *
	 * @return IHttpSession
	 *	The http session.
	 */
	public function getHttpSession() : IHttpSession
	{
		return $this->context->getHttpSession();
	}

	/**
	 * Gets the locale.
	 *
	 * @return ILocale
	 *	The locale.
	 */
	public function getLocale() : ILocale
	{
		return $this->context->getLocale();
	}

	/**
	 * Gets the memory cache.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public function getMemoryCache() : IMemoryCache
	{
		return $this->context->getMemoryCache();
	}

	/**
	 * Gets the message source.
	 *
	 * @return IMessageSource
	 *	The message source.
	 */
	public function getMessageSource() : IMessageSource
	{
		return $this->context->getMessageSource();
	}

	/**
	 * Gets the network cache.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public function getNetworkCache() : INetworkCache
	{
		return $this->context->getNetworkCache();
	}

	/**
	 * Gets the password digest.
	 *
	 * @return IPasswordDigest
	 *	The password digest.
	 */
	public function getPasswordDigest() : IPasswordDigest
	{
		return $this->context->getPasswordDigest();
	}

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public function getSlugManager() : ISlugManager
	{
		return $this->context->getSlugManager();
	}

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection
	{
		return $this->context->getSqlConnection();
	}

	/**
	 * Gets the theme.
	 *
	 * @return ITheme
	 *	The theme.
	 */
	public function getTheme() : ?ITheme
	{
		return $this->context->getTheme();
	}

	/**
	 * Executes a transaction.
	 *
	 * @param \Closure $closure
	 *	The transaction closure.
	 *
	 * @return mixed
	 *	The transaction result.
	 */
	public function transaction(\Closure $closure) // : mixed;
	{
		return $this->getSqlConnection()->transaction($closure);
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

		do
		{
			if (method_exists($context, $method))
			{
				return $context->{$method}(...$arguments);
			}
		}
		while ($context = $context->getContext());

		return parent::__call($method, $arguments);
	}
}
