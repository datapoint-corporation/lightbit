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

namespace Lightbit\Data\Caching\Simulation;

use \Lightbit\Data\Caching\Cache;

use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;
use \Lightbit\Data\Caching\IOpCache;

/**
 * SimulationCache.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class SimulationCache extends Cache implements IFileCache, IMemoryCache, INetworkCache, IOpCache
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Checks if a key is set.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return bool
	 *	The key status.
	 */
	public final function contains(string $key) : bool
	{
		return false;
	}

	/**
	 * Reads a value.
	 *
	 * @throws CacheReadException
	 *	Thrown if the key value is set but fails to be read because it can not
	 *	be unserialized from persistent storage.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value output variable.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function read(string $key, &$value) : bool
	{
		$value = null;
		return false;
	}

	/**
	 * Writes a value.
	 *
	 * @throws CacheWriteException
	 *	Thrown if the key value write fails because it can not be serialized
	 *	to persistent storage.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 *
	 * @param int $timeToLive
	 *	The value time to live.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function write(string $key, $value, int $timeToLive = null) : bool
	{
		return false;
	}
}
