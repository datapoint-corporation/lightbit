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

namespace Lightbit\Data\Caching;

use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Base\IChannel;
use \Lightbit\Data\Caching\CacheException;
use \Lightbit\Data\Caching\ICache;

/**
 * NoCache.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class NoCache extends Component implements ICache, IFileCache, IMemoryCache, INetworkCache, IChannel
{
	/**
	 * The content.
	 *
	 * @type array
	 */
	private $content;

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(Context $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, $configuration);
	}

	/**
	 * Closes the resource.
	 */
	public function close() : void
	{
		$this->content = null;
	}

	/**
	 * Checks for a value availability.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return bool
	 *	The check result.
	 */
	public function contains($key) : bool
	{
		return isset($this->content[$key]);
	}

	/**
	 * Attempts to read a value and, if not set, the default value
	 * is returned instead.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @param mixed $default
	 *	The default value.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function fetch($key, $default = null) // : mixed
	{
		return (!isset($this->content[$key]) && !array_key_exists($key, $this->content))
			? $this->content[$key]
			: $default;
	}

	/**
	 * Checks the resource status.
	 *
	 * @return bool
	 *	The resource status.
	 */
	public function isClosed() : bool
	{
		return !isset($this->content);
	}

	/**
	 * Reads a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function read($key) // : mixed
	{
		if ($this->isClosed())
		{
			throw new CacheException($this, sprintf('Bad cache resource status: resource is closed'));
		}

		if (!isset($this->content[$key]) && !array_key_exists($key, $this->content))
		{
			throw new KeyNotFoundCacheException($this, sprintf('Cache key not found: "%s"', $key));
		}

		return $this->content[$key];
	}
	
	/**
	 * Removes a value.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function remove($key) // : mixed
	{
		$result = null;

		if (isset($this->content[$key]))
		{
			$result = $this->content[$key];
			unset($this->content[$key]);
		}

		return $result;
	}

	/**
	 * Starts the resource.
	 */
	public function start() : void
	{
		$this->content = [];
	}
	
	/**
	 * Creates an array from this map.
	 *
	 * @return array
	 *	The result.
	 */
	public function toArray() : array
	{
		return $this->content;
	}

	/**
	 * Writes a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 */
	public function write($key, $value) : void
	{
		$this->content[$key] = $value;
	}
}