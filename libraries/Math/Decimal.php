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

namespace Lightbit\Math;

use \Lightbit\Data\ISerializable;
use \Lightbit\Math\DecimalParseException;

/**
 * Decimal.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Decimal implements ISerializable
{
	/**
	 * Unserializes the object.
	 *
	 * @param string $subject
	 *	The subject.
	 *
	 * @return object
	 *	The object, on success.
	 */
	public static function unserialize(string $subject) : ?object
	{
		try
		{
			return new Decimal($subject);
		}
		catch (DecimalParseException $e)
		{
			return null;
		}
	}

	/**
	 * The decimal.
	 *
	 * @var string
	 */
	private $decimal;

	/**
	 * The precision.
	 *
	 * @var int
	 */
	private $precision;

	/**
	 * Constructor.
	 *
	 * @param string $decimal
	 *	The decimal.
	 *
	 * @param int $precision
	 *	The decimal precision.
	 */
	public function __construct(string $decimal, int $precision = 8)
	{
		if (!preg_match('%^\\d+(\\.\\d+)?$%', $decimal))
		{
			throw new DecimalParseException(sprintf('Can not parse decimal, illegal format: "%s"', $decimal));
		}

		$this->decimal = $decimal;
		$this->precision = $precision;
	}

	/**
	 * Performs addition.
	 *
	 * @param Decimal $decimal
	 *	The decimal to add.
	 */
	public function add(Decimal $decimal) : void
	{
		$this->decimal = bcadd($this->decimal, $decimal->decimal, (($decimal->precision > $this->precision) ? $decimal->precision : $this->precision));
	}

	/**
	 * Performs division.
	 *
	 * @param Decimal $decimal
	 *	The decimal to divide.
	 */
	public function divide(Decimal $decimal) : void
	{
		$this->decimal = bcdiv($this->decimal, $decimal->decimal, (($decimal->precision > $this->precision) ? $decimal->precision : $this->precision));
	}

	/**
	 * Performs multiplication.
	 *
	 * @param Decimal $decimal
	 *	The decimal to multiply.
	 */
	public function multiply(Decimal $decimal) : void
	{
		$this->decimal = bcmul($this->decimal, $decimal->decimal, (($decimal->precision > $this->precision) ? $decimal->precision : $this->precision));
	}

	/**
	 * Performs raising to the power.
	 *
	 * @param Decimal $decimal
	 *	The decimal to raise against.
	 */
	public function power(Decimal $decimal) : void
	{
		$this->decimal = bcpow($this->decimal, $decimal->decimal, (($decimal->precision > $this->precision) ? $decimal->precision : $this->precision));
	}

	/**
	 * Performs subtraction.
	 *
	 * @param Decimal $decimal
	 *	The decimal to subtract.
	 */
	public function subtract(Decimal $decimal) : void
	{
		$this->decimal = bcsub($this->decimal, $decimal->decimal, (($decimal->precision > $this->precision) ? $decimal->precision : $this->precision));
	}

	/**
	 * Serializes the object.
	 *
	 * @return string
	 *	The result.
	 */
	public function serialize() : string
	{
		return $this->toString();
	}

	/**
	 * Converts to a float.
	 *
	 * @return float
	 *	The result.
	 */
	public function toFloat() : float
	{
		$decimal = $this->decimal;

		if ($locale = localeconv())
		{
			$decimal = strtr($decimal, [ '.' => $locale['decimal_point'] ]);
		}

		return floatval($decimal);
	}

	/**
	 * Converts to a string.
	 *
	 * @param int $precision
	 *	The conversion precision.
	 *
	 * @return string
	 *	The result.
	 */
	public function toString(int $precision = null) : string
	{
		if (!isset($precision))
		{
			if (($i = strpos($this->decimal, '.')) !== false)
			{
				$precision = strlen(substr(rtrim($this->decimal, '0'), $i + 1));
			}
			else
			{
				$precision = 0;
			}
		}

		return number_format($this->toFloat(), $precision, '.', '');
	}
}