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

use \Lightbit\Base\Object;
use \Lightbit\Data\Sql\ISqlSelectCriteria;

/**
 * SqlCriteria.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SqlSelectCriteria extends SqlCriteria implements ISqlSelectCriteria
{
	/**
	 * The distinct flag.
	 *
	 * @var bool
	 */
	private $distinct;

	/**
	 * The from.
	 *
	 * @var string
	 */
	private $from;

	/**
	 * The group.
	 *
	 * @var string
	 */
	private $group;

	/**
	 * The limit.
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * The offset.
	 *
	 * @var int
	 */
	private $offset;

	/**
	 * The select.
	 *
	 * @var string
	 */
	private $select;

	/**
	 * The sort.
	 *
	 * @var string
	 */
	private $sort;

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The SQL criteria configuration.
	 */
	public function __construct(array $configuration = null)
	{
		parent::__construct($configuration);

		$this->distinct = false;
	}

	/**
	 * Gets the from.
	 *
	 * @return string
	 *	The from.
	 */
	public function getFrom() : ?string
	{
		return $this->from;
	}

	/**
	 * Gets the group.
	 *
	 * @return string
	 *	The group.
	 */
	public function getGroup() : ?string
	{
		return $this->group;
	}

	/**
	 * Gets the limit.
	 *
	 * @return int
	 *	The limit.
	 */
	public function getLimit() : ?int
	{
		return $this->limit;
	}

	/**
	 * Gets the offset.
	 *
	 * @return int
	 *	The offset.
	 */
	public function getOffset() : ?int
	{
		return $this->offset;
	}

	/**
	 * Gets the select.
	 *
	 * @return string
	 *	The select.
	 */
	public function getSelect() : ?string
	{
		return $this->select;
	}

	/**
	 * Gets the sort.
	 *
	 * @return string
	 *	The sort.
	 */
	public function getSort() : ?string
	{
		return $this->sort;
	}

	/**
	 * Checks the from.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasFrom() : bool
	{
		return !!$this->from;
	}

	/**
	 * Checks the group.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasGroup() : bool
	{
		return !!$this->group;
	}

	/**
	 * Checks the limit.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasLimit() : bool
	{
		return isset($this->limit);
	}

	/**
	 * Checks the offset.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasOffset() : bool
	{
		return isset($this->offset);
	}

	/**
	 * Checks the select.
	 *
	 * @return bool
	 *	The select.
	 */
	public function hasSelect() : bool
	{
		return !!$this->select;
	}

	/**
	 * Checks the sort.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasSort() : bool
	{
		return !!$this->sort;
	}

	/**
	 * Checks the distinct flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isDistinct() : bool
	{
		return $this->distinct;
	}

	/**
	 * Sets the distinct flag.
	 *
	 * @param bool $distinct
	 *	The distinct flag.
	 */
	public function setDistinct(bool $distinct) : void
	{
		$this->distinct = $distinct;
	}

	/**
	 * Sets the from.
	 *
	 * @param string $from
	 *	The from.
	 */
	public function setFrom(?string $from) : void
	{
		$this->from = $from;
	}

	/**
	 * Sets the group.
	 *
	 * @param bool $group
	 *	The group.
	 */
	public function setGroup(?string $group) : void
	{
		$this->group = $group;
	}

	/**
	 * Sets the limit.
	 *
	 * @param int $limit
	 *	The limit.
	 */
	public function setLimit(?int $limit) : void
	{
		$this->limit = $limit;
	}

	/**
	 * Sets the offset.
	 *
	 * @param int $offset.
	 *	The offset.
	 */
	public function setOffset(?int $offset) : void
	{
		$this->offset = $offset;
	}

	/**
	 * Sets the select.
	 *
	 * @param string $select
	 *	The select.
	 */
	public function setSelect(?string $select) : void
	{
		$this->select = $select;
	}

	/**
	 * Sets the sort.
	 *
	 * @param string $sort
	 *	The sort.
	 */
	public function setSort(?string $sort) : void
	{
		$this->sort = $sort;
	}
}