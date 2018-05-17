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

namespace Lightbit\Data\Parsing;

use \Lightbit\ArgumentException;
use \Lightbit\Data\Parsing\IParser;
use \Lightbit\Data\Parsing\ParserException;

/**
 * FloatParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class FloatParser implements IParser
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Compose.
	 *
	 * @param mixed $subject
	 *	The composition subject.
	 *
	 * @return string
	 *	The result.
	 */
	public function compose($subject) : string
	{
		if (is_int($subject) || is_float($subject))
		{
			return rtrim(rtrim(number_format($subject, 16, '.', ''), '0'), '.');
		}

		throw new ParserException(
			$this,
			sprintf('Can not compose boolean, wrong argument type: "%s"', gettype($subject)),
			new ArgumentException('subject', sprintf('Can not accept argument, bad type: "%s", of type "%s"', 'subject', gettype($subject)))
		);
	}

	/**
	 * Parse.
	 *
	 * @param string $subject
	 *	The parsing subject.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function parse(string $subject) : string
	{
		if (preg_match('%^(\\-|\\+)?((\\d+(\\.\\d+)?)|(.\\d+))$%', $subject))
		{
			return floatval($subject);
		}

		throw new ParserException($this, sprintf('Can not parse float, wrong format: "%s"', $subject));
	}
}
