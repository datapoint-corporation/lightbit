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
use \Lightbit\Base\Context;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Http\IHttpResponse;

/**
 * HttpResponse.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpResponse extends Component implements IHttpResponse
{
	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(Context $context, string $id, array $configuration = null)
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
		$headersCollection = [];

		foreach (headers_list() as $header => $content)
		{
			$headersCollection[strtr(ucwords(strtolower(strtr($attribute, [ '_' => ' ', '-' => ' ' ]))), [ ' ' => '-' ])]
				= is_array($content)
				? $content
				: [ $content ];
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
	 * Clears the active output buffers, headers and status code.
	 *
	 * If content has already been sent and, as such, the response can not
	 * be reset, an exception is thrown.
	 */
	public function reset() : void
	{
		if (headers_sent() || ob_get_level() < 1)
		{
			throw new Exception('Can not reset HTTP response.');
		}

		header_remove();
		http_response_code(200);

		while (ob_get_level() > 1)
		{
			ob_end_clean();
		}

		ob_clean();
	}

	/**
	 * Sets an header content.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param string $content
	 *	The header content.
	 *
	 * @param bool $replace
	 *	The header replace flag.
	 */
	public final function setHeader(string $header, string $content, bool $replace = true) : void
	{
		header((strtr($header, [ ':' => '' ]) . ': ' . strtr($content, [ "\n" => '', "\r" => '' ])), $replace);
	}

	/**
	 * Sets the status code.
	 *
	 * @param int $statusCode
	 *	The status code.
	 */
	public final function setStatusCode(int $statusCode) : void
	{
		http_response_code($statusCode);
	}
}