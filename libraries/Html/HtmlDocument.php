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
use \Lightbit\Helpers\ObjectHelper;
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
	 * The inline scripts.
	 *
	 * @type array
	 */
	private $inlineScripts;

	/**
	 * The inline styles.
	 *
	 * @type array
	 */
	private $inlineStyles;

	/**
	 * The meta attributes.
	 *
	 * @type array
	 */
	private $metaAttributes;

	/**
	 * The scripts.
	 *
	 * @type array
	 */
	private $scripts;

	/**
	 * The styles.
	 *
	 * @type array
	 */
	private $styles;

	/**
	 * The tags.
	 *
	 * @type array
	 */
	private $tags;

	/**
	 * The title.
	 *
	 * @type string
	 */
	private $title;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $id, array $configuration = null)
	{
		parent::__construct($id);

		$this->reset();

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Sets an additional inline script.
	 *
	 * @param string $script
	 *	The inline script file system alias.
	 *
	 * @param string $position
	 *	The script position.
	 *
	 * @param array $attributes
	 *	The script attributes.
	 */
	public function addInlineScript(string $script, string $position = 'head', array $attributes = null) : void
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
	 * @param string $position
	 *	The script position.
	 *
	 * @param array $attributes
	 *	The script attributes.
	 */
	public function addScript(string $location, string $position = 'head', array $attributes = null) : void
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
			foreach ($this->tags as $i => $tag)
			{
				$result .= $html->element($tag['tag'], $tag['attributes']) . PHP_EOL;
			}

			foreach ($this->metaAttributes as $i => $attributes)
			{
				$result .= $html->element('meta', $attributes) . PHP_EOL;
			}

			if ($this->title)
			{
				$result .= $html->element('title', null, $this->title) . PHP_EOL;
			}

			foreach ($this->styles as $i => $style)
			{
				$result .= 

					$html->element
					(
						'link', 
						$html->merge
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

					$html->element
					(
						'style', 
						$html->merge([ 'type' => 'text/css' ], $style['attributes']), 
						(new View((new Alias($style['style']))->resolve('php')))->run(null, true),
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

					$html->element
					(
						'script', 
						$html->merge
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

					$html->element
					(
						'script', 
						$html->merge([ 'type' => 'text/javascript', 'language' => 'javascript' ], $script['attributes']), 
						(new View((new Alias($script['script']))->resolve('php')))->run(null, true),
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
		$this->inlineScripts = [];
		$this->inlineStyles = [];
		$this->metaAttributes = [];
		$this->scripts = [];
		$this->styles = [];
		$this->tags = [];
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
}