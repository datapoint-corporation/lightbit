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
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $id, array $configuration = null)
	{
		parent::__construct($id, $configuration);
	}

	/**
	 * Gets the headers.
	 *
	 * @return array
	 *	The headers.
	 */
	public final function getHeaders() : array
	{
		static $headers;

		if (!isset($headers))
		{
			$headers = [];

			foreach ($_SERVER as $key => $value)
			{
				if (strpos($key, 'HTTP_') === 0)
				{
					$headers[strtr(ucwords(strtolower(strtr(substr($key, 5), [ '_' => ' ' ]))), [ ' ' => '-' ])]
						= (is_array($value) ? $value : [ $value ]);					

				}
			}
		}

		return $headers;
	}

	/**
	 * Gets a header.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return string
	 *	The header content.
	 */
	public final function getHeader(string $header) : ?string
	{
		foreach ($this->getHeaders() as $candidate => $content)
		{
			if (strcasecmp($header, $candidate) === 0)
			{
				if (isset($content[0]))
				{
					return $content[0];
				}

				break;
			}
		}

		return null;
	}

	/**
	 * Gets a header collection.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return string
	 *	The header collection.
	 */
	public final function getHeaderCollection(string $header) : array
	{
		foreach ($this->getHeaders() as $candidate => $content)
		{
			if (strcasecmp($header, $candidate) === 0)
			{
				return $content;
			}
		}

		return [];
	}
}