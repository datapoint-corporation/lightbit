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

namespace Lightbit\Configuration;

use \Lightbit\Configuration\IConfigurationKeyNotSetException;
use \Lightbit\Configuration\IConfigurationKeyTypeException;
use \Lightbit\Data\Collections\IStringMap;

/**
 * IConfiguration.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
interface IConfiguration extends IStringMap
{
	/**
	 * Accepts a configuration by invoking the setter methods for the
	 * available properties, according to a given configuration map.
	 *
	 * @param object $subject
	 *	The configuration subject.
	 *
	 * @param array $configuration
	 *	The configuration map.
	 */
	public function accept(object $subject, array $configuration) : void;

	/**
	 * Gets a boolean.
	 *
	 * @throws IConfigurationKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IConfigurationKeyTypeException
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
	public function getBool(string $key, bool $optional = false) : ?bool;

	/**
	 * Gets a float.
	 *
	 * @throws IConfigurationKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IConfigurationKeyTypeException
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
	public function getFloat(string $key, bool $optional = false) : ?float;

	/**
	 * Gets an integer.
	 *
	 * @throws IConfigurationKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IConfigurationKeyTypeException
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
	public function getInt(string $key, bool $optional = false) : ?int;

	/**
	 * Gets a string.
	 *
	 * @throws IConfigurationKeyNotSetException
	 *	Thrown when a non optional value fails to be retrieved from this map
	 *	because the given key is not set.
	 *
	 * @throws IConfigurationKeyTypeException
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
	public function getString(string $key, bool $optional = false) : ?string;
}
