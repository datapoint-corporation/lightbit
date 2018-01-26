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

namespace Lightbit\Data\Sql;

use \Lightbit\Data\IExpression;
use \Lightbit\Data\Sql\ISqlCriteria;

/**
 * SqlCriteria.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SqlCriteria implements ISqlCriteria
{
	/**
	 * The alias.
	 *
	 * @var string
	 */
	private $alias;

	/**
	 * The join.
	 *
	 * @var string
	 */
	private $join;

	/**
	 * The condition.
	 *
	 * @var string
	 */
	private $condition;

	/**
	 * The parameters.
	 *
	 * @var array
	 */
	private $parameters;

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The SQL criteria configuration.
	 */
	public function __construct(array $configuration = null)
	{
		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Adds a comparison.
	 *
	 * @param string $subject
	 *	The subject field name.
	 *
	 * @param mixed $candidate
	 *	The candidate.
	 */
	public function addComparison(string $subject, $candidate) : void
	{
		$comparison = '"' . strtr($subject, [ '.' => '"."' ]) . '" ';

		if (isset($candidate))
		{
			if ($candidate instanceof IExpression)
			{
				$comparison .= '= ' . $candidate->toString();
			}
			else
			{
				$parameter = ':lb' . Lightbit::getInstance()->increment();
				$comparison .= '= ' . $parameter;

				$this->parameters[$parameter] = $candidate;
			}
		}
		else
		{
			$comparison .= 'IS NULL';
		}

		$this->condition = $this->condition ?
			($this->condition . ' AND ' . $comparison) : $comparison;
	}

	/**
	 * Adds comparisons.
	 *
	 * @param array $comparisons
	 *	The comparisons to add, as an associative array containing the
	 *	candidates indexed by subject field name.
	 */
	public function addComparisons(array $comparisons) : void
	{
		foreach ($comparisons as $subject => $candidate)
		{
			$this->addComparison($subject, $candidate);
		}
	}

	/**
	 * Gets the alias.
	 *
	 * @return string
	 *	The alias.
	 */
	public function getAlias() : ?string
	{
		return $this->alias;
	}

	/**
	 * Gets the condition.
	 *
	 * @return string
	 *	The condition.
	 */
	public function getCondition() : ?string
	{
		return $this->condition;
	}

	/**
	 * Gets the join.
	 *
	 * @return string
	 *	The join.
	 */
	public function getJoin() : ?string
	{
		return $this->join;
	}

	/**
	 * Gets the parameters.
	 *
	 * @return array
	 *	The parameters.
	 */
	public function getParameters() : ?array
	{
		return $this->parameters;
	}

	/**
	 * Gets the condition.
	 *
	 * This method is an alias of "getCondition".
	 *
	 * @return string
	 *	The condition.
	 */
	public function getWhere() : ?string
	{
		return $this->getCondition();
	}

	/**
	 * Checks the alias.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAlias() : bool
	{
		return !!$this->alias;
	}

	/**
	 * Checks the condition.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasCondition() : bool
	{
		return !!$this->condition;
	}

	/**
	 * Checks the join.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasJoin() : bool
	{
		return !!$this->join;
	}

	/**
	 * Checks the parameters.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasParameters() : bool
	{
		return !!$this->parameters;
	}

	/**
	 * Checks the condition.
	 *
	 * This method is an alias of "hasCondition".
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasWhere() : bool
	{
		return $this->hasCondition();
	}

	/**
	 * Sets the alias.
	 *
	 * @param string $alias
	 *	The alias.
	 */
	public function setAlias(?string $alias) : void
	{
		$this->alias = $alias;
	}

	/**
	 * Sets the condition.
	 *
	 * @param string $condition
	 *	The condition.
	 */
	public function setCondition(?string $condition) : void
	{
		$this->condition = $condition;
	}

	/**
	 * Sets the join.
	 *
	 * @param string $join
	 *	The join.
	 */
	public function setJoin(?string $join) : void
	{
		$this->join = $join;
	}

	/**
	 * Sets the parameters.
	 *
	 * @param array $parameters
	 *	The parameters.
	 *
	 * @param bool $dispose
	 *	When set, the existing parameters will be disposed.
	 */
	public function setParameters(?array $parameters, bool $dispose = false) : void
	{
		$this->parameters = (!$this->parameters || $dispose) ?
			$parameters : ($parameters + $this->parameters);
	}

	/**
	 * Sets the condition.
	 *
	 * This method is an alias of "setCondition".
	 *
	 * @param string $condition
	 *	The condition.
	 */
	public function setWhere(?string $condition) : void
	{
		$this->setCondition($condition);
	}
}
