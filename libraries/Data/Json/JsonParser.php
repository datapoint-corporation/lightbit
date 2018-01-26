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

namespace Lightbit\Data\Json;

use \Lightbit\Data\Json\JsonParserException;

/**
 * JsonParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class JsonParser
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Parses content.
	 *
	 * @param string $content
	 *	The content to parse.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function parse(string $content)
	{
		$result = json_decode($content, true, 512, JSON_BIGINT_AS_STRING);

		if ($result === false && json_last_error() > 0)
		{
			throw new JsonParserException($this, sprintf('%s (Code %d)', json_last_error_msg(), json_last_error()));
		}

		return $result;
	}

	/**
	 * Parses content to an array.
	 *
	 * @param string $content
	 *	The content to parse.
	 *
	 * @return array
	 *	The result.
	 */
	public function toArray(string $content) : array
	{
		$result = $this->parse($content);

		if (!is_array($result))
		{
			throw new JsonParserException($this, 'Can not return json result, not an array');
		}

		return $result;
	}
}