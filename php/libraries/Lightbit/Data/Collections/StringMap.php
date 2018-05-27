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

use \Lightbit\Data\Collections\StringMapKeyNotSetException;
use \Lightbit\Data\Collections\StringMapKeyValueParseException;
use \Lightbit\Data\Collections\StringMapKeyValueTypeMismatchException;
use \Lightbit\Data\Filtering\FilterParseException;
use \Lightbit\Data\Filtering\FilterProvider;

use \Lightbit\Data\Collections\IStringMap;

/**
 * StringMap.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class StringMap implements IStringMap
{
	/**
	 * The values.
	 *
	 * @var array
	 */
	private $valuesMap;

	/**
	 * Constructor.
	 *
	 * @param array $valuesMap
	 *	The string map values map.
	 */
	public function __construct(array $valuesMap = null)
	{
		$this->valuesMap = $valuesMap ?? [];
	}

	/**
	 * Gets a boolean.
	 *
	 * @throws StringMapKeyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws StringMapKeyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a boolean.
	 *
	 * @throws StringMapKeyValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @param bool $optional
	 *	The key optional flag.
	 *
	 * @return bool
	 *	The key value.
	 */
	public final function getBool(string $key, bool $optional = false) : ?bool
	{
		if (isset($this->valuesMap[$key]))
		{
			if (is_bool($this->valuesMap[$key]))
			{
				return $this->valuesMap[$key];
			}

			if (is_string($this->valuesMap[$key]))
			{
				try
				{
					return FilterProvider::getInstance()->getFilter('bool')->parse($this->valuesMap[$key]);
				}
				catch (FilterParseException $e)
				{
					throw new StringMapKeyValueParseException(
						$this,
						sprintf(
							'Can not get string map key value, parsing failure: "%s"', $key
						),
						$e
					);
				}
			}

			throw new StringMapKeyValueTypeMismatchException($this, sprintf(
				'Can not get string map key value, type mismatch: "%s"',
				$key
			));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf(
			'Can not get string map key value, not set: "%s"',
			$key
		));
	}

	/**
	 * Gets a float.
	 *
	 * @throws StringMapKeyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws StringMapKeyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a float.
	 *
	 * @throws StringMapKeyValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @param bool $optional
	 *	The key optional flag.
	 *
	 * @return float
	 *	The key value.
	 */
	public final function getFloat(string $key, bool $optional = false) : ?float
	{
		if (isset($this->valuesMap[$key]))
		{
			if (is_float($this->valuesMap[$key]) || is_int($this->valuesMap[$key]))
			{
				return $this->valuesMap[$key];
			}

			if (is_string($this->valuesMap[$key]))
			{
				try
				{
					return FilterProvider::getInstance()->getFilter('float')->parse($this->valuesMap[$key]);
				}
				catch (FilterParseException $e)
				{
					throw new StringMapKeyValueParseException(
						$this,
						sprintf(
							'Can not get string map key value, parsing failure: "%s"', $key
						),
						$e
					);
				}
			}

			throw new StringMapKeyValueTypeMismatchException($this, sprintf(
				'Can not get string map key value, type mismatch: "%s"',
				$key
			));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf(
			'Can not get string map key value, not set: "%s"',
			$key
		));
	}

	/**
	 * Gets an integer.
	 *
	 * @throws StringMapKeyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws StringMapKeyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as an integer.
	 *
	 * @throws StringMapKeyValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @param bool $optional
	 *	The key optional flag.
	 *
	 * @return int
	 *	The key value.
	 */
	public final function getInt(string $key, bool $optional = false) : ?int
	{
		if (isset($this->valuesMap[$key]))
		{
			if (is_int($this->valuesMap[$key]))
			{
				return $this->valuesMap[$key];
			}

			if (is_string($this->valuesMap[$key]))
			{
				try
				{
					return FilterProvider::getInstance()->getFilter('int')->parse($this->valuesMap[$key]);
				}
				catch (FilterParseException $e)
				{
					throw new StringMapKeyValueParseException(
						$this,
						sprintf(
							'Can not get string map key value, parsing failure: "%s"', $key
						),
						$e
					);
				}
			}

			throw new StringMapKeyValueTypeMismatchException($this, sprintf(
				'Can not get string map key value, type mismatch: "%s"',
				$key
			));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf(
			'Can not get string map key value, not set: "%s"',
			$key
		));
	}

	/**
	 * Gets a string.
	 *
	 * @throws StringMapKeyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws StringMapKeyValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @param bool $optional
	 *	The key optional flag.
	 *
	 * @return int
	 *	The key value.
	 */
	public final function getString(string $key, bool $optional = false) : ?string
	{
		if (isset($this->valuesMap[$key]))
		{
			if (is_string($this->valuesMap[$key]))
			{
				return $this->valuesMap[$key];
			}

			throw new StringMapKeyValueTypeMismatchException($this, sprintf(
				'Can not get string map key value, type mismatch: "%s"',
				$key
			));
		}

		if ($optional)
		{
			return null;
		}

		throw new StringMapKeyNotSetException($this, sprintf(
			'Can not get string map key value, not set: "%s"',
			$key
		));
	}

	/**
	 * Converts to an array.
	 *
	 * @return array
	 *	The result.
	 */
	public final function toArray() : array
	{
		return $this->valuesMap;
	}
}
