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
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\ISqlStatementFactory;

/**
 * SqlStatementFactory.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class SqlStatementFactory extends Object implements ISqlStatementFactory
{
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
	abstract public function delete(string $table, ?ISqlCriteria $criteria) : ISqlStatement;

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
	abstract public function insert(string $table, array $values) : ISqlStatement;

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
	abstract public function select(string $table, ?ISqlCriteria $criteria) : ISqlStatement;

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
	abstract public function update(string $table, array $values, ?ISqlCriteria $criteria) : ISqlStatement;

	/**
	 * The sql connection.
	 *
	 * @type ISqlConnection
	 */
	private $sqlConnection;

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
		$this->sqlConnection = $sqlConnection;

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public final function getConnection() : ISqlConnection
	{
		return $this->sqlConnection;
	}

	/**
	 * Quotes an identifier.
	 *
	 * @param string $identifier
	 *	The identifier to quote.
	 *
	 * @return string
	 *	The result.
	 */
	protected function quote(string $identifier) : string
	{
		return '"' . strtr($identifier, [ '.' => '"."' ]) . '"';
	}

	/**
	 * Prepares a statement.
	 *
	 * @param string $statement
	 *	The statement.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	protected function statement(string $statement, array $arguments = null) : ISqlStatement
	{
		$result = $this->getConnection()->statement($statement);

		if ($arguments)
		{
			$result->setArguments($arguments);
		}

		return $result;
	}
}