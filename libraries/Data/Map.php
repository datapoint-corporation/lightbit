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

use \Lightbit\Base\Object;
use \Lightbit\Data\MapValueNotFoundException;

/**
 * Map.
 *
 * This class contains a base implementation for a basic data map,
 * from keys to values, for read and write purposes.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Map extends Object implements IMap
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
	 * @param array $content
	 *	The map initial content.
	 */
	public function __construct(array $content = null)
	{
		$this->content = (isset($content) ? $content : []);
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
		if (isset($this->content[$key]))
		{
			return $this->content[$key];
		}

		return $default;
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
		if (isset($this->content[$key]))
		{
			$result = $this->content[$key];

			if (is_array($result))
			{
				return new Map($result);
			}

			return $result;
		}

		throw new MapValueNotFoundException($this, $key, sprintf('Map value not found: "%s"', $key));
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
		}

		unset($this->content[$key]);
		return $result;
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

	public function __get(string $property) // : mixed
	{
		return $this->read($property);
	}

	public function __isset(string $property) : bool
	{
		return isset($this->content[$property]);
	}

	public function __set(string $property, $value)  : void
	{
		$this->content[$property] = $value;
	}
}
