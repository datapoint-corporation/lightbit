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
use \Lightbit\Data\Parsing\BooleanParser;
use \Lightbit\Data\Parsing\FloatParser;
use \Lightbit\Data\Parsing\ObjectParser;
use \Lightbit\Data\Parsing\StringParser;
use \Lightbit\Data\Parsing\ParserException;
use \Lightbit\Data\IModel;
use \Lightbit\Html\IHtmlComposer;

/**
 *
 */
class HtmlComposer extends Component implements IHtmlComposer
{
	/**
	 * The void elements tag.
	 *
	 * @var array
	 */
	private const ELEMENT_VOID = 
	[
		'area',
		'base',
		'br',
		'col',
		'embed',
		'hr',
		'img',
		'input',
		'keygen',
		'link',
		'menuitem',
		'meta',
		'param',
		'source',
		'track',
		'wbr'
	];

	/**
	 * Composes an attribute.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param mixed $attribute
	 *	The attribute.
	 */
	public final function attribute(string $property, $attribute) : string
	{
		if (isset($attribute))
		{
			if (is_bool($attribute))
			{
				return ($attribute ? $this->text($property) : '');
			}

			$composition;

			try
			{
				switch (gettype($attribute))
				{
					case 'boolean':
						$composition = (new BooleanParser())->compose($attribute);
						break;

					case 'double':
					case 'float':
						$composition = (new FloatParser())->compose($attribute);
						break;

					case 'integer':
						$composition = (new IntegerParser())->compose($attribute);
						break;

					case 'string':
						$composition = (new StringParser())->compose($attribute);
						break;

					case 'object':
						$composition = (new ObjectParser(get_class($attribute)))->compose($attribute);
						break;

					default:
						throw new HtmlComposerException($this, sprintf('Can not compose attribute, unsupported data type: "%s"', $property));
				}
			}
			catch (ParserException $e)
			{
				throw new HtmlComposerException($this, sprintf('Can not compose attribute, parser exception: "%s"', $property));
			}

			return $this->text($property) . '="' . $this->text($composition) . '"';
		}

		return '';
	}

	/**
	 * Composes the attributes.
	 *
	 * @param array $attributes
	 *	The attributes
	 *
	 * @return string
	 *	The result.
	 */
	public final function attributes(array $attributes) : string
	{
		$tokens = [];

		foreach ($attributes as $property => $attribute)
		{
			if (is_string($property) && $property)
			{
				if ($token = $this->attribute($property, $attribute))
				{
					$tokens[] = $token;
				}
			}
		}

		sort($tokens, SORT_STRING);
		return implode(' ', $tokens);
	}

	/**
	 * Composes an element opening tag.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @return string
	 *	The result.
	 */
	public function begin(string $tag, array $attributes = null) : string
	{
		$result = '<' . $tag;

		if ($attributes && $suffix = $this->attributes($attributes))
		{
			$result .= ' ' . $suffix;
		}

		return $result . '>';
	}

	/**
	 * Composes an element.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @param array $attributes
	 *	The element attributes.
	 *
	 * @param string $content
	 *	The element content.
	 *
	 * @param bool $encode
	 *	The element content encoding flag.
	 *
	 * @return string
	 *	The result.
	 */
	public function element(string $tag, array $attributes = null, string $content = null, bool $encode = true) : string
	{
		$result = '<' . $tag;

		if ($attributes && $suffix = $this->attributes($attributes))
		{
			$result .= ' ' . $suffix;
		}

		if ($content)
		{
			$result .= '>' . ($encode ? $this->text($content) : $content);
		}

		else if (in_array($tag, self::ELEMENT_VOID))
		{
			return $result .' />';
		}

		else
		{
			$result .= '>';
		}

		return $result . '</' . $tag . '>';
	}

	/**
	 * Composes an element ending tag.
	 *
	 * @param string $tag
	 *	The element tag.
	 *
	 * @return string
	 *	The result.
	 */
	public function end(string $tag) : string
	{
		return '</' . $tag . '>';
	}

