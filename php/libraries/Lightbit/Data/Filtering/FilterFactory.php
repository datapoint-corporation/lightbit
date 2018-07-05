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

namespace Lightbit\Data\Filtering;

use \Lightbit\Data\Filtering\FilterFactoryException;

use \Lightbit\Data\Filtering\IFilter;

/**
 * FilterFactory.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class FilterFactory implements IFilterFactory
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Creates a filter.
	 *
	 * @throws FilterFactoryException
	 *	Thrown when the filter creation fails.
	 *
	 * @param string $type
	 *	The filter type.
	 *
	 * @return IFilter
	 *	The filter.
	 */
	public function createFilter(string $type) : IFilter
	{
		switch ($type)
		{
			case 'array':
				return new ArrayFilter();

			case 'bool':
			case 'boolean':
				return new BooleanFilter();

			case 'double':
			case 'float':
				return new FloatFilter();

			case 'int':
			case 'integer':
				return new IntegerFilter();

			case 'string':
				return new StringFilter();
		}

		if (class_exists($type))
		{
			return new ObjectFilter($type);
		}

		throw new FilterFactoryException($this, sprintf(
			'Can not create filter, it does not exist: "%s"',
			$type
		));
	}
}
