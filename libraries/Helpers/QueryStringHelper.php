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

use \Lightbit;
use \Lightbit\Base\Object;
use \Lightbit\Base\Exception;

/**
 * QueryStringHelper.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class QueryStringHelper
{
	/**
	 * Encodes a query string.
	 *
	 * @param array $parameters
	 *	The query string parameters.
	 *
	 * @return string
	 *	The query string.
	 */
	public static function encode(array $content) : string
	{
		return http_build_query(self::encodeArray($content), '_', '&', PHP_QUERY_RFC1738);
	}

	/**
	 * Encodes an array recursively in preparation to build a query string.
	 *
	 * @param array $content
	 *	The content to encode.
	 *
	 * @return array
	 *	The result.
	 */
	private static function encodeArray(array $content) : array
	{
		foreach ($content as $key => $value)
		{
			if (is_array($value))
			{
				$content[$key] = self::encodeArray($value);
				continue;
			}

			if ($value instanceof Object)
			{
				$content[$key] = Lightbit::getApplication()->getSlugManager()->compose($value);
				continue;
			}

			$content[$key] = TypeHelper::toString($value);
		}

		return $content;
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
