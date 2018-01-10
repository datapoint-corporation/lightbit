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

use \Lightbit\Base\Object;
use \Lightbit\Base\ITheme;
use \Lightbit\Data\IModel;

/**
 * Creates the markup for a single html attribute.
 *
 * @param string $property
 *	The property name.
 *
 * @param mixed $attribute
 *	The attribute.
 *
 * @return string
 *	The markup.
 */
function __html_attribute(string $property, $attribute) : string
{
	if ($attribute)
	{
		if (is_bool($attribute))
		{
			return ($attribute ? (' ' . __html_encode($property)) : '');
		}

		if ($attribute instanceof Object)
		{
			$attribute = __html_encode(__context()->getSlugManager()->compose($attribute));
		}
		else
		{
			$attribute = __html_encode(__type_to_string($attribute));
		}

		if ($attribute)
		{
			return ' ' . __html_encode($property) . '="' . $attribute . '"';
		}
	}

	return '';
}

/**
 * Creates the markup for an array of html attributes.
 *
 * @param array $attributes
 *	The associative array of attributes, indexed by property name.
 *
 * @return string
 *	The markup.
 */
function __html_attribute_array(?array ...$attributes) : string
{
	$attributes = __html_attribute_array_merge(...$attributes);

	$result = '';

	if ($attributes)
	{
		foreach ($attributes as $property => $attribute)
		{
			if ($m = __html_attribute($property, $attribute))
			{
				$result .= $m;
			}
		}
	}

	return $result;
}

/**
 * Combines multiple arrays of html attributes into one.
 *
 * @param array $attributes
 *	The associative array of attributes, indexed by property name.
 *
 * @return array
 *	The result.
 */
function __html_attribute_array_merge(?array ...$attributes) : array
{
	$result = [];
	$class = [];

	foreach ($attributes as $i => $argument)
	{
		if ($argument)
		{
			if ($c = __map_get($argument, '?string', 'class'))
			{
				$class = array_merge($class, explode(' ', $c));
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
 * Creates the markup for a html element opening tag.
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
function __html_begin(string $tag, array $attributes = null) : string
{
	$result = '<' . __html_encode($tag);

	if ($attributes)
	{
		$result .= __html_attribute_array($attributes);
	}

	return $result . '>';
}

/**
 * Creates the body html element opening tag.
 *
 * The body element base attributes are defined by the html adapter linked
 * to the currently active context.
 *
 * @param array $attributes
 *	The body attributes.
 *
 * @return string
 *	The markup.
 */
function __html_body_begin(array $attributes = null) : string
{
	$context = __context();
	$html = $context->getHtmlAdapter();

	return __html_begin('body', __html_attribute_array_merge($html->getBodyAttributes(), $attributes));
}

/**
 * Creates the markup for a body html element closing tag.
 *
 * @return string
 *	The markup.
 */
function __html_body_end() : string
{
	return '</body>';
}

/**
 * Creates the markup for a html comment.
 *
 * @param string $content
 *	The comment content.
 *
 * @return string
 *	The markup.
 */
function __html_comment(string $content) : string
{
	return '<!--' . __html_encode($content) . ' //-->';
}

/**
 * Decodes the hypertext content.
 *
 * @param string $content
 *	The hypertext content.
 *
 * @return string
 *	The result.
 */
function __html_decode(string $content) : string
{
	return htmlspecialchars_decode($content, (ENT_QUOTES | ENT_HTML5));
}

/**
 * Creates the html doctype declaration.
 *
 * @return string
 *	The markup.
 */
function __html_doctype() : string
{
	return '<!DOCTYPE html>'; 
}

/**
 * Creates the document html element opening tag.
 *
 * @param array $attributes
 *	The html element attributes.
 *
 * @return string
 *	The markup.
 */
function __html_document_begin(array $attributes = null) : string
{
	$context = __context();
	$html = $context->getHtmlAdapter();

	return __html_doctype() . PHP_EOL . __html_begin('html', __html_attribute_array_merge($html->getDocumentAttributes(), $attributes));
}

/**
 * Creates the document html element closing tag.
 *
 * @return string
 *	The markup.
 */
function __html_document_end() : string
{
	return '</html>';
}

/**
 * Creates the markup for a html element.
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
 * @param bool $encode
 *	The element content encode flag.
 *
 * @return string
 *	The markup.
 */
function __html_element(string $tag, array $attributes = null, string $content = null, bool $encode = true) : string
{
	$result = '<' . __html_encode($tag);

	if ($attributes)
	{
		$result .= __html_attribute_array($attributes);
	}

	if (!$content && __html_element_is_void($tag))
	{
		return $result . ' />';
	}

	$result .= '>';

	if ($content)
	{
		$result .= ($encode ? __html_encode($content) : $content);
	}

	return $result . __html_end($tag);
}

/**
 * Checks if an element is void.
 *
 * @param string $tag
 *	The element tag name.
 *
 * @return bool
 *	The result.
 */
function __html_element_is_void(string $tag) : bool
{
	static $voids =
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

	return isset($voids[$tag]);
}

/**
 * Creates the markup for an html element ending tag.
 *
 * @param string $tag
 *	The element tag name.
 *
 * @return string
 *	The markup.
 */
function __html_end(string $tag) : string
{
	return '</' . __html_encode($tag) . '>';
}

/**
 * Encodes content to hypertext.
 *
 * @param string $content
 *	The content.
 *
 * @return string
 *	The result.
 */
function __html_encode(string $content) : string
{
	return htmlspecialchars($content, (ENT_QUOTES | ENT_HTML5), 'UTF-8');
}

/**
 * Creates a basic html form.
 *
 * This method was implemented to ease the development and testing of features
 * that depend on user submitted data and is not meant to be used in a 
 * production scenario.
 *
 * @param IModel $model
 *	The model.
 *
 * @param string $method
 *	The method.
 *
 * @param array $action
 *	The route resolving to a controller action.
 *
 * @return string
 *	The markup.
 */
function __html_form(IModel $model, string $method = 'POST', array $action = null) : string
{
	$html = __context()->getHtmlAdapter();

	$result = __html_form_begin([ 'action' => $action, 'method' => $method ]);

	foreach ($model->getSafeAttributesName() as $i => $attribute)
	{
		$id = $html->getActiveInputID($model, $attribute);
		$name = $html->getActiveInputName($model, $attribute);

		$result .= __html_begin('fieldset');

		$result .= __html_element
		(
			'label',
			[ 'for' => $id ],
			$model->getAttributeLabel($attribute),
			false
		);

		$result .= __html_element('br');

		$result .= __html_element
		(
			'input',
			[ 
				'id' => $id, 
				'name' => $name, 
				'type' => 'text', 
				'value' => $model->getAttribute($attribute),
				'placeholder' => $model->getAttributePlaceholder($attribute)
			]
		);

		$messages = $model->getAttributeErrors($attribute);

		if ($messages)
		{
			$result .= __html_begin('ul');

			foreach ($messages as $i => $message)
			{
				$result .= __html_element('li', null, $message);
			}

			$result .= __html_end('ul');
		}

		$result .= __html_end('fieldset');
	}

	$result .= __html_element('input', [ 'type' => 'submit', 'value' => 'Submit' ]);

	$result .= __html_form_end();
	return $result;
}

/**
 * Creates the head html element.
 *
 * @return string
 *	The markup.
 */
function __html_head() : string
{
	$context = __context();
	$document = $context->getHtmlDocument();

	return __html_element
	(
		'head',
		null,
		$document->inflate('head'),
		false
	);
}