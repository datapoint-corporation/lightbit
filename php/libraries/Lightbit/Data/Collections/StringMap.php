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

namespace Lightbit\Data\Collections;

use \Lightbit\Data\Collections\IStringMap;
use \Lightbit\Data\Parsing\ParserException;
use \Lightbit\Data\Parsing\ParserProvider;

/**
 * StringMap.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class StringMap implements IStringMap
{
	/**
	 * The parser provider.
	 *
	 * @var IParserProvider
	 */
	private $parserProvider;

	/**
	 * The values.
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Constructor.
	 *
	 * @param array $values
	 *	The string map values.
	 */
	public function __construct(array $values = null)
	{
		$this->values = $values ?? [];
	}

	/**
	 * Gets a boolean.
	 *
	 * @throws IStringMapKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IStringMapKeyTypeException
	 *	Thrown when a value fails to be retrieved from this map because it
	 *	does not match and can not be converted to the expected type.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param bool $optional
	 *	The value optional flag.
	 *
	 * @return bool
	 *	The value.
	 */
	public final function getBool(string $key, bool $optional = false) : ?bool
	{
		if (isset($this->values[$key]))
		{
			if (is_bool($this->values[$key]))
			{
				return $this->values[$key];
			}

			if (is_string($this->values[$key]))
			{
				try
				{
					return ParserProvider::getInstance()->getParser('bool')->parse($this->values[$key]);
				}

				catch (ParserException $e) {}
			}

			throw new StringMapKeyTypeException($this, sprintf('Can not get string map key value, it is not a boolean: "%s"', $key));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf('Can not get string map key value, it is not set: "%s"', $key));
	}

	/**
	 * Gets a float.
	 *
	 * @throws IStringMapKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IStringMapKeyTypeException
	 *	Thrown when a value fails to be retrieved from this map because it
	 *	does not match and can not be converted to the expected type.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param bool $optional
	 *	The value optional flag.
	 *
	 * @return float
	 *	The value.
	 */
	public final function getFloat(string $key, bool $optional = false) : ?float
	{
		if (isset($this->values[$key]))
		{
			if (is_float($this->values[$key]) || is_int($this->values[$key]))
			{
				return $this->values[$key];
			}

			if (is_string($this->values[$key]))
			{
				try
				{
					return ParserProvider::getInstance()->getParser('float')->parse($this->values[$key]);
				}

				catch (ParserException $e) {}
			}

			throw new StringMapKeyTypeException($this, sprintf('Can not get string map key value, it is not a float: "%s"', $key));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf('Can not get string map key value, it is not set: "%s"', $key));
	}

	/**
	 * Gets an integer.
	 *
	 * @throws IStringMapKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IStringMapKeyTypeException
	 *	Thrown when a value fails to be retrieved from this map because it
	 *	does not match and can not be converted to the expected type.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param bool $optional
	 *	The value optional flag.
	 *
	 * @return int
	 *	The value.
	 */
	public final function getInt(string $key, bool $optional = false) : ?int
	{
		if (isset($this->values[$key]))
		{
			if (is_int($this->values[$key]))
			{
				return $this->values[$key];
			}

			if (is_string($this->values[$key]))
			{
				try
				{
					return ParserProvider::getInstance()->getParser('int')->parse($this->values[$key]);
				}

				catch (ParserException $e) {}
			}

			throw new StringMapKeyTypeException($this, sprintf('Can not get string map key value, it is not an integer: "%s"', $key));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf('Can not get string map key value, it is not set: "%s"', $key));
	}

	/**
	 * Gets a string.
	 *
	 * @throws IStringMapKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IStringMapKeyTypeException
	 *	Thrown when a value fails to be retrieved from this map because it
	 *	does not match and can not be converted to the expected type.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param bool $optional
	 *	The value optional flag.
	 *
	 * @return string
	 *	The value.
	 */
	public final function getString(string $key, bool $optional = false) : ?string
	{
		if (isset($this->values[$key]))
		{
			if (is_string($this->values[$key]))
			{
				return $this->values[$key];
			}

			throw new StringMapKeyTypeException($this, sprintf('Can not get string map key value, it is not a boolean: "%s"', $key));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf('Can not get string map key value, it is not set: "%s"', $key));
	}

	/**
	 * Converts to an associative array.
	 *
	 * @return array
	 *	The associative array.
	 */
	public final function toArray() : array
	{
		return $this->values;
	}
}
