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

namespace Lightbit\Data\Validation;

use \Lightbit\Data\IModel;
use \Lightbit\Data\Validation\Rule;

/**
 * FullNameRule.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class FullNameRule extends Rule
{
	/**
	 * The names.
	 *
	 * @var array
	 */
	private $names;

	/**
	 * The transform flag.
	 *
	 * @var bool
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
		parent::__construct($model, $id);

		$this->transform = true;

		$this->setMessage('format', 'Value of "{attribute}" must be your full name.');

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Gets the names.
	 *
	 * @return array
	 *	The names.
	 */
	public final function getNames() : ?array
	{
		return $this->names;
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
		$this->names = preg_split('%([^\\pL]+)%u', $subject, -1, PREG_SPLIT_NO_EMPTY);

		if (!isset($this->names[1]))
		{
			$this->report($attribute, 'format');
			$this->names = null;

			return false;
		}

		if ($this->transform)
		{
			foreach ($this->names as $i => $name)
			{
				$this->names[$i] = mb_convert_case
				(
					$name, 
					(mb_strlen($name) > 2 ? MB_CASE_TITLE : MB_CASE_LOWER)
				);
			}

			$model->setAttribute($attribute, implode(' ', $this->names));
		}

		return true;
	}
}