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

namespace Lightbit\Data\Filtering;

use \Lightbit\Data\Filtering\Filter;
use \Lightbit\Data\Filtering\FilterException;

/**
 * FloatFilter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @version 1.0.0
 */
class FloatFilter extends Filter
{
	/**
	 * The unsigned flag.
	 *
	 * @type bool
	 */
	private $unsigned = false;

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The filter configuration.
	 */
	public function __construct(array $configuration = null)
	{
		parent::__construct($configuration);
	}

	/**
	 * Runs the filter.
	 *
	 * @param mixed $value
	 *	The value to run the filter on.
	 *
	 * @return float
	 *	The value.
	 */
	public function run($value) : float
	{
		while (!is_float($value))
		{
			if (is_int($value))
			{
				$value = floatval($value);
				break;
			}

			if (is_string($value))
			{
				if (preg_match('%^(\\-|\\+)?(\\d+|((\\d+)?\\.\\d+))$%', $value))
				{
					$value = floatval($value);
					break;
				}
			}

			throw new FilterException($this, sprintf('Bad filter value data type: expecting "%s", found "%s"', 'float', __type_of($value)));
		}

		if ($this->unsigned && $value < 0)
		{
			throw new FilterException($this, sprintf('Out of range value: expecting unsigned float, got signed float instead.'));
		}

		return $value;
	}

	/**
	 * Defines the unsigned flag.
	 *
	 * @param bool $unsigned
	 *	The unsigned flag value.
	 */
	public final function setUnsigned(bool $unsigned) : void
	{
		$this->unsigned = $unsigned;
	}
}
