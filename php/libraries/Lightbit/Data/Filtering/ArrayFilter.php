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
 * ArrayFilter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class ArrayFilter implements IFilter
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
		if (is_array($subject))
		{
			return json_encode(
				$this->precompose($subject),
				JSON_UNESCAPED_UNICODE
			);
		}

		throw new FilterParseException($this, sprintf(
			'Can not compose array, incompatible subject type: "%s"',
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
	 * @return mixed
	 *	The result.
	 */
	public final function parse(string $subject) : array
	{
		if ($subject)
		{
			if (in_array($subject[0], [ '[', '{' ]) && ($result = json_decode($subject)))
			{
				return $result;
			}

			return preg_split('%(\\s+)%', $subject, -1, PREG_SPLIT_NO_EMPTY);
		}

		return [];
	}

	/**
	 * Pre-composes a subject by invoking the applicable filters for any
	 * non-scalar or array values, recursively.
	 *
	 * @throws FilterComposeException
	 *	Thrown when a value is of an incompatible type or can not be
	 *	composed by this filter.
	 *
	 * @return array
	 *	The subject.
	 */
	private function precompose(array $subject) : array
	{
		foreach ($subject as $i => $value)
		{
			if (is_array($value))
			{
				$subject[$i] = $this->precompose($subject);
			}

			else if (!is_scalar($value))
			{
				$subject[$i] = FilterProvider::getInstance()->getFilter(
					lbstypeof($value)
				)

				->compose($value);
			}
		}

		return $subject;
	}

	/**
	 * Transform.
	 *
	 * @throws FilterParseException
	 *	Thrown when the subject is a string with an incompatible format which
	 *	can not be parsed by this filter.
	 *
	 * @throws FilterTransformException
	 *	Thrown when the subject is of an incompatible type which can not
	 *	be transformed by this filter.
	 *
	 * @param mixed $subject
	 *	The transformation subject.
	 *
	 * @return array
	 *	The result.
	 */
	public final function transform($subject) : array
	{
		if (is_array($subject))
		{
			return $subject;
		}

		if (is_string($subject))
		{
			return $this->parse($subject);
		}

		throw new FilterTransformException($this, sprintf(
			'Can not transform array, incompatible subject type: "%s"',
			lbstypeof($subject)
		));
	}
}
