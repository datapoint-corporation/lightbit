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
use \Lightbit\Data\Parsing\ParserProvider;
use \Lightbit\Http\IHttpQueryString;

/**
 * HttpServerRequestQueryString.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpServerRequestQueryString implements IHttpQueryString, IStringMap
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
	 * The parameters.
	 *
	 * @var array
	 */
	private $parameters;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->parameters = $_GET ?? [];
	}

	/**
	 * Gets a parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter fails to be retrieved because it is
	 *	not set in this query string.
	 *
	 * @throws HttpQueryStringParameterParseException
	 *	Thrown when the parameter fails to be retrieved because it failed
	 *	parsing according to the applicable type.
	 *
	 * @param string $type
	 *	The parameter type.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return mixed
	 *	The parameter value.
	 */
	public final function get(string $type, string $parameter, bool $optional = false)
	{
		try
		{
			if (isset($this->parameters[$parameter]) && is_string($this->parameters[$parameter]) && $this->parameters[$parameter])
			{
				return ParserProvider::getInstance()->getParser($type)->parse($this->parameters[$parameter]);
			}
		}
		catch (ParserException $e)
		{
			throw new HttpQueryStringParameterParseException($this, sprintf('Can not get query string parameter, parsing failure: "%s", expecting "%s"', $parameter, $type));
		}

		if ($optional)
		{
			return null;
		}

		throw new HttpQueryStringParameterNotSetException($this, sprintf('Can not get query string parameter, not set: "%s"', $parameter));
	}

	/**
	 * Gets a boolean parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter fails to be retrieved because it is
	 *	not set in this query string.
	 *
	 * @throws HttpQueryStringParameterParseException
	 *	Thrown when the parameter fails to be retrieved because it failed
	 *	parsing according to the applicable type.
	 *
	 * @param string $parameter
	 *	The parameter name.
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
	 * Gets a float parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter fails to be retrieved because it is
	 *	not set in this query string.
	 *
	 * @throws HttpQueryStringParameterParseException
	 *	Thrown when the parameter fails to be retrieved because it failed
	 *	parsing according to the applicable type.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return float
	 *	The parameter value.
	 */
	public final function getFloat(string $parameter, bool $optional = false) : ?float
	{
		return $this->get('bool', $parameter, $optional);
	}

	/**
	 * Gets an integer parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter fails to be retrieved because it is
	 *	not set in this query string.
	 *
	 * @throws HttpQueryStringParameterParseException
	 *	Thrown when the parameter fails to be retrieved because it failed
	 *	parsing according to the applicable type.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return int
	 *	The parameter value.
	 */
	public final function getInt(string $parameter, bool $optional = false) : ?int
	{
		return $this->get('bool', $parameter, $optional);
	}

	/**
	 * Gets a string parameter.
	 *
	 * @throws HttpQueryStringParameterNotSetException
	 *	Thrown when the parameter fails to be retrieved because it is
	 *	not set in this query string.
	 *
	 * @throws HttpQueryStringParameterParseException
	 *	Thrown when the parameter fails to be retrieved because it failed
	 *	parsing according to the applicable type.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param bool $optional
	 *	The parameter optional flag.
	 *
	 * @return string
	 *	The parameter value.
	 */
	public final function getString(string $parameter, bool $optional = false) : ?string
	{
		return $this->get('bool', $parameter, $optional);
	}

	/**
	 * Gets an array representation of this query string.
	 *
	 * @return array
	 *	The array representation of this query string.
	 */
	public final function toArray() : array
	{
		return $this->parameters;
	}
}
