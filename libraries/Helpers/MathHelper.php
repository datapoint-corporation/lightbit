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

namespace Lightbit\Helpers;

/**
 * MathHelper.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MathHelper
{
	/**
	 * Rounds a number up.
	 *
	 * @param string $number
	 *	The number.
	 *
	 * @param int $precision
	 *	The precision.
	 *
	 * @return string
	 *	The result.
	 */
	public static function ceil(string $number, int $precision = 0) : string
	{
		// Multiply the value according to the precision
		$multiplier = bcpow('10', $precision, LIGHTBIT_PRECISION);
		$number = bcmul($number, $multiplier, LIGHTBIT_PRECISION);
		
		// Make the necessary changes to the integral part
		list ($integral, $fractional) = explode('.', $number);

		if (bccomp('0', $fractional, LIGHTBIT_PRECISION))
		{
			$number = bcadd($integral, '1', LIGHTBIT_PRECISION);
		}
		
		// Divide it back and format the number for return
		$number = bcdiv($number, $multiplier, LIGHTBIT_PRECISION);

		return number_format($number, $precision, '.', '');
	}

	/**
	 * Rounds a number down.
	 *
	 * @param string $number
	 *	The number.
	 *
	 * @param int $precision
	 *	The precision.
	 *
	 * @return string
	 *	The result.
	 */
	public static function floor(string $number, int $precision = 0) : string
	{
		// Multiply the value according to the precision
		$multiplier = bcpow('10', $precision, LIGHTBIT_PRECISION);
		$number = bcmul($number, $multiplier, LIGHTBIT_PRECISION);
		
		// Remove the fractional part of the number
		$number = substr($number, 0, strrpos($number, '.'));

		// Divide it back and format the number for return
		$number = bcdiv($number, $multiplier, LIGHTBIT_PRECISION);

		return number_format($number, $precision, '.', '');
	}

	/**
	 * Rounds a number.
	 *
	 * @param string $number
	 *	The number.
	 *
	 * @param int $precision
	 *	The precision.
	 *
	 * @return string
	 *	The result.
	 */
	public static function round(string $number, int $precision = 0) : string
	{
		return number_format($number, $precision, '.', '');
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		trigger_error(sprintf('Class does not support construction: "%s"', __CLASS__), E_USER_ERROR);
		exit(1);
	}
}
