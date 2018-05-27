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

use \Lightbit\Data\Filtering\IFilter;

/**
 * FilterProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class FilterProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var FilterProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return FilterProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : FilterProvider
	{
		return (self::$instance ?? (self::$instance = new FilterProvider()));
	}

	/**
	 * The filter factory.
	 *
	 * @var IFilterFactory
	 */
	private $filterFactory;

	/**
	 * The filters map.
	 *
	 * @var array
	 */
	private $filtersMap;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->filtersMap = [];
	}

	/**
	 * Gets a filter.
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
	public final function getFilter(string $type) : IFilter
	{
		return ($this->filtersMap[$type] ?? ($this->filtersMap[$type] = $this->getFilterFactory()->createFilter($type)));
	}

	/**
	 * Gets the filter factory.
	 *
	 * @return IFilterFactory
	 *	The filter factory.
	 */
	public final function getFilterFactory() : IFilterFactory
	{
		return ($this->filterFactory ?? ($this->filterFactory = new FilterFactory()));
	}
}
