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

namespace Lightbit\Data;

use \Lightbit\Base\Element;
use \Lightbit\Data\IModel;
use \Lightbit\Helpers\ObjectHelper;

/**
 * IModel.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Model extends Element implements IModel
{
	/**
	 * The attributes name.
	 *
	 * @type array
	 */
	private static $attributesName = [];

	/**
	 * Creates a model instance.
	 *
	 * @param string $scenario
	 *	The model scenario.
	 *
	 * @return IModel
	 *	The model.
	 */
	public static function model(string $scenario = 'default') : IModel
	{
		return new static($scenario);
	}

	/**
	 * The scenario.
	 *
	 * @type string
	 */
	private $scenario;

	/**
	 * Constructor.
	 *
	 * @param string $scenario
	 *	The model scenario.
	 *
	 * @param array $attributes
	 *	The model attributes.
	 *
	 * @param array $configuration
	 *	The model configuration.
	 */
	public function __construct(string $scenario, array $attributes = null, array $configuration = null)
	{
		$this->scenario = $scenario;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}

		if ($attributes)
		{
			ObjectHelper::setAttributes($this, $attributes);
		}
	}

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public final function getAttributes() : array
	{
		$result = [];

		foreach ($this->getAttributesName() as $i => $attribute)
		{
			$result[$attribute] = ObjectHelper::getAttribute($this, $attribute);
		}

		return $result;
	}

	/**
	 * Gets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public final function getAttributesName() : array
	{
		if (!isset(self::$attributesName[static::class]))
		{
			self::$attributesName[static::class] = [];

			foreach ((new \ReflectionClass(static::class))->getProperties() as $i => $property)
			{
				if ($property->isPublic() && !$property->isStatic())
				{
					self::$attributesName[static::class][] = $attributesName;
				}
			}
		}

		return self::$attributesName[static::class];
	}

	/**
	 * Gets the scenario.
	 *
	 * @return string
	 *	The scenario.
	 */
	public final function getScenario() : string
	{
		return $this->scenario;
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
	public final function hasAttribute(string $attribute) : bool
	{
		return ObjectHelper::hasAttribute($this, $attribute);
	}

	/**
	 * Checks the scenario.
	 *
	 * @param string $scenario
	 *	The scenarios to match against.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isScenario(string ...$scenario) : bool
	{
		foreach ($scenario as $i => $subject)
		{
			if ($subject == $this->scenario)
			{
				return true;
			}
		}

		return false;
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
	public final function setAttribute(string $attribute, $value) : void
	{
		ObjectHelper::setAttribute($this, $attribute, $value);
	}

	/**
	 * Sets the attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public final function setAttributes(array $attributes) : void
	{
		ObjectHelper::setAttribute($this, $attributes);
	}
}