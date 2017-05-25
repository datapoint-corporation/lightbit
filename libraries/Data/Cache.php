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

namespace Lightbit\Data;

use \Lightbit\Base\Component;
use \Lightbit\Data\ICache;

/**
 * Cache.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Cache extends Component implements ICache
{
	/**
	 * Checks for a value availability.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return bool
	 *	The check result.
	 */
	abstract public function contains($key) : bool;

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
	abstract public function fetch($key, $default = null); // : mixed

	/**
	 * Reads a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	abstract public function read($key); // : mixed
	
	/**
	 * Removes a value.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	abstract public function remove($key); // : mixed
	
	/**
	 * Creates an array from this map.
	 *
	 * @return array
	 *	The result.
	 */
	abstract public function toArray() : array;

	/**
	 * Writes a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 */
	abstract public function write($key, $value) : void;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $id, array $configuration = null)
	{
		parent::__construct($id, $configuration);
	}
}