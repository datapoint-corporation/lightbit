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

namespace Lightbit\I18n;

use \Lightbit\Base\IComponent;
use \Lightbit\I18n\ILocale;

/**
 * ILocaleManager.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ILocaleManager extends IComponent
{
	/**
	 * Gets a locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 *
	 * @return ILocale
	 *	The locale.
	 */
	public function getLocale(string $id) : ILocale;

	/**
	 * Gets the preferred locale.
	 *
	 * If this is a web environment and if the "Accept-Language" request 
	 * header is set, it will be used as the preferred locale.
	 *
	 * If this is a console environment, or if no other means are available,
	 * the default system locale is used as the preferred locale.
	 *
	 * @return ILocale
	 *	The preferred locale.
	 */
	public function getPreferredLocale() : ILocale;
}