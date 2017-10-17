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

use \Lightbit\Data\IModel;
use \Lightbit\Data\Validation\Rule;

/**
 * EmailAddressRule.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class EmailAddressRule extends Rule
{
	/**
	 * The transform flag.
	 *
	 * @type bool
	 */
	private $transform;

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
		parent::__construct($model, $id, null);

		$this->setMessage('format', 'Value of "{attribute}" is not an acceptable email address.');
		$this->transform = true;

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Sets the transform flag.
	 *
	 * @param string $transform
	 *	The transform flag.
	 */
	public final function setTransform(bool $transform) : void
	{
		$this->transform = $transform;
	}

	/**
	 * Validates a single attribute.
	 *
	 * By the time this method is called, the rule is confirmed to apply to
	 * the given model and attribute as this method is meant only to validate
	 * and, if necessary, report any encountered errors.
	 *
	 * If the attribute requires transformation, the new value must be set once
	 * the original passes validation.
	 *
	 * @param IModel $model
	 *	The model.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @param string $subject
	 *	The attribute.
	 *
	 * @return bool
	 *	The result.
	 */
	protected function validateAttribute(IModel $model, string $attribute, $subject) : bool
	{
		if (!is_string($subject) || !preg_match('%^.+@.+\\.[a-z]{2,}$%i', $subject))
		{
			$this->report($attribute, 'format');
			return false;
		}

		if ($this->transform)
		{
			$i = strpos($subject, '@');
			$subject = substr($subject, 0, $i + 1) . strtolower(substr($subject, $i + 1));
		}

		$model->setAttribute($attribute, $subject);
		return true;
	}
}