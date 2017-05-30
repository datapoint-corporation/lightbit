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

namespace Lightbit\Http;

use \Lightbit\Base\Action;
use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Data\Validation\Filter;
use \Lightbit\Http\HttpQueryStringParameterNotFoundException;
use \Lightbit\Http\IHttpQueryString;

/**
 * HttpQueryString.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class HttpQueryString extends Component implements IHttpQueryString
{
	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, $configuration);
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
		return isset($_GET[$key]);
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
		if (isset($_GET[$key]))
		{
			return $_GET[$key];
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
		if (isset($_GET[$key]))
		{
			$result = $_GET[$key];

			if (is_array($result))
			{
				return new Map($result);
			}

			return $result;
		}

		throw new HttpQueryStringParameterNotFoundException($this, $key, sprintf('Query string parameter not found: "%s"', $key));
	}

	/**
	 * Creates an array from this map.
	 *
	 * @return array
	 *	The result.
	 */
	public function toArray() : array
	{
		return $_GET;
	}

	public function __get(string $property) // : mixed
	{
		return $this->read($property);
	}

	public function __isset(string $property) : bool
	{
		return isset($_GET[$property]);
	}
}