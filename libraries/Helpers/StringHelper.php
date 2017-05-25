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

use \Lightbit\Exception;

/**
 * StringHelper.
 *
 * Provides static utility methods for the most common procedures based
 * on strings.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class StringHelper
{
	/**
	 * Creates a content transliteration.
	 *
	 * @param string $content
	 *	The content to create the transliteration from.
	 *
	 * @return string
	 *	The transliteration.
	 */
	public static function transliteration(string $content) : string
	{
		static $transliterator;

		if (!isset($transliterator))
		{
			$transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');

			if (!$transliterator)
			{
				throw new Exception(sprintf('String transliterator is not available: "%s"', 'Any-pt; pt-ASCII'));
			}
		}

		$result = $transliterator->transliterate($content);

		if ($result === false)
		{
			throw new Exception(sprintf('String transliteration error: ', $transliterator->getErrorMessage()));
		}

		return $result;
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
