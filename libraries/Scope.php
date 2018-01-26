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

namespace Lightbit;

use \Lightbit\Data\Manipulation\StringCamelCaseConversion;

/**
 * Scope.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Scope
{
	/**
	 * The object.
	 *
	 * @var object
	 */
	private $object;

	/**
	 * Constructor.
	 *
	 * @param object $object
	 *	The scope object.
	 */
	public function __construct(object $object)
	{
		$this->object = $object;
	}

	/**
	 * Gets an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public function getAttribute(string $attribute)
	{
		return $this->object->{$attribute};
	}

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public function getAttributes(array $attributesName = null) : array
	{
		if (!isset($attributesName))
		{
			$attributesName = [];

			foreach ((new ReflectionClass($this->object))->getProperties() as $i => $property)
			{
				if ($property->isPublic() && !$property->isStatic())
				{
					$attributesName[] = $property->getName();
				}
			}
		}

		$attributes = [];

		foreach ($attributesName as $i => $attribute)
		{
			$attributes[$attribute] = $this->object->{$attribute};
		}

		return $attributes;
	}

	/**
	 * Checks if an attribute is set.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttribute(string $attribute) : bool
	{
		return isset($this->object->{$attribute});
	}

	/**
	 * Includes a script.
	 *
	 * @param string $path
	 *	The script path.
	 *
	 * @param array $variables
	 *	The script variables.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function include(string $path, array $variables = null)
	{
		return (new Script($path))->include($this->object, $variables);
	}

	/**
	 * Sets an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param mixed $value
	 *	The attribute value.
	 */
	public function setAttribute(string $attribute, $value) : void
	{
		$this->object->{$property} = $attribute;
	}

	/**
	 * Sets multiple attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public function setAttributes(array $attributes) : void
	{
		foreach ($attributes as $attribute => $value)
		{
			if (is_string($attribute) && $attribute && $attribute[0] !== '@')
			{
				$this->object->{$attribute} = $value;
			}
		}
	}

	/**
	 * Sets a property.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param mixed $value
	 *	The property value.
	 */
	public function setProperty(string $property, $value) : void
	{
		$method = 'set' . (new StringCamelCaseConversion($property))->toUpperCamelCase();
		$this->object->{$method}($value);
	}

	/**
	 * Configures the scope.
	 *
	 * @param array $configuration
	 *	The scope configuration.
	 */
	public function configure(array $configuration) : void
	{
		foreach ($configuration as $property => $attribute)
		{
			if ($property && is_string($property))
			{
				if ($property[0] !== '@')
				{
					$method = 'set' . (new StringCamelCaseConversion($property))->toUpperCamelCase();
					$this->object->{$method}($attribute);
				}
			}
		}
	}
}