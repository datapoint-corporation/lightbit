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

namespace Lightbit\Html;

use \Lightbit\Base\IComponent;

/**
 * IHtmlAdapter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHtmlAdapter extends IComponent
{
	/**
	 * Inflates an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $value
	 *	The attribute value.
	 *
	 * @return string
	 *	The markup.
	 */
	public function attribute(string $attribute, $value) : string;

	/**
	 * Inflates attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 *
	 * @return string
	 *	The markup.
	 */
	public function attributes(array $attributes) : string;

	/**
	 * Inflates the begin of an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @return string
	 *	The markup.
	 */
	public function begin(string $tag, array $attributes = null) : string;

	/**
	 * Inflates a comment.
	 *
	 * @param string $content
	 *	The comment content.
	 *
	 * @return string
	 *	The markup.
	 */
	public function comment(string $content) : string;

	/**
	 * Inflates an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @param string $content
	 *	The element content.
	 *
	 * @param bool $escape
	 *	The escape flag which, when set, will cause the content to be escaped
	 *	before being injected within the element.
	 *
	 * @return string
	 *	The markup.
	 */
	public function element(string $tag, array $attributes = null, string $content = null, bool $escape = true) : string;

	/**
	 * Inflates the end of an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @return string
	 *	The markup.
	 */
	public function end(string $tag) : string;

	/**
	 * Escapes the given content.
	 *
	 * @param string $content
	 *	The content.
	 *
	 * @return string
	 *	The result.
	 */
	public function escape(string $content) : string;

	/**
	 * Merges attributes.
	 *
	 * @param array $attributes
	 *	The attributes to merge.
	 *
	 * @return array
	 *	The result.
	 */
	public function merge(...$attributes) : array;
}