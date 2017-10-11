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

function __html_begin(string $tag, array $attributes = null) : string
{
	$result = '<' . __html_encode($tag);

	if ($attributes)
	{
		$result .= __html_attribute_array($attributes);
	}

	return $result . '>';
}

function __html_body_begin(array $attributes = null) : string
{
	$context = __context();
	$theme = $context->getTheme();

	if ($theme)
	{
		return __html_begin('body', $attributes);
	}

	return __html_begin('body', $attributes);
}

function __html_body_end() : string
{
	$context = __context();
	$document = $context->getHtmlDocument();

	return $document->inflate('body') . __html_end('body');
}

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

function __html_form_begin(array $attributes = null) : string
{
	if (!isset($attributes))
	{
		$attributes = [];
	}

	// Set the default action
	if (!isset($attributes['action']))
	{
		$attributes['action'] = __url(__action()->getRoute());
	}

	// Set action as the resolved url.
	else if (is_array($attributes['action']))
	{
		$attributes['action'] = __url($attributes['action']);
	}

	// Set the default method
	if (!isset($attributes['method']))
	{
		$attributes['method'] = 'POST';
	}

	return __html_begin('form', $attributes);
}

function __html_form_end() : string
{
	return __html_end('form');
}

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