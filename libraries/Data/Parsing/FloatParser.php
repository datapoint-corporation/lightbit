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

use \Lightbit\Data\Parsing\Parser;
use \Lightbit\Data\Parsing\ParserException;

/**
 * FloatParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class FloatParser extends Parser
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Composes a subject.
	 *
	 * @param mixed $subject.
	 *	The subject.
	 *
	 * @return string
	 *	The result.
	 */
	public function compose($subject) : string
	{
		if (is_string($subject))
		{
			$subject = $this->parse($subject);
		}

		if (is_int($subject))
		{
			$subject = floatval($subject);
		}

		else 
		{
			throw new ParserException($this, sprintf('Can not compose boolean, unsupported subject data type: "%s"', gettype($subject)));
		}

		$precision;
		$subject = (string) $subject;

		if (($i = strpos($subject, '.')) !== false)
		{
			$precision = strlen(substr(rtrim($subject, '0'), $i + 1));
		}
		else
		{
			$precision = 0;
		}

		return number_format($subject, $precision, '.', '');
	}

	/**
	 * Parses a subject.
	 *
	 * @param mixed $subject.
	 *	The subject.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function parse($subject)
	{
		if (is_int($subject))
		{
			return (double) $subject; 
		}

		if (is_double($subject))
		{
			return $subject;
		}

		if (is_string($subject))
		{
			if (!preg_match('%^\\-?\\d+(\\.\\d+)?$%', $subject))
			{
				throw new ParserException($this, sprintf('Can not parse float, illegal format: "%s"', $subject));
			}

			return floatval($subject);
		}

		throw new ParserException($this, sprintf('Can not parse float, unsupported subject data type: "%s"', gettype($subject)));
	}
}