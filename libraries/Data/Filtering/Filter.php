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

use \Lightbit\Base\Element;
use \Lightbit\Data\Filtering\ArrayFilter;
use \Lightbit\Data\Filtering\IFilter;
use \Lightbit\Data\Filtering\IntegerFilter;
use \Lightbit\Data\Filtering\StringFilter;
use \Lightbit\Data\Filtering\TypeFilter;

/**
 * Filter.
 *
 * This defines the base implementation for a validation filter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Filter extends Element implements IFilter
{
	/**
	 * Runs the filter.
	 *
	 * @param mixed $value
	 *	The value to run the filter on.
	 *
	 * @return mixed
	 *	The value.
	 */
	abstract public function run($value); // : mixed

	/**
	 * Creates a filter.
	 *
	 * @param string $className
	 *	The filter class name or alias.
	 *
	 * @param array $configuration
	 *	The filter configuration.
	 *
	 * @return IFilter
	 *	The filter.
	 */
	public static function create(string $className, array $configuration = null) : IFilter
	{
		static $filtersClassName =
		[
			'array' => ArrayFilter::class,
			'float' => FloatFilter::class,
			'int' => IntegerFilter::class,
			'string' => StringFilter::class,
			'type' => TypeFilter::class
		];

		if (isset($filtersClassName[$className]))
		{
			$className = $filtersClassName[$className];
		}

		return new $className($configuration);
	}

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The filter configuration.
	 */
	public function __construct(array $configuration = null)
	{
		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}
}
