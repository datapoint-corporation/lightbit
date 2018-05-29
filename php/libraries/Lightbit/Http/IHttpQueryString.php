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

namespace Lightbit\Http;

use \Lightbit\Data\Collections\IStringMap;
use \Lightbit\Http\HttpQueryStringParameterNotSetException;
use \Lightbit\Http\HttpQueryStringParameterValueParseException;
use \Lightbit\Http\HttpQueryStringParameterValueTypeMismatchParseException;

/**
 * IHttpQueryString.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
interface IHttpQueryString extends IStringMap
{
	/**
	 * Gets a parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter is not optional and it's value can not be
	 *	retrieved because it is not set.
	 *
	 * @throws HttpQueryStringParameterValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a boolean.
	 *
	 * @throws HttpQueryStringParameterValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $type
	 *	The parameter type.
	 *
	 * @param string $parameter
	 *	The parameter.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return bool
	 *	The parameter value.
	 */
	public function get(string $type, string $parameter, bool $optional = false);

	/**
	 * Gets a boolean.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter is not optional and it's value can not be
	 *	retrieved because it is not set.
	 *
	 * @throws HttpQueryStringParameterValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a boolean.
	 *
	 * @throws HttpQueryStringParameterValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $parameter
	 *	The parameter.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return bool
	 *	The parameter value.
	 */
	public function getBool(string $parameter, bool $optional = false) : ?bool;

	/**
	 * Gets a float.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter is not optional and it's value can not be
	 *	retrieved because it is not set.
	 *
	 * @throws HttpQueryStringParameterValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a float.
	 *
	 * @throws HttpQueryStringParameterValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $parameter
	 *	The parameter.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return float
	 *	The parameter value.
	 */
	public function getFloat(string $parameter, bool $optional = false) : ?float;

	/**
	 * Gets an integer.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter is not optional and it's value can not be
	 *	retrieved because it is not set.
	 *
	 * @throws HttpQueryStringParameterValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as an integer.
	 *
	 * @throws HttpQueryStringParameterValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $parameter
	 *	The parameter.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return int
	 *	The parameter value.
	 */
	public function getInt(string $parameter, bool $optional = false) : ?int;

	/**
	 * Gets a string.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter is not optional and it's value can not be
	 *	retrieved because it is not set.
	 *
	 * @throws HttpQueryStringParameterValueTypeMismatchParseException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $parameter
	 *	The parameter.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return int
	 *	The parameter value.
	 */
	public function getString(string $parameter, bool $optional = false) : ?string;
}
