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

namespace Lightbit\Globalization;

use \Lightbit\Base\Object;
use \Lightbit\Exception;
use \Lightbit\Globalization\Locale;

/**
 * Locale.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Locale extends Object implements ILocale
{
	/**
	 * Gets a locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 *
	 * @return Locale
	 *	The locale.
	 */
	public static function getLocale(string $id) : Locale
	{
		static $locales = [];

		if (!isset($locales[$id]))
		{
			$locales[$id] = new Locale($id);
		}

		return $locales[$id];
	}

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * The number formatters.
	 *
	 * @type array
	 */
	private $numberFormatters;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The locale identifier.
	 */
	private function __construct(string $id)
	{
		$this->id = $id;
		$this->numberFormatters = [];
	}

	/**
	 * Formats a number as currency.
	 *
	 * @param string $currency
	 *	The currency to format as.
	 *
	 * @param string $number
	 *	The number to format.
	 *
	 * @param int $precision
	 *	The number precision.
	 *
	 * @return string
	 *	The result.
	 */
	public function currency(string $currency, string $number, int $precision = null) : string
	{
		if ($precision)
		{
			$number = __decimal_round($currency, 2);
		}

		return $this->getNumberFormatter(\NumberFormatter::CURRENCY)
			->formatCurrency($number, $currency);
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the language code.
	 *
	 * @return string
	 *	The language code.
	 */
	public function getLanguageCode() : string
	{
		return strtolower(\Locale::getPrimaryLanguage($this->id));
	}

	/**
	 * Gets the region code.
	 *
	 * @return string
	 *	The region code.
	 */
	public function getRegionCode() : string
	{
		return strtoupper(\Locale::getRegion($this->id));
	}

	/**
	 * Gets a number formatter.
	 *
	 * @param int $style
	 *	The number formatter style.
	 *
	 * @return NumberFormatter
	 *	The number formatter.
	 */
	private function getNumberFormatter(int $style) : \NumberFormatter
	{
		if (!isset($this->numberFormatters[$style]))
		{
			$this->numberFormatters[$style] = new \NumberFormatter($this->id, $style);
		}

		return $this->numberFormatters[$style];
	}

	/**
	 * Formats a message.
	 *
	 * @param string $message
	 *	The message to format.
	 *
	 * @param array $arguments
	 *	The message arguments.
	 *
	 * @return string
	 *	The result.
	 */
	public function message(string $message, array $arguments = null) : string
	{
		if ($arguments)
		{
			$result = msgfmt_format_message($this->id, $message, $arguments);

			if ($result === false)
			{
				throw new Exception
				(
					sprintf
					(
						'Can not format message: locale %s, message %s, error %d, %s', 
						$this->id,
						$message,
						intl_get_error_code(),
						lcfirst(intl_get_error_message())
					)
				);
			}

			return $result;
		}

		return $message;
	}

	/**
	 * Formats a number.
	 *
	 * @param string $number
	 *	The number to format.
	 *
	 * @param int $precision
	 *	The number precision.
	 *
	 * @return string
	 *	The result.
	 */
	public function number(string $number, int $precision = null) : string
	{
		if ($precision)
		{
			$number = __decimal_round($number, $precision);
		}

		return $this->getNumberFormatter(\NumberFormatter::DECIMAL)
			->format($number);
	}
}
