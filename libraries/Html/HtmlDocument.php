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

use \Lightbit\Base\Component;
use \Lightbit\Base\View;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Html\Navigation\HtmlBreadcrumb;
use \Lightbit\Html\Navigation\IHtmlBreadcrumb;

/**
 * HtmlDocument.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HtmlDocument extends Component implements IHtmlDocument
{
	/**
	 * The breadcrumbs.
	 *
	 * @var array
	 */
	private $breadcrumbs;

	/**
	 * The default title.
	 *
	 * @var string
	 */
	private $defaultTitle;

	/**
	 * The inline scripts.
	 *
	 * @var array
	 */
	private $inlineScripts;

	/**
	 * The inline styles.
	 *
	 * @var array
	 */
	private $inlineStyles;

	/**
	 * The meta attributes.
	 *
	 * @var array
	 */
	private $metaAttributes;

	/**
	 * The scripts.
	 *
	 * @var array
	 */
	private $scripts;

	/**
	 * The styles.
	 *
	 * @var array
	 */
	private $styles;

	/**
	 * The tags.
	 *
	 * @var array
	 */
	private $tags;

	/**
	 * The title.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Sets an additional breadcrumb.
	 *
	 * @param string $label
	 *	The breadcrumb label.
	 *
	 * @param array $route
	 *	The breadcrumb route.
	 *
	 * @param array $attributes
	 *	The breadcrumb attributes.
	 */
	public function addBreadcrumb(string $label, array $route, array $attributes = null) : void
	{
		$this->breadcrumbs[] = new HtmlDocumentBreadcrumb($this, $label, $route, $attributes);
	}

	/**
	 * Sets an additional inline script.
	 *
	 * @param string $content
	 *	The script content.
	 *
	 * @param array $attributes
	 *	The script attributes.
	 *
	 * @param string $position
	 *	The script position.
	 */
	public function addInlineScript(string $content, array $attributes = null, string $position = 'body') : void
	{
		$this->inlineScripts[$position][] = new HtmlDocumentInlineScript($this, $content, $attributes);
	}

	/**
	 * Sets an additional inline style.
	 *
	 * @param string $content
	 *	The style content.
	 *
	 * @param array $attributes
	 *	The style attributes.
	 */
	public function addInlineStyle(string $content, array $attributes = null) : void
	{
		$this->inlineStyles[] = new HtmlDocumentInlineStyle($this, $content, $attributes);
	}

	/**
	 * Sets an additional script.
	 *
	 * @param string $location
	 *	The script location.
	 *
	 * @param array $attributes
	 *	The script attributes.
	 *
	 * @param string $position
	 *	The script position.
	 */
	public function addScript(string $location, array $attributes = null, string $position = 'body') : void
	{
		$this->scripts[$position][] =
		[
			'attributes' => $attributes,
			'location' => $location
		];
	}

	/**
	 * Sets an additional style.
	 *
	 * @param string $location
	 *	The stylesheet location.
	 *
	 * @param array $attributes
	 *	The style attributes.
	 */
	public function addStyle(string $location, array $attributes = null) : void
	{
		$this->styles[] = new HtmlDocumentStyle($this, $location, $attributes);
	}

	/**
	 * Sets an additional tag.
	 *
	 * @param string $tag
	 *	The tag name.
	 *
	 * @param array $attributes
	 *	The link attributes.
	 */
	public function addTag(string $tag, array $attributes = null) : void
	{
		$this->tags[] = new HtmlDocumentTag($this, $tag, $attributes);
	}

	/**
	 * Gets the breadcrumbs.
	 *
	 * @return array
	 *	The breadcrumbs.
	 */
	public function getBreadcrumbs() : array
	{
		return $this->breadcrumbs;
	}

	/**
	 * Gets the inline scripts.
	 *
	 * @return array
	 *	The inline scripts, by position.
	 */
	public function getInlineScripts() : array
	{
		return $this->inlineScripts;
	}

	/**
	 * Gets the inline styles.
	 *
	 * @return string
	 *	The inline styles.
	 */
	public function getInlineStyles() : array
	{
		return $this->inlineStyles;
	}

	/**
	 * Gets the scripts.
	 *
	 * @return array
	 *	The scripts.
	 */
	public function getScripts() : array
	{
		return $this->scripts;
	}

	/**
	 * Gets the styles.
	 *
	 * @return array
	 *	The styles.
	 */
	public function getStyles() : array
	{
		return $this->styles;
	}

	/**
	 * Gets the tags.
	 *
	 * @return array
	 *	The tags.
	 */
	public function getTags() : array
	{
		return $this->tags;
	}

	/**
	 * Gets the title.
	 *
	 * @return string
	 *	The title.
	 */
	public function getTitle() : string
	{
		return $this->title ?? $this->defaultTitle;
	}

	/**
	 * Resets the document.
	 */
	public function reset() : void
	{
		$this->breadcrumbs = [];
		$this->defaultTitle = 'Untitled Page';
		$this->inlineScripts = [];
		$this->inlineStyles = [];
		$this->metaAttributes = [];
		$this->scripts = [];
		$this->styles = [];
		$this->tags = [];
	}

	public function setBreadcrumbs(array $breadcrumbs) : void
	{
		$this->breadcrumbs = [];
		$this->addBreadcrumbs($breadcrumbs);
	}

	/**
	 * Sets the default title.
	 *
	 * @param string $defaultTitle
	 *	The default title.
	 */
	public function setDefaultTitle(?string $defaultTitle) : void
	{
		$this->defaultTitle = $defaultTitle;
	}

	/**
	 * Sets the title.
	 *
	 * @param string $title
	 *	The title.
	 */
	public function setTitle(?string $title) : void
	{
		$this->title = $title;
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the component construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		parent::onConstruct();
		
		$this->reset();
	}
}
