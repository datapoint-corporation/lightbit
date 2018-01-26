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

namespace Lightbit\Data\Conversing;

use \Transliterator;

use \Lightbit\Math\Decimal;
use \Lightbit\Data\Slugifiable;

/**
 * StringConversion.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class StringConversion
{
	/**
	 * The subject.
	 *
	 * @var mixed
	 */
	private $subject;

	/**
	 * Constructor.
	 *
	 * @param mixed $subject
	 *	The conversion subject.
	 */
	public function __construct($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * Converts to string.
	 *
	 * @return string
	 *	The result.
	 */
	public function toString() : string
	{
		switch (gettype($this->subject))
		{
			case 'string':
				return $this->subject;

			case 'boolean':
				return $this->subject ? 'true' : 'false';
		}

		if (is_int($this->subject) || is_double($this->subject))
		{
			$precision;
			$locale = localeconv();
			$subject = (string) $this->subject;
			$subject = str_replace($locale['decimal_point'], '.', $subject);
			$subject = str_replace($locale['thousands_sep'], '', $subject);
			return (new Decimal($subject))->toString();
		}

		if (is_object($this->subject))
		{
			if ($this->subject instanceof Slugifiable)
			{
				return $this->subject->slugify();
			}

			throw new StringConversionException($this, sprintf('Can not convert subject to string, unsupported type: "%s"', get_class($this->subject)));
		}

		throw new StringConversionException($this, sprintf('Can not convert subject to string, unsupported type: "%s"', gettype($this->subject)));
	}
}