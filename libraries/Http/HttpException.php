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

use \Lightbit\Exception;
use \Lightbit\Helpers\HttpHelper;

/**
 * HttpException.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpException extends Exception
{
	/**
	 * The status code.
	 *
	 * @type int
	 */
	private $statusCode;

	/**
	 * The status message.
	 *
	 * @type string
	 */
	private $statusMessage;

	/**
	 * Constructor.
	 *
	 * @param int $statusCode
	 *	The http status code.
	 *
	 * @param string $message
	 *	The exception message.
	 *
	 * @param Throwable $previous
	 *	The previous throwable.
	 */
	public function __construct(int $statusCode, string $message, \Throwable $previous = null)
	{
		parent::__construct($message, $previous);

		$this->statusCode = $statusCode;
	}

	/**
	 * Gets the http status code.
	 *
	 * @return int
	 *	The http status code.
	 */
	public final function getStatusCode() : int
	{
		return $this->statusCode;
	}

	/**
	 * Gets the http status message.
	 *
	 * @return string
	 *	The http status message.
	 */
	public final function getStatusMessage() : string
	{
		if (!$this->statusMessage)
		{
			$this->statusMessage = HttpHelper::getStatusMessage($this->statusCode);
		}

		return $this->statusMessage;
	}
}