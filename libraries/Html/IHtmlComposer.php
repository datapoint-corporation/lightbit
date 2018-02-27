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

namespace Lightbit\Html;

use \Lightbit\Base\IComponent;
use \Lightbit\Data\IModel;

interface IHtmlComposer extends IComponent
{
	/**
	 * Composes an attribute.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param mixed $attribute
	 *	The attribute.
	 */
	public function attribute(string $property, $attribute) : string;

	/**
	 * Composes the attributes.
	 *
	 * @param array $attributes
	 *	The attributes
	 *
	 * @return string
	 *	The result.
	 */
	public function attributes(array $attributes) : string;

	/**
	 * Composes an element opening tag.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @return string
	 *	The result.
	 */
	public function begin(string $tag, array $attributes = null) : string;

	/**
	 * Composes an element.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @param string $content
	 *	The element content.
	 *
	 * @param bool $encode
	 *	The element content encoding flag.
	 *
	 * @return string
	 *	The result.
	 */
	public function element(string $tag, array $attributes = null, string $content = null, bool $encode = true) : string;

	/**
	 * Composes an element ending tag.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @return string
	 *	The result.
	 */
	public function end(string $tag) : string;

	/**
	 * Composes an input identifier.
	 *
	 * @param IModel $model
	 *	The input model.
	 *
	 * @param string $attribute
	 *	The input model attribute name.
	 *
	 * @return string
	 *	The result.
	 */
	public function id(IModel $model, string $attribute) : string;

	/**
	 * Composes an input name.
	 *
	 * @param IModel $model
	 *	The input model.
	 *
	 * @param string $attribute
	 *	The input model attribute name.
	 *
	 * @return string
	 *	The result.
	 */
	public function name(IModel $model, string $attribute) : string;

	/**
	 * Composes text.
	 *
	 * @param string $content
	 *	The content.
	 *
	 * @return string
	 *	The result.
	 */
	public function text(string $content) : string;
}