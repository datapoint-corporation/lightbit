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

namespace Lightbit\Data\Validation;

use \Lightbit\Base\Element;
use \Lightbit\Data\IModel;
use \Lightbit\Data\Validation\IRule;
use \Lightbit\Helpers\ObjectHelper;

/**
 * Rule.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Rule extends Element implements IRule
{
	/**
	 * The attributes name.
	 *
	 * @type array
	 */
	private $attributesName;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * The model.
	 *
	 * @type string
	 */
	private $model;

	/**
	 * The scenarios.
	 *
	 * @type array
	 */
	private $scenarios;

	/**
	 * Constructor.
	 *
	 * @param IModel $model
	 *	The rule model.
	 *
	 * @param string $id
	 *	The rule identifier.
	 *
	 * @param array $configuration
	 *	The rule configuration.
	 */
	public function __construct(IModel $model, string $id, array $configuration = null)
	{
		$this->model = $model;
		$this->id = $id;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Assigns an attribute value.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param mixed $value
	 *	The attribute value.
	 */
	protected function assign(string $attribute, $value) : void
	{
		$this->validateAttribute($attribute, $value);
	}

	/**
	 * Exports attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public final function export(array $attributes) : void
	{
		if ($this->hasScenario($this->model->getScenario()))
		{
			foreach ($attributes as $attribute => $value)
			{
				if ($this->isSafe() && $this->hasAttribute($attribute))
				{
					$this->assign($attribute, $value);
				}
			}
		}
	}

	/**
	 * Gets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public final function getAttributesName() : array
	{
		if (!isset($this->attributesName))
		{
			return $this->attributesName;
		}

		return $this->model->getAttributesName();
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public final function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the model.
	 *
	 * @return IModel
	 *	The model.
	 */
	public final function getModel() : IModel
	{
		return $this->model;
	}

	/**
	 * Checks for an attribute applicability.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 */
	public final function hasAttribute(string $attribute) : bool
	{
		return in_array($attribute, $this->getAttributesName());
	}

	/**
	 * Checks for an scenario applicability.
	 *
	 * @param string $scenario
	 *	The scenario.
	 */
	public function hasScenario(string $scenario) : bool
	{
		return isset($this->scenarios)
			? in_array($scenario, $this->scenarios)
			: true;
	}

	/**
	 * Checks the rule safe flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isSafe() : bool
	{
		return true;
	}

	/**
	 * Sets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public final function setAttributesName(?array $attributes) : void
	{
		$this->attributes = isset($attributes)
			? array_intersect($this->model->getAttributesName(), $attributes)
			: $this->model->getAttributesName();

	}

	/**
	 * Sets the scenarios.
	 *
	 * @param array $scenarios
	 *	The scenarios.
	 */
	public final function setScenarios(?array $scenarios) : void
	{
		$this->scenarios = $scenarios;
	}

	/**
	 * Runs the validation procedure.
	 *
	 * @return bool
	 *	The validation result.
	 */
	public final function validate() : bool
	{
		$result = true;

		if ($this->hasScenario($this->model->getScenario()))
		{
			foreach ($this->getAttributesName() as $i => $attribute)
			{
				if (!$this->validateAttribute($attribute, $model->getAttribute($attribute)))
				{
					$result = false;
				}
			}
		}

		return $result;
	}

	/**
	 * Runs the validation procedure on a single attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param mixed $value
	 *	The attribute value.
	 *
	 * @return bool
	 *	The validation result.
	 */
	protected function validateAttribute(string $attribute, $value) : bool
	{
		return true;
	}
}