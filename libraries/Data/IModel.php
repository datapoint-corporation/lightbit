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

use \Lightbit\Base\IElement;

/**
 * IModel.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IModel extends IElement
{
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
	public function __construct(string $scenario = 'default', array $attributes = null, array $configuration = null);

	/**
	 * Gets an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public function getAttribute(string $attribute); // : mixed

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public function getAttributes() : array;

	/**
	 * Gets the attributes name.
	 *
	 * @return array
	 *	The attributes name.
	 */
	public function getAttributesName() : array;

	/**
	 * Gets the rules.
	 *
	 * @return array
	 *	The rules.
	 */
	public function getRules() : array;

	/**
	 * Gets the safe attributes name.
	 *
	 * @return array
	 *	The safe attributes name.
	 */
	public function getSafeAttributesName() : array;

	/**
	 * Gets the scenario.
	 *
	 * @return string
	 *	The scenario.
	 */
	public function getScenario() : string;

	/**
	 * Checks if an attribute is set.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttribute(string $attribute) : bool;

	/**
	 * Imports the attributes.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 */
	public function import(array $attributes) : void;

	/**
	 * Checks the scenario.
	 *
	 * @param string $scenario
	 *	The scenarios to match against.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isScenario(string ...$scenario) : bool;

	/**
	 * Sets an attribute.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param mixed $value
	 *	The attribute value.
	 */
	public function setAttribute(string $attribute, $value) : void;

	/**
	 * Sets the attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public function setAttributes(array $attributes) : void;

	/**
	 * Validates the model according to the applicable rules.
	 *
	 * If an attribute requires transformation, the new value is set once
	 * the original passes validation.
	 *
	 * @return bool
	 *	The result.
	 */
	public function validate() : bool;
}