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
	 * Adds an attribute error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $message
	 *	The attribute error message.
	 */
	public function addAttributeError(string $attribute, string $message) : void;

	/**
	 * Adds an attribute error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param array $messages
	 *	The attribute error messages.
	 */
	public function addAttributeErrors(string $attribute, array $messages) : void;

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
	 * Gets the attribute errors.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return array
	 *	The attribute errors.
	 */
	public function getAttributeErrors(string $attribute) : array;

	/**
	 * Gets the attribute label.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return string
	 *	The attribute label.
	 */
	public function getAttributeLabel(string $attribute) : string;

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 *	The attributes.
	 */
	public function getAttributes() : array;

	/**
	 * Gets the attributes errors.
	 *
	 * @return array
	 *	The attributes errors.
	 */
	public function getAttributesErrors() : array;

	/**
	 * Gets the attributes label.
	 *
	 * @return array
	 *	The attributes label.
	 */
	public function getAttributesLabel() : array;

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
	 * Checks if an attribute has an error.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributeError(string $attribute) : bool;

	/**
	 * Checks for attributes errors.
	 *
	 * @param array $attributes
	 *	The attributes names.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributesErrors(string $attributes = null) : bool;

	/**
	 * Imports the attributes.
	 *
	 * @param array $attributes
	 *	The attributes to import.
	 *
	 * @return bool
	 *	The result.
	 */
	public function import(array $attributes) : bool;

	/**
	 * Checks if an attribute is available.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAttributeAvailable(string $attribute) : bool;

	/**
	 * Checks if an attribute is required.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAttributeRequired(string $attribute) : bool;

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
	public function setAttributeError(string $attribute, string $message) : void;

	/**
	 * Sets the attributes.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public function setAttributes(array $attributes) : void;

	/**
	 * Sets an attributes error.
	 *
	 * @param array $attributesError
	 *	The attributes error.
	 *
	 * @param bool $merge
	 *	The attributes error merge flag.
	 */
	public function setAttributesError(array $attributesError, bool $merge = true) : void;

	/**
	 * Sets an attributes errors.
	 *
	 * @param array $attributesErrors
	 *	The attributes errors.
	 *
	 * @param bool $merge
	 *	The attributes errors merge flag.
	 */
	public function setAttributesErrors(array $attributesErrors, bool $merge = true) : void;

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