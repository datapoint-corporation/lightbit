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

/**
 * Formats a number for display.
 *
 * The main advantage between this and the standard "number_format" function is
 * the fact it allows to retain the original number precision if needed.
 *
 * @param string $number
 *	The number.
 *
 * @param int $precision
 *	The precision.
 *
 * @param string $decimals
 *	The decimals delimiter.
 *
 * @param string $thousands
 *	The thousands delimiter.
 *
 * @return string
 *	The result.
 */
function __number_format(string $number, int $precision = null, string $decimals = '.', string $thousands = '')
{
	if (!isset($precision))
	{
		if (($i = strpos($number, '.')) !== false)
		{
			$precision = strlen(substr($number, $i + 1));
		}
		else
		{
			$precision = 0;
		}
	}

	return number_format($number, $precision, $decimals, $thousands);
}
