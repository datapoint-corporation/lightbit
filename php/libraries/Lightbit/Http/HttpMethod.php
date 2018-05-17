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

use \Lightbit\ArgumentException;

/**
 * HttpMethod.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpMethod implements IHttpMethod
{
	/**
	 * Gets an instance.
	 *
	 * @param string $name
	 *	The method name.
	 *
	 * @return HttpMethod
	 *	The method.
	 */
	public static function getInstance(string $name) : HttpMethod
	{
		switch ($name)
		{
			case 'GET':
			case 'HEAD':
				return new HttpMethod($name, true, true);

			case 'DELETE':
			case 'OPTIONS':
			case 'PUT':
			case 'TRACE':
				return new HttpMethod($name, true, false);
		}

		throw new ArgumentException('name', sprintf('Can not get http method, it does not exist: "%s"', $name));
	}

	/**
	 * The name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The idempotent flag.
	 *
	 * @var bool
	 */
	private $idempotent;

	/**
	 * The safe flag.
	 *
	 * @var bool
	 */
	private $safe;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 *	The method name.
	 *
	 * @param bool $idempotent
	 *	The method idempotent flag.
	 *
	 * @param bool $safe
	 *	The method safe flag.
	 */
	public function __construct(string $name, bool $idempotent, bool $safe)
	{
		$this->name = $name;
		$this->idempotent = $idempotent;
		$this->safe = $safe;
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public final function getName() : string
	{
		return $this->name;
	}

	/**
	 * Checks the idempotent flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isIdempotent() : bool
	{
		return $this->idempotent;
	}

	/**
	 * Checks the safe flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isSafe() : bool
	{
		return $this->safe;
	}
}
