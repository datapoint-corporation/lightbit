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

use \Lightbit\Base\Component;
use \Lightbit\I18n\Icu\IcuLocale;
use \Lightbit\I18n\ILocale;
use \Lightbit\I18n\ILocaleManager;
use \Lightbit\I18n\LocaleManagerException;

use \ResourceBundle;

/**
 * ILocaleManager.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class IcuLocaleManager extends Component implements ILocaleManager
{
	/**
	 * The available locales.
	 *
	 * @var array
	 */
	private $availableLocales;

	/**
	 * Gets a locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 *
	 * @return ILocale
	 *	The locale.
	 */
	public function getLocale(string $id) : ILocale
	{
		if (!isset($this->availableLocales))
		{
			$this->availableLocales = ResourceBundle::getLocales('');
		}

		if (in_array($id, $this->availableLocales))
		{
			return new IcuLocale($id);
		}

		throw new LocaleManagerException($this, sprintf('Can not get locale, not available: "%s"', $id));
	}

	/**
	 * Checks if a locale exists.
	 *
	 * @param string $id
	 *	The locale identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasLocale(string $id) : bool
	{
		if (!isset($this->availableLocales))
		{
			$this->availableLocales = ResourceBundle::getLocales('');
		}

		return in_array($id, $this->availableLocales);
	}
}