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

namespace Lightbit\Data\Sql;

use \Lightbit\Data\Sql\ISqlCriteria;

/**
 * ISqlSelectCriteria.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlSelectCriteria extends ISqlCriteria
{
	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The SQL criteria configuration.
	 */
	public function __construct(array $configuration = null);

	/**
	 * Gets the from.
	 *
	 * @return string
	 *	The from.
	 */
	public function getFrom() : ?string;

	/**
	 * Gets the group.
	 *
	 * @return string
	 *	The group.
	 */
	public function getGroup() : ?string;

	/**
	 * Gets the limit.
	 *
	 * @return int
	 *	The limit.
	 */
	public function getLimit() : ?int;

	/**
	 * Gets the offset.
	 *
	 * @return int
	 *	The offset.
	 */
	public function getOffset() : ?int;

	/**
	 * Gets the select.
	 *
	 * @return string
	 *	The select.
	 */
	public function getSelect() : ?string;

	/**
	 * Gets the sort.
	 *
	 * @return string
	 *	The sort.
	 */
	public function getSort() : ?string;

	/**
	 * Checks the from.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasFrom() : bool;

	/**
	 * Checks the group.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasGroup() : bool;

	/**
	 * Checks the limit.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasLimit() : bool;

	/**
	 * Checks the offset.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasOffset() : bool;

	/**
	 * Checks the select.
	 *
	 * @return bool
	 *	The select.
	 */
	public function hasSelect() : bool;

	/**
	 * Checks the sort.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasSort() : bool;

	/**
	 * Checks the distinct flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isDistinct() : bool;

	/**
	 * Sets the distinct flag.
	 *
	 * @param bool $distinct
	 *	The distinct flag.
	 */
	public function setDistinct(bool $distinct) : void;

	/**
	 * Sets the from.
	 *
	 * @param string $from
	 *	The from.
	 */
	public function setFrom(?string $from) : void;

	/**
	 * Sets the group.
	 *
	 * @param bool $group
	 *	The group.
	 */
	public function setGroup(?string $group) : void;

	/**
	 * Sets the limit.
	 *
	 * @param int $limit
	 *	The limit.
	 */
	public function setLimit(?int $limit) : void;

	/**
	 * Sets the offset.
	 *
	 * @param int $offset.
	 *	The offset.
	 */
	public function setOffset(?int $offset) : void;

	/**
	 * Sets the select.
	 *
	 * @param string $select
	 *	The select.
	 */
	public function setSelect(?string $select) : void;

	/**
	 * Sets the sort.
	 *
	 * @param string $sort
	 *	The sort.
	 */
	public function setSort(?string $sort) : void;
}