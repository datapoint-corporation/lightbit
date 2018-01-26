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

namespace Lightbit\I18n\Icu;

use \Lightbit\I18n\ILocale;
use \Lightbit\Math\Decimal;

use \Locale;
use \MessageFormatter;
use \NumberFormatter;

/**
 * IcuLocale.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class IcuLocale implements ILocale
{
	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The locale identifier.
	 */
	public function __construct(string $id)
	{
		$this->id = $id;
	}

	/**
	 * Formats a currency.
	 *
	 * Considering how important it is to present the correct values, this
	 * function expects to work with string based decimals.
	 *
	 * If no precision is given, the default precision matching the locale
	 * is expected instead.
	 *
	 * @param string $currency
	 *	The currency.
	 *
	 * @param float $amount
	 *	The currency amount.
	 *
	 * @param int $precision
	 *	The currency precision.
	 */
	public function currency(string $currency, string $amount, int $precision = null) : string
	{
		$ft = (new Decimal($amount))->toFloat();
		return (new NumberFormatter($this->id, NumberFormatter::CURRENCY))->formatCurrency($ft, $currency);
	}

	/**
	 * Gets the region code.
	 *
	 * @return string
	 *	The region code.
	 */
	public function getRegionCode() : ?string
	{
		return Locale::getRegion($this->id);
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
		return Locale::getPrimaryLanguage($this->id);
	}

	/**
	 * Formats a message.
	 *
	 * @param string $message
	 *	The message.
	 *
	 * @param array $parameters
	 *	The message parameters.
	 *
	 * @return string
	 *	The result.
	 */
	public function message(string $message, array $parameters = null) : string
	{
		if ($parameters)
		{
			return (new MessageFormatter($this->id, $message))->format($parameters);
		}

		return $message;
	}

	/**
	 * Formats a number.
	 *
	 * @param string $amount
	 *	The number amount.
	 *
	 * @param int $precision
	 *	The number precision.
	 *
	 * @return string
	 *	The result.
	 */
	public function number(string $amount, int $precision = null) : string
	{
		$ft = (new Decimal($amount))->toFloat();

		if ($precision)
		{
			$ft = (new Decimal(number_format($ft, $precision, '.', '')))->toFloat();
		}

		return (new NumberFormatter($this->id, NumberFormatter::DECIMAL))->format($ft);
	}
}