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

namespace Lightbit\Data\Caching;

use \Lightbit\Data\Caching\Op\OpCache;

use \Lightbit\Data\Caching\ICacheFactory;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;

/**
 * CacheProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class CacheProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var CacheProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return CacheProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : CacheProvider
	{
		return (self::$instance ?? (self::$instance = new CacheProvider()));
	}

	/**
	 * The file cache.
	 *
	 * @var IFileCache
	 */
	private $fileCache;

	/**
	 * The memory cache.
	 *
	 * @var IMemoryCache
	 */
	private $memoryCache;

	/**
	 * The network cache.
	 *
	 * @var INetworkCache
	 */
	private $networkCache;

	/**
	 * The opcache cache.
	 *
	 * @var OpCache
	 */
	private $opCache;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the cache factory.
	 *
	 * @return ICacheFactory
	 *	The cache factory.
	 */
	public final function getCacheFactory() : ICacheFactory
	{
		return ($this->cacheFactory ?? ($this->cacheFactory = new CacheFactory()));
	}

	/**
	 * Gets the opcode cache.
	 *
	 * @return OpCache
	 *	The opcode cache.
	 */
	public final function getOpCache() : OpCache
	{
		return ($this->opCache ?? ($this->opCache = new OpCache()));
	}

	/**
	 * Gets the file cache.
	 *
	 * @throws CacheFactoryException
	 *	Thrown if the cache fails to be created.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public final function getFileCache() : IFileCache
	{
		return ($this->fileCache ?? ($this->fileCache = $this->getCacheFactory()->createFileCache()));
	}

	/**
	 * Gets the memory cache.
	 *
	 * @throws CacheFactoryException
	 *	Thrown if the cache fails to be created.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public final function getMemoryCache() : IMemoryCache
	{
		return ($this->memoryCache ?? ($this->memoryCache = $this->getCacheFactory()->createMemoryCache()));
	}

	/**
	 * Gets the network cache.
	 *
	 * @throws CacheFactoryException
	 *	Thrown if the cache fails to be created.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public final function getNetworkCache() : INetworkCache
	{
		return ($this->networkCache ?? ($this->networkCache = $this->getCacheFactory()->createNetworkCache()));
	}

	/**
	 * Sets the cache factory.
	 *
	 * @param ICacheFactory $cacheFactory
	 *	The cache factory.
	 */
	public final function setCacheFactory(ICacheFactory $cacheFactory) : void
	{
		$this->cacheFactory = $cacheFactory;
	}
}
