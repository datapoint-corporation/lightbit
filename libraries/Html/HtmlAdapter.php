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

use \Lightbit;
use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Base\Object;
use \Lightbit\Data\IModel;
use \Lightbit\Helpers\TypeHelper;
use \Lightbit\Html\IHtmlAdapter;

/**
 * HtmlAdapter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HtmlAdapter extends Component implements IHtmlAdapter
{
	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, $configuration);
	}

	/**
	 * Creates an active input name.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input name.
	 */
	protected function activeInputName(IModel $model, string $attribute) : string
	{
		$session = $this->getHttpSession();

		$hash = hash('md5', (Lightbit::VERSION . '/' . get_class($model) . '/' . $attribute));
		$id = 'lightbit.html.adapter.input.' . $hash;

		$result = $session->fetch($id);

		if (!$result)
		{
			$result = sprintf('%x', crc32(hash('md5', ($hash . '/' . $session->getGuid()))));
			$session->write($id, $result);
		}

		return $result;
	}

	/**
	 * Creates an active input identifier.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input identifier.
	 */
	protected function activeInputID(IModel $model, string $attribute) : string
	{
		$session = $this->getHttpSession();

		$hash = hash('md5', (Lightbit::VERSION . '/' . get_class($model) . '/' . $attribute));
		$id = 'lightbit.html.adapter.input.' . $hash . 'id';

		$result = $session->fetch($id);

		if (!$result)
		{
			$result = sprintf('%x', crc32(hash('md5', ($hash . '/' . $session->getGuid() . '/id'))));
			$session->write($id, $result);
		}

		return $result;
	}

	/**
	 * Inflates an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $value
	 *	The attribute value.
	 *
	 * @return string
	 *	The markup.
	 */
	public function attribute(string $attribute, $value) : string
	{
		if (isset($value))
		{
			$typeName = TypeHelper::getNameOf($value);

			if ($typeName === 'bool')
			{
				if ($value)
				{
					return strtr($this->escape($attribute), [ ' ' => '-' ]);
				}

				return '';
			}

			if ($typeName !== 'string')
			{
				$value = ($value instanceof Object)
					? $this->getSlugManager()->compose($value)
					: TypeHelper::toString($value);
			}
		
			return strtr($this->escape($attribute), [ ' ' => '-' ]) . '="' . $this->escape($value) . '"';
		}

		return '';
	}

	/**
	 * Inflates attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 *
	 * @return string
	 *	The markup.
	 */
	public function attributes(array $attributes) : string
	{
		$parts = '';

		foreach ($attributes as $attribute => $value)
		{
			$part = $this->attribute($attribute, $value);

			if ($part)
			{
				$parts .= ' ' . $part;
			}
		}

		return ($parts ? substr($parts, 1) : '');
	}

	/**
	 * Inflates the begin of an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @return string
	 *	The markup.
	 */
	public function begin(string $tag, array $attributes = null) : string
	{
		$result = '<' . $this->escape($tag);

		if ($attributes)
		{
			$markup = $this->attributes($attributes);

			if ($markup)
			{
				$result .= ' ' . $markup;
			}
		}

		return $result . '>';
	}

	/**
	 * Inflates a comment.
	 *
	 * @param string $content
	 *	The comment content.
	 *
	 * @return string
	 *	The markup.
	 */
	public function comment(string $content) : string
	{
		return '<!-- ' . $this->escape($content) . ' //-->';
	}

	/**
	 * Inflates an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @param string $content
	 *	The element content.
	 *
	 * @param bool $escape
	 *	The escape flag which, when set, will cause the content to be escaped
	 *	before being injected within the element.
	 *
	 * @return string
	 *	The markup.
	 */
	public function element(string $tag, array $attributes = null, string $content = null, bool $escape = true) : string
	{
		$result = '<' . $this->escape($tag);

		if ($attributes)
		{
			$markup = $this->attributes($attributes);

			if ($markup)
			{
				$result .= ' ' . $markup;
			}
		}

		if ($this->isVoidElementTag($tag) && !$content)
		{
			return $result . ' />';
		}

		if ($content && $escape)
		{
			$content = $this->escape($content);
		}

		return $result . '>' . $content . '</' . $this->escape($tag) . '>';
	}

	/**
	 * Inflates the end of an element.
	 *
	 * @param string $tag
	 *	The element tag name.
	 *
	 * @return string
	 *	The markup.
	 */
	public function end(string $tag) : string
	{
		return '</' . $this->escape($tag) . '>';
	}

	/**
	 * Escapes the given content.
	 *
	 * @param string $content
	 *	The content.
	 *
	 * @return string
	 *	The result.
	 */
	public function escape(string $content) : string
	{
		return htmlspecialchars($content);
	}

	/**
	 * Gets an active input identifier.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input identifier.
	 */
	public final function getActiveInputID(IModel $model, string $attribute) : string
	{
		static $activeInputsID = [];

		$className = get_class($model);

		if (!isset($activeInputsID[$className][$attribute]))
		{
			if (!isset($activeInputsID[$className]))
			{
				$activeInputsID[$className] = [];
			}

			$activeInputsID[$className][$attribute] = $this->activeInputID($model, $attribute);
		}

		return $activeInputsID[$className][$attribute];
	}

	/**
	 * Gets an active input name.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input name.
	 */
	public final function getActiveInputName(IModel $model, string $attribute) : string
	{
		static $activeInputsName = [];

		$className = get_class($model);

		if (!isset($activeInputsName[$className][$attribute]))
		{
			if (!isset($activeInputsName[$className]))
			{
				$activeInputsName[$className] = [];
			}

			$activeInputsName[$className][$attribute] = $this->activeInputName($model, $attribute);
		}

		return $activeInputsName[$className][$attribute];
	}

	/**
	 * Checks for a void element tag.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @return bool
	 *	The result.
	 */
	protected function isVoidElementTag(string $tag) : bool
	{
		static $voidElementsTag = 
		[
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

		return isset($voidElementsTag[strtolower($tag)]);
	}

	/**
	 * Merges attributes.
	 *
	 * @param array $attributes
	 *	The attributes to merge.
	 *
	 * @return array
	 *	The result.
	 */
	public function merge(...$attributes) : array
	{
		$result = [];
		
		$classNames = [];
		foreach ($attributes as $i => $node)
		{
			if ($node && is_array($node))
			{
				$result = $node + $result;

				if (isset($node['class']) && $node['class'])
				{
					$classNames = array_merge($classNames, explode(' ', $node['class']));
				}
			}
		}

		if ($classNames)
		{
			$result['class'] = implode(' ', array_unique($classNames));
		}

		return $result;
	}
}