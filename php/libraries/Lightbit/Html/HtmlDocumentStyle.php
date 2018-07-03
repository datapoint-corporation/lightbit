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

use \Lightbit\Html\Composition\HtmlComposer;
use \Lightbit\Html\Composition\HtmlComposerException;
use \Lightbit\Html\Composition\HtmlComposerProvider;

use \Lightbit\Html\IHtmlElement;

/**
 * HtmlDocumentStyle.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlDocumentStyle implements IHtmlElement
{
	/**
	 * The attribute map.
	 *
	 * @var array
	 */
	private $attributeMap;

	/**
	 * The uniform resource location.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Constructor.
	 *
	 * @param string $url
	 *	The style uniform resource location.
	 *
	 * @param array $attributeMap
	 *	The style attribute map.
	 */
	public function __construct(string $url, array $attributeMap = null)
	{
		$this->attributeMap = $attributeMap;
		$this->url = $url;
	}

	/**
	 * Composes the element.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @return string
	 *	The markup.
	 */
	public function compose() : string
	{
		$composer = HtmlComposerProvider::getInstance()->getComposer();

		return $composer->element(
			'link',
			$composer->map(
				[ 'rel' => 'stylesheet', 'type' => 'text/javastyle', 'href' => $this->url ],
				$this->attributeMap
			)
		);
	}

	/**
	 * Gets the attribute map.
	 *
	 * @return array
	 *	The attribute map.
	 */
	public function getAttributeMap() : array
	{
		return ($this->attributeMap ?? []);
	}

	/**
	 * Gets the element identifier.
	 *
	 * @return string
	 *	The element identifier.
	 */
	public function getID() : ?string
	{
		if (isset($this->attributeMap['id']))
		{
			return $this->attributeMap['id'];
		}

		return null;
	}
}
