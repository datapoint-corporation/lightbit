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

use \Closure;
use \Throwable;

use \Lightbit\Configuration\ConfigurationException;

use \Lightbit\Configuration\IConfiguration;
use \Lightbit\Data\Caching\ICache;

/**
 * CacheException.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
abstract class Cache implements ICache
{
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
	abstract public function read(string $key, &$value) : bool;

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
	abstract public function write(string $key, $value, int $timeToLive = null) : bool;

	/**
	 * Constructor.
	 */
	protected function __construct()
	{

	}

	/**
	 * Configures this object by accepting any relevant properties from
	 * the given configuration.
	 *
	 * @throws ConfigurationException
	 *	Thrown if a configurable property fails to be set.
	 *
	 * @param IConfiguration $configuration
	 *	The configuration to accept from.
	 */
	public function configure(IConfiguration $configuration) : void
	{

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
	 * @return mixed
	 *	The value, if set.
	 */
	public final function fetch(string $key)
	{
		if ($this->read($key, $value))
		{
			return $value;
		}

		return null;
	}

	/**
	 * Gets a value.
	 *
	 * @throws CacheReadException
	 *	Thrown if the key value is set but fails to be read because it can not
	 *	be unserialized from persistent storage.
	 *
	 * @throws CacheWriteException
	 *	Thrown if the key value write fails because it can not be serialized
	 *	to persistent storage.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param Closure $closure
	 *	The value closure.
	 *
	 * @param int $timeToLive
	 *	The value time to live.
	 *
	 * @return mixed
	 *	The value.
	 */
	public final function invoke(string $key, Closure $closure, int $timeToLive = null)
	{
		if (!$this->read($key, $value))
		{
			$this->write($key, ($value = $closure()), $timeToLive);
		}

		return $value;
	}
}
