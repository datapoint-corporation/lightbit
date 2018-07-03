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

namespace Lightbit\Html\Composition;

use \Lightbit\Data\Filtering\FilterProvider;
use \Lightbit\Data\Filtering\FilterFactoryException;
use \Lightbit\Data\Html\Composition\HtmlComposerException;

use \Lightbit\Html\Composition\IHtmlComposer;
use \Lightbit\Configuration\IConfiguration;

/**
 * HtmlComposer.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlComposer implements IHtmlComposer
{
	/**
	 * The void element tag map.
	 *
	 * @var array
	 */
	private const VOID_ELEMENT_TAG_MAP = [
		'area' => true,
		'base' => true,
		'br' => true,
		'col' => true,
		'embed' => true,
		'hr' => true,
		'img' => true,
		'input' => true,
		'keygen' => true,
		'link' => true,
		'menuitem' => true,
		'meta' => true,
		'param' => true,
		'source' => true,
		'track' => true,
		'wbr' => true
	];

	/**
	 * The active element tag list.
	 *
	 * @var array
	 */
	private $activeElementTagList;

	/**
	 * Constructor.
	 *
	 * @param IConfiguration $configuration
	 *	The composer configuration.
	 */
	public function __construct(IConfiguration $configuration = null)
	{
		$this->activeElementTagList = [];

		if ($configuration)
		{
			$configuration->accept($this, []);
		}
	}

	/**
	 * Composes an element attribute markup.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param array $attributeMap
	 *	The attribute map.
	 *
	 * @return string
	 *	The markup.
	 */
	public function attribute(array $attributeMap) : string
	{
		$html = '';

		foreach ($this->map($attributeMap) as $property => $attribute)
		{
			if (is_bool($attribute))
			{
				if ($attribute)
				{
					$html .= (' ' . $this->encode($property));
				}
			}
			else
			{
				$filter;

				try
				{
					$filter = FilterProvider::getInstance()->getFilter(
						lbstypeof($attribute)
					);
				}
				catch (FilterFactoryException $e)
				{
					throw new HtmlComposerException(
						$this,
						sprintf(
							'Can not compose attribute, type filter creation failure: "%s"',
							lbstypeof($attribute)
						),
						$e
					);
				}

				$html .= (
					(' ' . $this->encode($property) . '="') .
					$this->encode($filter->compose($attribute)) .
					'"'
				);
			}
		}

		return $html;
	}

	/**
	 * Composes an element beginning markup.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributeMap
	 *	The element attribute map.
	 *
	 * @return string
	 *	The markup.
	 */
	public function begin(string $tag, array $attributeMap = null) : string
	{
		$this->activeElementTagList[] = $tag;

		$html = ('<' . $this->encode($tag));

		if ($attributeMap)
		{
			$html .= $this->attribute($attributeMap);
		}

		$html .= '>';

		return $html;
	}

	/**
	 * Decodes text.
	 *
	 * @param string $text
	 *	The text to decode.
	 *
	 * @return string
	 *	The text.
	 */
	public function decode(string $text) : string
	{
		return htmlspecialchars_decode($text);
	}

	/**
	 * Composes an element.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributeMap
	 *	The element attribute map.
	 *
	 * @return string
	 *	The markup.
	 */
	public function element(string $tag, array $attributeMap = null) : string
	{
		$html = ('<' . $this->encode($tag));

		if ($attributeMap)
		{
			$html .= $this->attribute($attributeMap);
		}

		if (isset(self::VOID_ELEMENT_TAG_MAP[$tag]))
		{
			$html .= ' />';
		}
		else
		{
			$html .= ('></' . $this->encode($tag) . '>');
		}

		return $html;
	}

	/**
	 * Encodes text.
	 *
	 * @param string $text
	 *	The text to encode.
	 *
	 * @return string
	 *	The markup.
	 */
	public function encode(string $text) : string
	{
		return htmlspecialchars($text, (ENT_HTML5 | ENT_IGNORE | ENT_QUOTES), 'UTF-8', true);
	}

	/**
	 * Composes an element ending markup.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @return string
	 *	The markup.
	 */
	public function end() : string
	{
		if ($this->activeElementTagList)
		{
			return ('</' . $this->encode(array_pop($this->activeElementTagList)) . '>');
		}

		throw new HtmlComposerException($this, sprintf(
			'Can not compose element ending tag, there is no open element'
		));
	}

	/**
	 * Creates an single attribute map by merging all given maps together,
	 * transforming attributes as applicable.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if attribute mapping fails.
	 *
	 * @param array $attributeMap
	 *	The attribute map.
	 *
	 * @return array
	 *	The attribute map.
	 */
	public function map(array ...$attributeMap) : array
	{
		$finalAttributeMap = [];
		$finalAttributeMapClassMap = [];

		foreach ($attributeMap as $i => $attributeMapListItem)
		{
			foreach ($attributeMapListItem as $property => $attribute)
			{
				if (is_string($property) && $property)
				{
					if ($property[0] === '@')
					{
						continue;
					}
					if ($property === 'class')
					{
						if (is_array($attribute))
						{
							foreach ($attribute as $class => $inclusion)
							{
								if (is_bool($inclusion))
								{
									$finalAttributeMapClassMap[$class] = $inclusion;
								}
								else if (is_callable($inclusion))
								{
									$finalAttributeMapClassMap[$class] = !!call_user_func($inclusion, $class);
								}
								else
								{
									$finalAttributeMapClassMap[$class] = !!$inclusion;
								}
							}
						}
						else if (is_string($attribute))
						{
							foreach (preg_split('%\\s+%', $attribute, -1, PREG_SPLIT_NO_EMPTY) as $i => $class)
							{
								$finalAttributeMapClassMap[$class] = true;
							}
						}
					}
					else
					{
						$finalAttributeMap[$property] = $attribute;
					}
				}
				else if (is_int($property))
				{
					if (is_string($attribute))
					{
						$finalAttributeMap[$attribute] = true;
					}
					else
					{
						throw new HtmlComposerException($this, sprintf(
							'Can not map non-string attribute with numeric index: "%s"',
							$property
						));
					}
				}
			}
		}

		if ($finalAttributeMapClassMap)
		{
			$finalAttributeMapClassList = [];

			foreach ($finalAttributeMapClassMap as $class => $inclusion)
			{
				if ($inclusion)
				{
					$finalAttributeMapClassList[] = $class;
				}
			}

			if ($finalAttributeMapClassList)
			{
				$finalAttributeMap['class'] = implode(' ', $finalAttributeMapClassList);
			}
		}

		return $finalAttributeMap;
	}

	/**
	 * Composes a text sequence.
	 *
	 * @throws HtmlComposerException
	 *	Thrown if composition fails.
	 *
	 * @param string $text
	 *	The text to compose.
	 *
	 * @return string
	 *	The markup.
	 */
	public function text(string $text) : string
	{
		return $this->encode($text);
	}
}
