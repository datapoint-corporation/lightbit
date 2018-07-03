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

namespace Lightbit\Html\Composition;

/**
 * IHtmlComposer.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
interface IHtmlComposer
{
	/**
	 * Composes an element beginning markup.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributeMap
	 *	The element attribute map.
	 *
	 * @return string
	 *	The markup.
	 */
	public function begin(string $tag, array $attributeMap = null) : string;

	/**
	 * Decodes text.
	 *
	 * @param string $text
	 *	The text to decode.
	 *
	 * @return string
	 *	The text.
	 */
	public function decode(string $text) : string;

	/**
	 * Composes an element.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributeMap
	 *	The element attribute map.
	 *
	 * @return string
	 *	The markup.
	 */
	public function element(string $tag, array $attributeMap = null) : string;

	/**
	 * Encodes text.
	 *
	 * @param string $text
	 *	The text to encode.
	 *
	 * @return string
	 *	The markup.
	 */
	public function encode(string $text) : string;

	/**
	 * Composes an element ending markup.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @return string
	 *	The markup.
	 */
	public function end() : string;

	/**
	 * Creates an single attribute map by merging all given maps together,
	 * transforming attributes as applicable.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if attribute mapping fails.
	 *
	 * @param array $attributeMap
	 *	The attribute map.
	 *
	 * @return array
	 *	The attribute map.
	 */
	public function map(array ...$attributeMap) : array;

	/**
	 * Composes a text sequence.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $text
	 *	The text to compose.
	 *
	 * @return string
	 *	The markup.
	 */
	public function text(string $text) : string;
}
