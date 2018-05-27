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

use \Lightbit\Data\Collections\IMap;

/**
 * IStringMap.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
interface IStringMap extends IMap
{
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
	public function getBool(string $key, bool $optional = false) : ?bool;

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
	public function getFloat(string $key, bool $optional = false) : ?float;

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
	public function getInt(string $key, bool $optional = false) : ?int;

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
	public function getString(string $key, bool $optional = false) : ?string;
}
