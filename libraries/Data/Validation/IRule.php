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

use \Lightbit\Base\IElement;
use \Lightbit\Data\IModel;

/**
 * IRule.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IRule extends IElement
{
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
	public function __construct(IModel $model, string $id, array $configuration = null);

	/**
	 * Exports attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public function export(array $attributes) : void;

	/**
	 * Gets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public function getAttributesName() : array;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the model.
	 *
	 * @return IModel
	 *	The model.
	 */
	public function getModel() : IModel;

	/**
	 * Checks for an attribute applicability.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 */
	public function hasAttribute(string $attribute) : bool;

	/**
	 * Checks for an scenario applicability.
	 *
	 * @param string $scenario
	 *	The scenario.
	 */
	public function hasScenario(string $scenario) : bool;

	/**
	 * Checks the rule safe flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isSafe() : bool;

	/**
	 * Sets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public function setAttributesName(?array $attributes) : void;

	/**
	 * Sets the scenarios.
	 *
	 * @param array $scenarios
	 *	The scenarios.
	 */
	public function setScenarios(?array $scenarios) : void;

	/**
	 * Runs the validation procedure.
	 *
	 * @return bool
	 *	The validation result.
	 */
	public function validate() : bool;
}