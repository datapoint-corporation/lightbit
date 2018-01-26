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
 * IntegerParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class IntegerParser extends Parser
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
			return number_format($this->parse($subject), 0, '', '');
		}

		else if (is_int($subject))
		{
			return number_format($subject, 0, '', '');
		}

		throw new ParserException($this, sprintf('Can not compose integer, unsupported subject data type: "%s"', gettype($subject)));
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
			return $subject; 
		}

		if (is_string($subject))
		{
			if (!preg_match('%^\\-?\\d+$%', $subject))
			{
				throw new ParserException($this, sprintf('Can not parse float, illegal format: "%s"', $subject));
			}

			return intval($subject);
		}

		throw new ParserException($this, sprintf('Can not parse float, unsupported subject data type: "%s"', gettype($subject)));
	}
}