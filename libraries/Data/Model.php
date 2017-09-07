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
use \Lightbit\Data\Validation\Rule;
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
	 * The attributes.
	 *
	 * @type array
	 */
	private $attributes;

	/**
	 * The attributes errors.
	 *
	 * @type array
	 */
	private $attributesErrors;

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
	public function __construct(string $scenario = 'default', array $attributes = null, array $configuration = null)
	{
		$this->attributesErrors = [];
		$this->scenario = $scenario;

		if ($configuration)
		{
			$this->configure($configuration);
		}

		if ($attributes)
		{
			ObjectHelper::setAttributes($this, $attributes);
		}
	}

	/**
	 * Adds an attribute error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $message
	 *	The attribute error message.
	 */
	public final function addAttributeError(string $attribute, string $message) : void
	{
		$this->attributesErrors[$attribute][] = $message;
	}

	/**
	 * Adds an attribute error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param array $messages
	 *	The attribute error messages.
	 */
	public final function addAttributeErrors(string $attribute, array $messages) : void
	{
		$this->attributesErrors[$attribute] = isset($this->attributesErrors[$attribute])
			? array_merge($this->attributesErrors[$attribute], $messages)
			: $messages;
	}

	/**
	 * Performs a commit.
	 */
	public function commit() : void
	{
		$this->attributes = $this->getAttributes();
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
	public function getAttribute(string $attribute) // : mixed
	{
		return ObjectHelper::getAttribute($this, $attribute);
	}

	/**
	 * Gets the attribute errors.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return array
	 *	The attribute errors.
	 */
	public final function getAttributeErrors(string $attribute) : array
	{
		if (isset($this->attributesErrors[$attribute]))
		{
			return $this->attributesErrors[$attribute];
		}

		return [];
	}

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public final function getAttributes() : array
	{
		return ObjectHelper::getAttributes($this, $this->getAttributesName());
	}

	/**
	 * Gets the attributes with update.
	 *
	 * If a commit was performed prior to invoking this function, the
	 * attributes that have been modified since then will be returned as an
	 * associative array. However, if a commit was not performed, all
	 * attributes will be returned instead.
	 *
	 * @param bool $inverse
	 *	When set, the original attributes are returned instead.
	 *
	 * @return array
	 *	The difference.
	 */
	public final function getAttributesWithUpdate(bool $inverse = false) : array
	{
		if (isset($this->attributes))
		{
			$result = [];

			if ($inverse)
			{
				foreach ($this->getAttributes() as $attribute => $value)
				{
					if ($value !== $this->attributes[$attribute])
					{
						$result[$attribute] = $this->attributes[$attribute];
					}
				}
			}
			else
			{
				foreach ($this->getAttributes() as $attribute => $value)
				{
					if ($value !== $this->attributes[$attribute])
					{
						$result[$attribute] = $value;
					}
				}
			}

			return $result;
		}

		return $this->getAttributes();
	}

	/**
	 * Gets the attributes errors.
	 *
	 * @return array
	 *	The attributes errors.
	 */
	public final function getAttributesErrors() : array
	{
		return $this->attributesErrors;
	}

	/**
	 * Gets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public final function getAttributesName() : array
	{
		static $attributesName;

		if (!isset($attributesName))
		{
			$attributesName = [];

			foreach ((new \ReflectionClass(static::class))->getProperties() as $i => $property)
			{
				if ($property->isPublic() && !$property->isStatic())
				{
					$attributesName[] = $property->getName();
				}
			}
		}

		return $attributesName;
	}

	/**
	 * Gets the rules.
	 *
	 * @return array
	 *	The rules.
	 */
	public final function getRules() : array
	{
		static $rules;

		if (!isset($rules))
		{
			$rules = [];
			$schema = $this->getSchema();

			if (isset($schema['rules']))
			{
				foreach ($schema['rules'] as $id => $rule)
				{
					$rules[] = Rule::create($this, $id, $rule);
				}
			}
		}

		return $rules;
	}

	/**
	 * Gets the safe attributes name.
	 *
	 * @return array
	 *	The safe attributes name.
	 */
	public final function getSafeAttributesName() : array
	{
		static $safeAttributesName;

		if (!isset($safeAttributesName[$this->scenario]))
		{
			$matches = [];

			foreach ($this->getRules() as $i => $rule)
			{
				if ($rule->hasScenario($this->scenario) && $rule->isSafe())
				{
					$matches[] = $rule->getAttributesName();
				}
			}
			
			$safeAttributesName[$this->scenario] = $matches 
				? array_unique(array_merge(...$matches))
				: [];
		}

		return $safeAttributesName[$this->scenario];
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
	 * Gets the schema.
	 *
	 * @return array
	 *	The schema.
	 */
	protected final function getSchema() : array
	{
		static $schema;

		if (!isset($schema))
		{
			$schema = $this->schema();
		}

		return $schema;
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
	 * Checks if an attribute has an error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasAttributeError(string $attribute) : bool
	{
		return isset($this->attributesErrors[$attribute]);
	}

	/**
	 * Checks for attributes errors.
	 *
	 * @param array $attributes
	 *	The attributes names.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasAttributesErrors(string $attributes = null) : bool
	{
		if (isset($attributes))
		{
			return !!array_intersect
			(
				$attributes,
				array_keys($this->attributesErrors)
			);
		}

		return !!$this->attributesErrors;
	}

	/**
	 * Imports the attributes.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 *
	 * @return bool
	 *	The result.
	 */
	public function import(array $attributes) : bool
	{
		$this->onImport($attributes);

		foreach ($this->getRules() as $i => $rule)
		{
			$rule->export($attributes);
		}

		$this->onAfterValidate();
		$this->onAfterImport($attributes);

		return !$this->attributesErrors;
	}

	/**
	 * Checks if an attribute is available.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isAttributeAvailable(string $attribute) : bool
	{
		return in_array($attribute, $this->getAttributesName());
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
	public final function isScenario(string ...$scenario) : bool
	{
		return in_array($this->scenario, ...$scenario);
	}

	/**
	 * Performs a rollback.
	 */
	public function rollback() : void
	{
		if (!isset($this->attributes))
		{
			throw Exception(sprintf('Can not rollback, no commit: class "%s"', static::class));
		}

		ObjectHelper::setAttributes($this, $this->attributes);
	}

	/**
	 * Creates the schema.
	 *
	 * Please note this method must be deterministic as its result is meant to
	 * be processed for use throughout multiple instances.
	 *
	 * @return array
	 *	The schema.
	 */
	protected function schema() : array
	{
		return [];
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
	 * Sets an attribute error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $message
	 *	The attribute error message.
	 *
	 * @param bool $merge
	 *	The attribute error merge flag.
	 */
	public final function setAttributeError(string $attribute, string $message, bool $merge = true) : void
	{
		if ($merge)
		{
			$this->attributesErrors[$attribute][] = $message;
		}
		else
		{
			$this->attributesErrors[$attribute] = [ $message ];
		}
	}

	/**
	 * Sets the attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public final function setAttributes(array $attributes) : void
	{
		ObjectHelper::setAttributes($this, $attributes);
	}

	/**
	 * Sets an attributes error.
	 *
	 * @param array $attributesError
	 *	The attributes error.
	 *
	 * @param bool $merge
	 *	The attributes error merge flag.
	 */
	public function setAttributesError(array $attributesError, bool $merge = true) : void
	{
		if (!$merge)
		{
			$this->attributesErrors = [];
		}

		foreach ($attributesErrors as $attribute => $message)
		{
			$this->addAttributeError($attribute, $message);
		}
	}

	/**
	 * Sets an attributes errors.
	 *
	 * @param array $attributesErrors
	 *	The attributes errors.
	 *
	 * @param bool $merge
	 *	The attributes errors merge flag.
	 */
	public function setAttributesErrors(array $attributesErrors, bool $merge = true) : void
	{
		if (!$merge)
		{
			$this->attributesErrors = [];
		}

		foreach ($attributesErrors as $attribute => $messages)
		{
			$this->addAttributeErrors($attribute, $messages);
		}
	}

	/**
	 * Validates the model according to the applicable rules.
	 *
	 * If an attribute requires transformation, the new value is set once
	 * the original passes validation.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function validate() : bool
	{
		$result = true;

		foreach ($this->getRules() as $i => $rule)
		{
			if (!$rule->validate())
			{
				$result = false;
			}
		}
	}

	/**
	 * On After Import.
	 *
	 * This method is called during the import procedure, after the rules
	 * export procedures complete.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 */
	protected function onAfterImport(array $attributes) : void
	{
		$this->raise('data.model.import.after', $this, $attributes);
	}

	/**
	 * On After Validate.
	 *
	 * This method is called during the validation procedure, after the rules
	 * validation procedures complete.
	 */
	protected function onAfterValidate() : void
	{
		$this->raise('data.model.validate.after', $this);
	}

	/**
	 * On Import.
	 *
	 * This method is called during the import procedure, before the rules
	 * export procedures complete.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 */
	protected function onImport(array $attributes) : void
	{
		$this->raise('data.model.import', $this, $attributes);
	}

	/**
	 * On Validate.
	 *
	 * This method is called during the validation procedure, before the rules
	 * validation procedures complete.
	 */
	protected function onValidate() : void
	{
		$this->raise('data.model.validate', $this);
	}
}