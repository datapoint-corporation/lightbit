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

use \Lightbit\Data\Filtering\FilterException;
use \Lightbit\Data\Filtering\FilterProvider;
use \Lightbit\Http\HttpQueryStringParameterNotSetException;
use \Lightbit\Http\HttpQueryStringParameterValueParseException;
use \Lightbit\Http\HttpQueryStringParameterValueTypeMismatchParseException;

use \Lightbit\Http\IHttpQueryString;

/**
 * HttpServerRequestQueryString.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpServerRequestQueryString implements IHttpQueryString
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServerRequestQueryString
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServerRequestQueryString
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServerRequestQueryString
	{
		return (self::$instance ?? (self::$instance = new HttpServerRequestQueryString()));
	}

	/**
	 * The parameter map.
	 *
	 * @var array
	 */
	private $parameterMap;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->parameterMap = $_GET ?? [];
	}

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
	public final function get(string $type, string $parameter, bool $optional = false)
	{
		if (isset($this->parameterMap[$parameter]))
		{
			if (is_string($this->parameterMap[$parameter]))
			{
				try
				{
					FilterProvider::getInstance()->getFilter($type)->parse($this->parameterMap[$parameter]);
				}
				catch (FilterParseException $e)
				{
					throw new HttpQueryStringParameterValueParseException($this, sprintf(
						'Can not get query string parameter, parsing failure: "%s"',
						$parameter
					));
				}
			}

			throw new HttpQueryStringParameterValueTypeMismatchException($this, sprintf(
				'Can not get query string parameter, value type mismatch: "%s"',
				$parameter
			));
		}

		if ($optional)
		{
			return null;
		}

		throw new HttpQueryStringParameterNotSetException($this, sprintf(
			'Can not get query string parameter, not set: "%s"',
			$parameter
		));
	}

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
	public final function getBool(string $parameter, bool $optional = false) : ?bool
	{
		return $this->get('bool', $parameter, $optional);
	}

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
	public final function getFloat(string $parameter, bool $optional = false) : ?float
	{
		return $this->get('float', $parameter, $optional);
	}

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
	public final function getInt(string $parameter, bool $optional = false) : ?int
	{
		return $this->get('int', $parameter, $optional);
	}

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
	public final function getString(string $parameter, bool $optional = false) : ?string
	{
		return $this->get('string', $parameter, $optional);
	}

	/**
	 * Converts to an array.
	 *
	 * @return array
	 *	The result.
	 */
	public final function toArray() : array
	{
		return $this->parameterMap;
	}
}
