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

namespace Lightbit\Data\Sql\MySql;

use \Lightbit\Base\Object;
use \Lightbit\Data\IExpression;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlCriteria;
use \Lightbit\Data\Sql\ISqlSelectCriteria;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\SqlStatementFactory;

/**
 * SqlStatementFactory.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlSqlStatementFactory extends SqlStatementFactory
{
	/**
	 * Constructor.
	 *
	 * @param ISqlConnection $sqlConnection
	 *	The sql driver connection.
	 *
	 * @param array $configuration
	 *	The sql driver configuration.
	 */
	public function __construct(ISqlConnection $sqlConnection, array $configuration = null)
	{
		parent::__construct($sqlConnection, $configuration);
	}

	/**
	 * Creates a count statement.
	 *
	 * @param string $table
	 *	The table names.
	 *
	 * @param ISqlCriteria $criteria
	 *	The select criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function count(string $table, ?ISqlCriteria $criteria) : ISqlStatement
	{
		if ($criteria)
		{
			$statement = 'SELECT ';

			if ($criteria instanceof ISqlSelectCriteria)
			{
				if ($criteria->isDistinct())
				{
					$statement .= 'COUNT(DISTINCT ';
				}

				$statement .= $criteria->hasSelect() ?
					$criteria->getSelect() : '*';

				$statement .= ') FROM ';

				$statement .= $criteria->hasFrom() ?
					$criteria->getFrom() : $this->quote($table);
			}
			else
			{
				$statement .= 'COUNT(\'1\') FROM ' . $this->quote($table);
			}

			if ($criteria->hasAlias())
			{
				$statement .= ' ' . $this->quote($criteria->getAlias());
			}

			if ($criteria->hasJoin())
			{
				$statement .= ' ' . $criteria->getJoin();
			}

			if ($criteria->hasCondition())
			{
				$statement .= ' WHERE ' . $criteria->getCondition();
			}

			if ($criteria instanceof ISqlSelectCriteria)
			{
				if ($criteria->hasGroup())
				{
					$statement .= ' GROUP BY ' . $criteria->getGroup();
				}

				if ($criteria->hasSort())
				{
					$statement .= ' ORDER BY ' . $criteria->getSort();
				}

				if ($criteria->hasLimit())
				{
					$statement .= ' LIMIT ' . $criteria->getLimit()
						. ' OFFSET ' . ($criteria->hasOffset() ? $criteria->getOffset() : '0');
				}

				else if ($criteria->hasOffset())
				{
					$statement .= ' LIMIT 32768 OFFSET ' . $criteria->getOffset();
				}
			}

			return $this->statement($statement, $criteria->getArguments());
		}

		return $this->statement('SELECT COUNT(\'1\') FROM ' . $this->quote($table));
	}

	/**
	 * Creates a delete statement.
	 *
	 * @param array $table
	 *	The table name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The delete criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function delete(string $table, ?ISqlCriteria $criteria) : ISqlStatement
	{
		if ($criteria)
		{
			$statement = 'DELETE';

			if ($criteria->hasAlias())
			{
				$statement .= ' ' . $criteria->getAlias()
					. ' FROM ' . $this->quote($table)
					. ' ' . $this->quote($criteria->getAlias());

				if ($criteria->hasJoin())
				{
					$statement .= ' ' . $criteria->getJoin();
				}
			}

			else if ($criteria->hasJoin())
			{
				throw new Exception(sprintf('Can not use join without table alias for delete statement: table %s, join %s', $table, $criteria->getJoin()));
			}

			else
			{
				$statement .= ' FROM ' . $this->quote($table);
			}

			if ($criteria->hasCondition())
			{
				$statement .= ' WHERE ' . $criteria->getCondition();
			}

			return $this->statement($statement, $criteria->getArguments());
		}

		return $this->statement('DELETE FROM ' . $this->quote($table));
	}

	/**
	 * Creates an insert statement.
	 *
	 * @param string $table
	 *	The table names.
	 *
	 * @param array $values
	 *	The values, indexed by field name.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function insert(string $table, array $values) : ISqlStatement
	{
		$fields = [];
		$placeholders = [];
		$parameters = [];

		foreach ($values as $field => $value)
		{
			$fields[] = $this->quote($field);

			if ($value instanceof IExpression)
			{
				$placeholders[] = $value->toString();
			}
			else
			{
				$parameter = ':lb' . __lightbit_next_id();
				$placeholders[] = $parameter;
				$parameters[$parameter] = $value;
			}
		}

		$statement = 'INSERT INTO ' . $this->quote($table)
			. ' (' . implode(', ', $fields) . ') '
			. ' VALUES (' . implode(', ', $placeholders) . ')';

		return $this->statement($statement, $parameters);
	}

	/**
	 * Creates a select statement.
	 *
	 * @param string $table
	 *	The table names.
	 *
	 * @param ISqlCriteria $criteria
	 *	The select criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function select(string $table, ?ISqlCriteria $criteria) : ISqlStatement
	{
		if ($criteria)
		{
			$statement = 'SELECT ';

			if ($criteria instanceof ISqlSelectCriteria)
			{
				if ($criteria->isDistinct())
				{
					$statement .= 'DISTINCT ';
				}

				$statement .= $criteria->hasSelect() ?
					$criteria->getSelect() : '*';

				$statement .= ' FROM ';

				$statement .= $criteria->hasFrom() ?
					$criteria->getFrom() : $this->quote($table);
			}
			else
			{
				$statement .= '* FROM ' . $this->quote($table);
			}

			if ($criteria->hasAlias())
			{
				$statement .= ' ' . $this->quote($criteria->getAlias());
			}

			if ($criteria->hasJoin())
			{
				$statement .= ' ' . $criteria->getJoin();
			}

			if ($criteria->hasCondition())
			{
				$statement .= ' WHERE ' . $criteria->getCondition();
			}

			if ($criteria instanceof ISqlSelectCriteria)
			{
				if ($criteria->hasGroup())
				{
					$statement .= ' GROUP BY ' . $criteria->getGroup();
				}

				if ($criteria->hasSort())
				{
					$statement .= ' ORDER BY ' . $criteria->getSort();
				}

				if ($criteria->hasLimit())
				{
					$statement .= ' LIMIT ' . $criteria->getLimit()
						. ' OFFSET ' . ($criteria->hasOffset() ? $criteria->getOffset() : '0');
				}

				else if ($criteria->hasOffset())
				{
					$statement .= ' LIMIT 32768 OFFSET ' . $criteria->getOffset();
				}
			}

			return $this->statement($statement, $criteria->getArguments());
		}

		return $this->statement('SELECT * FROM ' . $this->quote($table));
	}

	/**
	 * Creates an update statement.
	 *
	 * @param string $table
	 *	The table names.
	 *
	 * @param array $values
	 *	The values, indexed by field name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The update criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function update(string $table, array $values, ?ISqlCriteria $criteria) : ISqlStatement
	{
		$parameters = [];
		$assignments = [];

		foreach ($values as $field => $value)
		{
			$assignment = $this->quote($field) . ' = ';

			if ($value instanceof IExpression)
			{
				$assignment .= $value->toString();
			}
			else
			{
				$parameter = ':lb' . __lightbit_next_id();
				$assignment .= $parameter;
				$parameters[$parameter] = $value;
			}

			$assignments[] = $assignment;
		}

		$assignments = implode(', ', $assignments);

		if ($criteria)
		{
			$statement = 'UPDATE';

			if ($criteria->hasAlias())
			{
				$statement .= ' ' . $criteria->getAlias()
					. ' FROM ' . $this->quote($table)
					. ' ' . $this->quote($criteria->getAlias());

				if ($criteria->hasJoin())
				{
					$statement .= ' ' . $criteria->getJoin();
				}
			}

			else if ($criteria->hasJoin())
			{
				throw new Exception(sprintf('Can not use join without table alias for delete statement: table %s, join %s', $table, $criteria->getJoin()));
			}

			else
			{
				$statement .= ' ' . $this->quote($table);
			}

			$statement .= ' SET ' . $assignments;

			if ($criteria->hasCondition())
			{
				$statement .= ' WHERE ' . $criteria->getCondition();
			}

			if ($criteria->hasArguments())
			{
				$parameters += $criteria->getArguments();
			}

			return $this->statement($statement, $parameters);
		}

		return $this->statement(('UPDATE ' . $this->quote($table) . ' SET ' . $assignments), $parameters);
	}
}
