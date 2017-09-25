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

function __decimal_add(string $alpha, string $bravo) : string
{
	return bcadd($alpha, $bravo, LIGHTBIT_DECIMAL_PRECISION);
}

function __decimal_ceil(string $number, int $precision = 0) : string
{
	// Multiply the value according to the precision
	$multiplier = bcpow('10', $precision, LIGHTBIT_DECIMAL_PRECISION);
	$number = bcmul($number, $multiplier, LIGHTBIT_DECIMAL_PRECISION);

	// Make the necessary changes to the integral part
	list ($integral, $fractional) = explode('.', $number);

	if (bccomp('0', $fractional, LIGHTBIT_DECIMAL_PRECISION))
	{
		$number = bcadd($integral, '1', LIGHTBIT_DECIMAL_PRECISION);
	}

	// Divide it back and format the number for return
	$number = bcdiv($number, $multiplier, LIGHTBIT_DECIMAL_PRECISION);

	return number_format($number, $precision, '.', '');
}

function __decimal_divide(string $alpha, string $bravo) : string
{
	return bcdiv($alpha, $bravo, LIGHTBIT_DECIMAL_PRECISION);
}

function __decimal_floor(string $number, int $precision = 0) : string
{
	// Multiply the value according to the precision
	$multiplier = bcpow('10', $precision, LIGHTBIT_DECIMAL_PRECISION);
	$number = bcmul($number, $multiplier, LIGHTBIT_DECIMAL_PRECISION);

	// Remove the fractional part of the number
	$number = substr($number, 0, strrpos($number, '.'));

	// Divide it back and format the number for return
	$number = bcdiv($number, $multiplier, LIGHTBIT_DECIMAL_PRECISION);

	return number_format($number, $precision, '.', '');
}

function __decimal_multiply(string $alpha, string $bravo) : string
{
	return bcmul($alpha, $bravo, LIGHTBIT_DECIMAL_PRECISION);
}

function __decimal_precision() : int
{
	return LIGHTBIT_DECIMAL_PRECISION;
}

function __decimal_round(string $number, int $precision = 0) : string
{
	return number_format($number, $precision, '.', '');
}

function __decimal_subtract(string $alpha, string $bravo) : string
{
	return bcsub($alpha, $bravo, LIGHTBIT_DECIMAL_PRECISION);
}
