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

use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Http\IHttpRequest;

/**
 * HttpRequest.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRequest extends Component implements IHttpRequest
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
	 * Gets the headers collection.
	 *
	 * @return array
	 *	The headers collection.
	 */
	public final function getHeadersCollection() : array
	{
		static $headersCollection;

		if (!isset($headersCollection))
		{
			$headersCollection = [];

			foreach ($_SERVER as $attribute => $value)
			{
				if (strpos($attribute, 'HTTP_') === 0)
				{
					$headersCollection[strtr(ucwords(strtolower(strtr(substr($attribute, 5), [ '_' => ' ' ]))), [ ' ' => '-' ])]
						= is_array($value)
						? $value
						: [ $value ];
				}
			}
		}

		return $headersCollection;
	}

	/**
	 * Gets an header.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return string
	 *	The header content.
	 */
	public final function getHeader(string $header) : ?string
	{
		$collection = $this->getHeadersCollection();

		if (isset($collection[$header]))
		{
			return $collection[$header][0];
		}

		return null;
	}

	/**
	 * Gets an header collection.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return array
	 *	The header collection.
	 */
	public final function getHeaderCollection(string $header) : array
	{
		$collection = $this->getHeadersCollection();

		if (isset($collection[$header]))
		{
			return $collection[$header];
		}

		return [];
	}

	/**
	 * Exports to a model.
	 *
	 * @param IModel $model
	 *	The model to export to.
	 */
	public final function export(IModel $model) : void
	{

	}

	/**
	 * Checks if an header is defined.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasHeader(string $header) : bool
	{
		return isset(($collection = $this->getHeadersCollection())[$header]);
	}

	/**
	 * Checks the request method for a match.
	 *
	 * @param string $method
	 *	The method to match against.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isOfMethod(string $method) : bool
	{
		return $_SERVER['REQUEST_METHOD'] === $method;
	}
}