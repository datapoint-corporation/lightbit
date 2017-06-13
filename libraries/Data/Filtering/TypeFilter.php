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

namespace Lightbit\Data\Filtering;

use \Lightbit\Data\Filtering\Filter;
use \Lightbit\Data\Filtering\FilterException;
use \Lightbit\Helpers\TypeHelper;

/**
 * TypeFilter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @version 1.0.0
 */
class TypeFilter extends Filter
{
	/**
	 * The type name.
	 *
	 * @type string
	 */
	private $typeName;

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The filter configuration.
	 */
	public function __construct(array $configuration = null)
	{
		parent::__construct($configuration);

		$this->typeName = Object::class;
	}

	/**
	 * Runs the filter.
	 *
	 * @param mixed $value
	 *	The value to run the filter on.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function run($value) // : mixed
	{
		if (!isset($value))
		{
			if ($this->typeName[0] != '?')
			{
				throw new FilterException($this, sprintf('Bad filter value data type: expecting "%s", found "%s"', $this->typeName, 'NULL'));
			}

			return $value;
		}

		$typeName = $this->typeName[0] == '?'
			? substr($this->typeName, 1)
			: $this->typeName;

		if (TypeHelper::isBasicTypeName($typeName))
		{
			if (TypeHelper::getNameOf($value) == $typeName)
			{
				return true;
			}
		}
		else if ($value instanceof Object)
		{
			$class = new ReflectionClass($value);

			if ($class->getName() == $typeName 
				|| $class->isSubclassOf($typeName)
				|| $class->implemets($typeName))
			{
				return $value;
			}
		}

		throw new FilterException($this, sprintf('Bad filter value data type: expecting "%s", found "%s"', $typeName, TypeHelper::getNameOf($value)));
	}

	/**
	 * Sets the type name.
	 *
	 * @param string $typeName
	 *	The type name.
	 */
	public final function setTypeName(string $typeName) : void
	{
		$this->typeName = $typeName;
	}
}
