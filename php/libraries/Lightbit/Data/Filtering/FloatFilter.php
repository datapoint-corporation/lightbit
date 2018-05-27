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

namespace Lightbit\Data\Filtering;

use \Lightbit\Data\Filtering\FilterComposeException;
use \Lightbit\Data\Filtering\FilterParseException;

use \Lightbit\Data\Filtering\IFilter;

/**
 * FloatFilter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class FloatFilter implements IFilter
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
	 * @throws FilterComposeException
	 *	Thrown when the subject is of an incompatible type or can not be
	 *	composed by this filter.
	 *
	 * @param mixed $subject
	 *	The composition subject.
	 *
	 * @return string
	 *	The result.
	 */
	public final function compose($subject) : string
	{
		if (is_float($subject) || is_int($subject))
		{
			return rtrim(rtrim(number_format($subject, 16, '.', ''), '0'), '.');
		}

		throw new FilterComposeException($this, sprintf(
			'Can not compose float, incompatible subject type: "%s"',
			lbstypeof($subject)
		));
	}

	/**
	 * Parse.
	 *
	 * @throws FilterParseException
	 *	Thrown when the subject has an incompatible format or can not be
	 *	parsed by this filter.
	 *
	 * @param string $subject
	 *	The parsing subject.
	 *
	 * @return float
	 *	The result.
	 */
	public final function parse(string $subject) : float
	{
		if (preg_match('%^(\\-|\\+)?((\\d+(\\.\\d+))|(\\.\\d+))$%', $subject))
		{
			return floatval($subject);
		}

		throw new FilterParseException($this, sprintf(
			'Can not parse float, bad subject format: "%s"',
			$subject
		));
	}
}