	/**
	 * Composes elements by position.
	 *
	 * @param string $position
	 *	The elements position.
	 *
	 * @return string
	 *	The markup.
	 */
	public function inflate(string $position) : string
	{
		$context = $this->getContext();
		$document = $context->getHtmlDocument();
		$result = '';

		if ($position === 'head')
		{
			// Charset.
			$result .= $this->element('meta', [ 'charset' => 'utf8' ]) . PHP_EOL;
			$result .= $this->element('meta', [ 'http-equiv' => 'text/html; charset=utf-8' ]) . PHP_EOL;

			// Tags.
			foreach ($document->getTags() as $i => $tag)
			{
				$result .= $this->element($tag->getName(), $tag->getAttributes()) . PHP_EOL;
			}

			// Title.
			$result .= $this->element('title', null, $document->getTitle()) . PHP_EOL;

			// Styles.
			foreach ($document->getStyles() as $i => $style)
			{
				$result .= $this->element
				(
					'link',
					$this->merge
					(
						[ 'href' => $style->getLocation(), 'rel' => 'stylesheet', 'type' => 'text/css' ],
						$style->getAttributes()
					)
				)

				. PHP_EOL;
			}

			// Inline styles.
			foreach ($document->getInlineStyles() as $i => $style)
			{
				$result .= $this->element
				(
					'style',
					$this->merge
					(
						[ 'type' => 'text/css' ],
						$style->getAttributes()
					),

					$style->getContent(),
					false
				)

				. PHP_EOL;
			}
		}

		// Scripts
		$scripts = $document->getScripts();

		if (isset($scripts[$position]))
		{
			foreach ($scripts[$position] as $i => $script)
			{
				$result .= $this->element
				(
					'script',
					$this->merge
					(
						[ 'language' => 'javascript', 'type' => 'text/javascript' ],
						$script->getAttributes(),
						[ 'src' => $script->getLocation() ]
					)
				)

				. PHP_EOL;
			}
		}

		// Inline scripts.
		$scripts = $document->getInlineScripts();

		if (isset($scripts[$position]))
		{
			foreach ($scripts[$position] as $i => $script)
			{
				$result .= $this->element
				(
					'script',
					$this->merge
					(
						[ 'language' => 'javascript', 'type' => 'text/javascript' ],
						$script->getAttributes()
					),

					$script->getContent(),
					false
				)

				. PHP_EOL;
			}
		}

		return $result;
	}

	/**
	 * Composes an input identifier.
	 *
	 * @param IModel $model
	 *	The input model.
	 *
	 * @param string $attribute
	 *	The input model attribute name.
	 *
	 * @return string
	 *	The result.
	 */
	public function id(IModel $model, string $attribute) : string
	{
		return (new StringSlugConversion($attribute, false))->toSlug();
	}

	/**
	 * Creates a merge between attributes.
	 *
	 * @param array $attributes
	 *	The attributes to merge.
	 *
	 * @return array
	 *	The result.
	 */
	public function merge(?array ...$attributes) : array
	{
		$result = [];
		$class = [];

		foreach ($attributes as $i => $argument)
		{
			if ($argument)
			{
				if (isset($argument['class']) && is_string($argument['class']))
				{
					$class = array_merge($class, explode(' ', $argument['class']));
				}

				$result += $argument;
			}
		}

		if ($class)
		{
			$result['class'] = implode(' ', array_unique($class));
		}

		return $result;
	}

	/**
	 * Composes an input name.
	 *
	 * @param IModel $model
	 *	The input model.
	 *
	 * @param string $attribute
	 *	The input model attribute name.
	 *
	 * @return string
	 *	The result.
	 */
	public function name(IModel $model, string $attribute) : string
	{
		return (new StringSlugConversion($attribute, false))->toSlug();
	}

	/**
	 * Composes text.
	 *
	 * @param string $content
	 *	The content.
	 *
	 * @return string
	 *	The result.
	 */
	public function text(string $content) : string
	{
		return htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}
}