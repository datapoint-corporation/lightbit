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

namespace Lightbit\Html\UI;

use \Lightbit\Base\IContext;
use \Lightbit\Html\HtmlWidget;
use \Lightbit\Html\IHtmlAdapter;

/**
 * IFormHtmlWidget.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class FormHtmlWidget extends HtmlWidget
{
	/**
	 * The html adapter.
	 *
	 * @type IHtmlAdapter
	 */
	private $html;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The html widget context.
	 *
	 * @param array $configuration
	 *	The html widget configuration.
	 */
	public function __construct(IContext $context, array $configuration = null)
	{
		parent::__construct($context, $configuration);

		$this->html = $this->getHtmlAdapter();
	}

	/**
	 * Creates a text input.
	 *
	 * @param string $name
	 *	The input name.
	 *
	 * @param array $attributes
	 *	The input attributes.
	 *
	 * @return string
	 *	The input markup.
	 */
	public function text(string $name, array $attributes = null) : string
	{
		return $this->html->element
		(
			'input',
			$this->html->merge
			(
				[ 'type' => 'text' ],
				$attributes,
				[ 'name' => $name ]
			)
		);
	}
}
