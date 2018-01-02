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

use \Lightbit\Base\Component;
use \Lightbit\Base\View;
use \Lightbit\Html\Navigation\HtmlBreadcrumb;
use \Lightbit\Html\Navigation\IHtmlBreadcrumb;

use \Lightbit\Base\IContext;
use \Lightbit\Html\IHtmlDocument;

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
	 * @param IHtmlBreadcrumb $title
	 *	The breadcrumb.
	 */
	public function addBreadcrumb(IHtmlBreadcrumb $breadcrumb) : void
	{
		$this->breadcrumbs[] = $breadcrumb;
	}

	/**
	 * Sets additional breadcrumbs.
	 *
	 * @param array $breadcrumbs
	 *	The breadcrumbs.
	 */
	public function addBreadcrumbs(array $breadcrumbs) : void
	{
		foreach ($breadcrumbs as $i => $breadcrumb)
		{
			if (! ($breadcrumb instanceof IHtmlBreadcrumb))
			{
				$breadcrumb = HtmlBreadcrumb::create($breadcrumb);
			}

			$this->addBreadcrumb($breadcrumb);
		}
	}

	/**
	 * Sets an additional inline script.
	 *
	 * @param string $script
	 *	The inline script file system alias.
	 *
	 * @param array $attributes
	 *	The script attributes.
	 *
	 * @param string $position
	 *	The script position.
	 */
	public function addInlineScript(string $script, array $attributes = null, string $position = 'body') : void
	{
		$this->inlineScripts[$position][] =
		[
			'attributes' => $attributes,
			'script' => $script
		];
	}

	/**
	 * Sets an additional inline style.
	 *
	 * @param string $style
	 *	The style file system alias.
	 *
	 * @param array $attributes
	 *	The style attributes.
	 */
	public function addInlineStyle(string $style, array $attributes = null) : void
	{
		$this->inlineStyles[] =
		[
			'attributes' => $attributes,
			'style' => $style
		];
	}

	/**
	 * Sets additional meta attributes.
	 *
	 * @param array $attributes
	 *	The meta attributes.
	 */
	public function addMetaAttributes(array $attributes) : void
	{
		$this->metaAttributes[] = $attributes;
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
		$this->styles[] =
		[
			'attributes' => $attributes,
			'location' => $location
		];
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
		$this->tags[] =
		[
			'attributes' => $attributes,
			'tag' => $tag
		];
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
	 * Gets the meta attributes.
	 *
	 * @return array
	 *	The meta attributes.
	 */
	public function getMetaAttributes() : array
	{
		return $this->metaAttributes;
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
	public function getTitle() : ?string
	{
		return $this->title;
	}

	/**
	 * Inflates the document.
	 *
	 * @param string $position
	 *	The position.
	 *
	 * @return string
	 *	The markup.
	 */
	public function inflate(string $position) : string
	{
		$html = $this->getHtmlAdapter();
		$result = '';

		if ($position === 'head')
		{
			$result .= __html_element('base', [ 'href' => $this->getHttpRouter()->getBaseUrl() ]) . PHP_EOL;

			foreach ($this->tags as $i => $tag)
			{
				$result .= __html_element($tag['tag'], $tag['attributes']) . PHP_EOL;
			}

			foreach ($this->metaAttributes as $i => $attributes)
			{
				$result .= __html_element('meta', $attributes) . PHP_EOL;
			}

			if ($this->title)
			{
				$result .= __html_element('title', null, $this->title) . PHP_EOL;
			}

			else if ($this->defaultTitle)
			{
				$result .= __html_element('title', null, $this->defaultTitle) . PHP_EOL;
			}

			foreach ($this->styles as $i => $style)
			{
				$result .=

				__html_element
				(
					'link',
					__html_attribute_array_merge
					(
						[ 'rel' => 'stylesheet', 'type' => 'text/css' ],
						$style['attributes'],
						[ 'href' => $style['location'] ]
					)
				)

				. PHP_EOL;
			}

			foreach ($this->inlineStyles as $i => $style)
			{
				$result .=

				__html_element
				(
					'style',
					__html_attribute_array_merge([ 'type' => 'text/css' ], $style['attributes']),
					(new View($this->getContext(), (__asset_path_resolve(null, 'php', $style['style']))))->run(null, true),
					false
				)

				. PHP_EOL;
			}
		}

		if (isset($this->scripts[$position]))
		{
			foreach ($this->scripts[$position] as $i => $script)
			{
				$result .=

				__html_element
				(
					'script',
					__html_attribute_array_merge
					(
						[
							'language' => 'javascript',
							'type' => 'text/javascript'
						],
						$script['attributes'],
						[
							'src' => $script['location']
						]
					)
				)

				. PHP_EOL;
			}
		}

		if (isset($this->inlineScripts[$position]))
		{
			foreach ($this->inlineScripts as $i => $script)
			{
				$result .=

				__html_element
				(
					'script',
					__html_attribute_array_merge([ 'type' => 'text/javascript', 'language' => 'javascript' ], $script['attributes']),
					(new View($this->getContext(), (__asset_path_resolve(null, 'php', $script['script']))))->run(null, true),
					false
				)

				. PHP_EOL;
			}
		}

		return $result;
	}

	/**
	 * Resets the document.
	 */
	public function reset() : void
	{
		$this->breadcrumbs = [];
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
