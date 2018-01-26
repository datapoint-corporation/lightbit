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

use \Lightbit\Html\IHtmlDocument;

/**
 * HtmlDocumentStyle.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HtmlDocumentStyle
{
	/**
	 * The attributes.
	 *
	 * @var string
	 */
	private $attributes;

	/**
	 * The document.
	 *
	 * @var HtmlDocument
	 */
	private $document;

	/**
	 * The location.
	 *
	 * @var string
	 */
	private $location;

	/**
	 * Constructor.
	 *
	 * @param IHtmlDocument $document
	 *	The style document.
	 *
	 * @param string $location
	 *	The style location.
	 *
	 * @param array $attributes
	 *	The style attributes.
	 */
	public function __construct(IHtmlDocument $document, string $location, array $attributes = null)
	{
		$this->attributes = $attributes;
		$this->document = $document;
		$this->location = $location;
	}

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public function getAttributes() : array
	{
		return $this->attributes ?? [];
	}

	/**
	 * Gets the document.
	 *
	 * @return IHtmlDocument
	 *	The document.
	 */
	public function getDocument() : IHtmlDocument
	{
		return $this->document;
	}

	/**
	 * Gets the location.
	 *
	 * @return string
	 *	The location.
	 */
	public function getLocation() : string
	{
		return $this->location;
	}
}