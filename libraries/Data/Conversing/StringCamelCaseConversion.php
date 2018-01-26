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

namespace Lightbit\Data\Conversing;

/**
 * StringCamelCaseConversion.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class StringCamelCaseConversion
{
	/**
	 * The multibyte flag.
	 *
	 * @var bool
	 */
	private $multibyte;

	/**
	 * The subject.
	 *
	 * @var string
	 */
	private $subject;

	/**
	 * Constructor.
	 *
	 * @param string $subject
	 *	The conversion subject.
	 *
	 * @param bool $multibyte
	 *	The conversion multibyte flag.
	 */
	public function __construct(string $subject, bool $multibyte = true)
	{
		$this->multibyte = $multibyte;
		$this->subject = $subject;
	}

	/**
	 * Converts to lower camel case.
	 *
	 * @return string
	 *	The result.
	 */
	public function toLowerCamelCase() : string
	{
		if ($this->multibyte)
		{
			$tokens = preg_split('%[^\\p{L}\\p{N}]+%', $this->subject, -1, PREG_SPLIT_NO_EMPTY);

			foreach ($tokens as $i => $token)
			{
				if ($i > 0)
				{
					$tokens[$i] = mb_strtoupper(mb_substr($token, 0, 1)) . mb_strtolower(mb_substr($token, 1));
				}
				else
				{
					$tokens[$i] = mb_strtolower($token);
				}
			}

			return implode('', $tokens);
		}

		$tokens = preg_split('%[^A-Za-z0-9]+%', $this->subject, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tokens as $i => $token)
		{
			if ($i > 0)
			{
				$tokens[$i] = strtoupper(substr($token, 0, 1)) . strtolower(substr($token, 1));
			}
			else
			{
				$tokens[$i] = strtolower($token);
			}
		}

		return implode('', $tokens);
	}

	/**
	 * Converts to upper camel case.
	 *
	 * @return string
	 *	The result.
	 */
	public function toUpperCamelCase() : string
	{
		if ($this->multibyte)
		{
			$tokens = preg_split('%[^\\p{L}]+%', $this->subject, -1, PREG_SPLIT_NO_EMPTY);

			foreach ($tokens as $i => $token)
			{
				$tokens[$i] = mb_strtoupper(mb_substr($token, 0, 1)) . mb_strtolower(mb_substr($token, 1));
			}

			return implode('', $tokens);
		}

		$tokens = preg_split('%[^A-Za-z0-9]+%', $this->subject, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tokens as $i => $token)
		{
			$tokens[$i] = strtoupper(substr($token, 0, 1)) . strtolower(substr($token, 1));
		}

		return implode('', $tokens);
	}
}