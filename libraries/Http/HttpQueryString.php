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
use \Lightbit\Http\HttpStatusException;
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
	 * Checks if a attribute is set.
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return bool
	 *	The result.
	 */
	public function has(string $property) : bool
	{
		return isset($_GET[$property]);
	}

	/**
	 * Gets a attribute.
	 *
	 * @param string $type
	 *	The property data type (e.g.: '?string').
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public function get(?string $type, string $property) // : mixed
	{
		try
		{
			return __type_filter($type, (isset($_GET[$property]) ? $_GET[$property] : null));
		}
		catch (\Throwable $e)
		{
			throw new HttpStatusException
			(
				400,
				sprintf('Can not get query string attribute: property %s', $property),
				$e
			);
		}
	}

	/**
	 * Gets the query string attributes.
	 *
	 * @return array
	 *	The query string attributes.
	 */
	public function toArray() : array
	{
		return $_GET;
	}
}