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
	 * The locales.
	 *
	 * @var array
	 */
	private $locales;

	/**
	 * The preferred locales whitelist.
	 *
	 * @var array
	 */
	private $whitelist;

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
		if ($this->hasLocale($id))
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
		if (!isset($this->locales))
		{
			$this->locales = ResourceBundle::getLocales('');

			if ($this->whitelist)
			{
				$this->locales = array_intersect($this->locales, $this->whitelist);
			}
		}

		return in_array($id, $this->locales);
	}

	/**
	 * Gets the preferred locale.
	 *
	 * If this is a web environment and if the "Accept-Language" request 
	 * header is set, it will be used as the preferred locale.
	 *
	 * If this is a console environment, or if no other means are available,
	 * the default system locale is used as the preferred locale.
	 *
	 * If a whitelist is set and the preferred locale is not included in it,
	 * the last locale on that list is returned instead.
	 *
	 * @return ILocale
	 *	The preferred locale.
	 */
	public function getPreferredLocale() : ILocale
	{
		// No whitelist? We'll simply pass the whole procedure to the
		// LibICU implementation, as it's faster.
		if (!$this->whitelist)
		{
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				return $this->getLocale(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']));
			}

			return $this->getLocale(locale_get_default());
		}

		// Since the language API doesn't provide a simpler way to do this,
		// for now, we'll have to settle with a custom parser.
		// Example: en-US,en;q=0.9,pt-PT;q=0.8,pt;q=0.7
		$match = null;

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$expressions = preg_split('%\\,%', $_SERVER['HTTP_ACCEPT_LANGUAGE'], -1, PREG_SPLIT_NO_EMPTY);

			foreach ($expressions as $i => $expression)
			{
			}
		}

		return $this->getLocale(end($this->whitelist));
	}

	/**
	 * Sets the locale whitelist.
	 *
	 * @param array $whitelist
	 *	The locale whitelist.
	 *
	 * @return array
	 *	The result.
	 */
	public function setWhitelist(?array $whitelist)
	{
		$this->locales = null;
		$this->whitelist = $whitelist;
	}
}