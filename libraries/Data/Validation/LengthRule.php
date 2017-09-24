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

use \Lightbit\Base\Exception;
use \Lightbit\Data\IModel;
use \Lightbit\Data\Validation\Rule;

/**
 * LengthRule.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class LengthRule extends Rule
{
	/**
	 * The minimum length.
	 *
	 * @type string
	 */
	private $minimum;

	/**
	 * The maximum length.
	 *
	 * @type string
	 */
	private $maximum;

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

		$this->setMessage('length', 'Value of "{attribute-label}" does not meet the expected length.');		

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Sets the maximum length.
	 *
	 * @param int $maximum
	 *	The maximum length.
	 */
	public function setMaximum(?int $maximum) : void
	{
		$this->maximum = $maximum;
	}

	/**
	 * Sets the minimum length.
	 *
	 * @param int $minimum
	 *	The minimum length.
	 */
	public function setMinimum(?int $minimum) : void
	{
		$this->minimum = $minimum;
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
		if ($this->minimum || $this->maximum)
		{
			$length = strlen($subject);

			if (($this->minimum && $length < $this->minimum)
				|| ($this->maximum && $length > $this->maximum))
			{
				$this->report
				(
					$attribute, 
					'length', 
					[ 
						'minimum' => $this->minimum, 
						'maximum' => $this->maximum 
					]
				);

				return false;
			}
		}

		return true;
	}
}