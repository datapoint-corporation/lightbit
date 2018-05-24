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

use \Lightbit\Data\Parsing\IParser;
use \Lightbit\Data\Parsing\ParserCompositionException;
use \Lightbit\Data\Parsing\ParserException;
use \Lightbit\Data\Parsing\ParserProvider;

/**
 * BooleanParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class BooleanParser implements IParser
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
	 * @throws ParserCompositionException
	 *	Thrown when the subject composition fails, containing the reason as
	 *	a previous throwable, if applicable.
	 *
	 * @param mixed $subject
	 *	The composition subject.
	 *
	 * @return string
	 *	The composition result.
	 */
	public final function compose($subject) : string
	{
		if (!is_bool($subject))
		{
			throw new ParserCompositionException($this, sprintf('Can not compose integer, bad subject type: "%s"', lbstypeof($subject)));
		}

		return ($subject ? 'true' : 'false');
	}

	/**
	 * Parses a subject.
	 *
	 * @throws ParserException
	 *	Thrown when the subject composition fails, containing the reason as
	 *	a previous throwable, if applicable.
	 *
	 * @throws ParserCompositionException
	 *	Thrown when the subject composition fails, containing the reason as
	 *	a previous throwable, if applicable.
	 *
	 * @param string $subject
	 *	The parsing subject.
	 *
	 * @return bool
	 *	The parsing result, on success.
	 */
	public final function parse(string $subject) : bool
	{
		if (preg_match('%^(true|false)$%', $subject))
		{
			return ($subject === 'true');
		}

		throw new ParserException($this, sprintf('Can not parse boolean, bad subject format: "%s"', $subject));
	}
}
