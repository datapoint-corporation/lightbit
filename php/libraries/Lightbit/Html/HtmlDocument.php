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

use \Lightbit\Configuration\IConfiguration;
use \Lightbit\Html\IHtmlDocument;

/**
 * HtmlDocument.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlDocument implements IHtmlDocument
{
	/**
	 * The character set.
	 *
	 * @var string
	 */
	private $characterSet;

	/**
	 * The default title.
	 *
	 * @var string
	 */
	private $defaultTitle;

	/**
	 * The title.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Constructor.
	 *
	 * @param IConfiguration $configuration
	 *	The html document configuration.
	 */
	public function __construct(IConfiguration $configuration = null)
	{
		$this->characterSet = 'utf-8';
		$this->defaultTitle = 'Untitled Document';

		if ($configuration)
		{
			$configuration->accept($this, [
				'default_title' => 'setDefaultTitle',
				'title' => 'setTitle'
			]);
		}
	}

	/**
	 * Gets the character set.
	 *
	 * @return string
	 *	The character set.
	 */
	public final function getCharacterSet() : string
	{
		return $this->characterSet;
	}

	/**
	 * Gets the title.
	 *
	 * @return string
	 *	The title.
	 */
	public final function getTitle() : string
	{
		return ($this->title ?? $this->defaultTitle);
	}

	/**
	 * Checks if a title is set.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasTitle() : bool
	{
		return !!$this->title;
	}

	/**
	 * Sets the character set.
	 *
	 * @return string
	 *	The character set.
	 */
	public final function setCharacterSet(string $characterSet) : void
	{
		$this->characterSet = $characterSet;
	}

	/**
	 * Sets the default title.
	 *
	 * @param string $defaultTitle
	 *	The default title.
	 */
	public final function setDefaultTitle(string $defaultTitle) : void
	{
		$this->defaultTitle = $defaultTitle;
	}

	/**
	 * Sets the title.
	 *
	 * @param string $title
	 *	The title.
	 */
	public final function setTitle(?string $title) : void
	{
		$this->title = $title;
	}
}
