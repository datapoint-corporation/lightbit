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
use \Lightbit\Html\Composition\HtmlComposerProvider;

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
	 * The script list.
	 *
	 * @var array
	 */
	private $scriptListMap;

	/**
	 * The style map.
	 *
	 * @var array
	 */
	private $styleList;

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
		$this->scriptListMap = [];
		$this->styleList = [];

		if ($configuration)
		{
			$configuration->accept($this, [
				'default_title' => 'setDefaultTitle',
				'title' => 'setTitle'
			]);
		}
	}

	/**
	 * Sets an additional inline script.
	 *
	 * @param string $position
	 *	The script position.
	 *
	 * @param string $content
	 *	The script content.
	 *
	 * @param array $attributeMap
	 *	The script attribute map.
	 */
	public final function addInlineScript(string $position, string $content, array $attributeMap = null) : void
	{
		$this->scriptListMap[$position][] = new HtmlDocumentInlineScript($position, $content, $attributeMap);
	}

	/**
	 * Sets an additional inline style.
	 *
	 * @param string $content
	 *	The style content.
	 *
	 * @param array $attributeMap
	 *	The style attribute map.
	 */
	public final function addInlineStyle(string $content, array $attributeMap = null) : void
	{
		$this->styleList[] = new HtmlDocumentInlineStyle($content, $attributeMap);
	}

	/**
	 * Sets an additional script.
	 *
	 * @param string $position
	 *	The script position.
	 *
	 * @param string $url
	 *	The script uniform resource location.
	 *
	 * @param array $attributeMap
	 *	The script attribute map.
	 */
	public final function addScript(string $position, string $url, array $attributeMap = null) : void
	{
		$this->scriptListMap[$position][] = new HtmlDocumentScript($position, $url, $attributeMap);
	}

	/**
	 * Sets an additional style.
	 *
	 * @param string $url
	 *	The style uniform resource location.
	 *
	 * @param array $attributeMap
	 *	The style attribute map.
	 */
	public final function addStyle(string $url, array $attributeMap = null) : void
	{
		$this->styleList[] = new HtmlDocumentStyle($url, $attributeMap);
	}

	/**
	 * Composes the document for a given position.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $position
	 *	The position to compose for.
	 *
	 * @return string
	 *	The markup.
	 */
	public function compose(string $position) : string
	{
		$html = '';
		$composer = HtmlComposerProvider::getInstance()->getComposer();

		$html .= ($composer->comment('begin document ' . $position) . PHP_EOL);

		if ($position === 'head')
		{
			$html .= ($composer->element('meta', [ 'charset' => $this->characterSet ]) . PHP_EOL);
			$html .= ($composer->element('meta', [ 'name' => 'X-Powered-By', 'content' => 'Lightbit/2.0.0' ]) . PHP_EOL);
			$html .= (($composer->begin('title') . $composer->text($this->title ?? $this->defaultTitle) . $composer->end()) . PHP_EOL);

			foreach ($this->styleList as $i => $style)
			{
				$html .= ($style->compose() . PHP_EOL);
			}
		}

		if (isset($this->scriptListMap[$position]))
		{
			foreach ($this->scriptListMap[$position] as $i => $script)
			{
				$html .= ($script->compose() . PHP_EOL);
			}
		}

		$html .= ($composer->comment('end document ' . $position) . PHP_EOL);

		return $html;
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
