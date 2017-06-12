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
use \Lightbit\Data\Validation\SafeRule;
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
	 * The rules class name.
	 *
	 * @type array
	 */
	private static $rulesClassName = 
	[
		'safe' => SafeRule::class
	];

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
	 * The snapshot.
	 *
	 * @type array
	 */
	private $snapshot;

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
	 * Commits any changes to the model attributes.
	 *
	 * @return array
	 *	The commit attributes snapshot.
	 */
	protected final function commit() : array
	{
		return $this->snapshot = $this->getAttributes();
	}

	/**
	 * Calculates the difference from the current state to the last commit,
	 * returning any attributes that have changed in between.
	 *
	 * @return array
	 *	The attributes that have changed in between.
	 */
	protected final function difference() : array
	{
		if (!$this->snapshot)
		{
			return $this->getAttributes();
		}

		$result = [];

		foreach ($this->getAttributes() as $attribute => $value)
		{
			if ($value !== $this->snapshot[$attribute])
			{
				$result[$attribute] = $value;
			}
		}

		return $result;
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
				foreach ($schema['rules'] as $i => $rule)
				{
					if (!isset($rule['@class']))
					{
						throw new Exception(sprintf('Model schema parse failure, rule class is missing: at rule "%s", property "@class"', $i));
					}

					$ruleClassName = isset(self::$rulesClassName[$rule['@class']])
						? self::$rulesClassName[$rule['@class']]
						: $rule['@class'];

					$rules[] = new $ruleClassName($this, $i, $rule);
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

			$safeAttributesName[$this->scenario] = array_unique(array_merge(...$matches));
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
	 * Imports the attributes.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 */
	public function import(array $attributes) : void
	{
		$this->onImport($attributes);

		foreach ($this->getRules() as $i => $rule)
		{
			$rule->export($attributes);
		}

		$this->onAfterValidate();
		$this->onAfterImport($attributes);
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
		return in_array($this->scenario, ...$scenario);
	}

	/**
	 * Performs a rollback operation on any changes from the last commit until
	 * the current state, regarding attributes.
	 */
	protected final function rollback() : void
	{
		if (!$this->snapshot)
		{
			throw Exception(sprintf('Can not rollback, commit not found: "%s"', static::class));
		}

		ObjectHelper::setAttributes($this, $this->snapshot);
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
	 * Sets the attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public final function setAttributes(array $attributes) : void
	{
		ObjectHelper::setAttribute($this, $attributes);
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