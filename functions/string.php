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

function __string_length(string $content) : int
{
	return mb_strlen($content);
}

function __string_match(string $subject, string $candidate, bool $insensitive = false) : string
{
	if ($insensitive)
	{
		return strtolower($subject) === strtolower($candidate);
	}

	return $subject === $candidate;
}

function __string_slug(string $content, string $delimiter = '-') : string
{
	return strtolower
	(
		implode
		(
			$delimiter,
			preg_split('%([^\\w]+|_+)%', __string_transliterate($content), -1, PREG_SPLIT_NO_EMPTY)
		)
	);
}

function __string_split_word(string $content) : array
{
	return preg_split('%[^\\p{L}]+%u', $content, -1, PREG_SPLIT_NO_EMPTY);
}

function __string_split_word_ascii(string $ascii)
{
	return preg_split('%[^\\w]+%', $content, -1, PREG_SPLIT_NO_EMPTY);
}

function __string_transliterate(string $content) : string
{
	static $transliterator;

	if (!$transliterator)
	{
		$transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

		if (!$transliterator)
		{
			__throw('String transliterator does not exist: transliterator %s', 'Any-Latin; Latin-ASCII');
		}
	}

	$result = $transliterator->transliterate($content);

	if ($result === false)
	{
		__throw('String transliterator error: ', $transliterator->getErrorMessage());
	}

	return $result;
}
