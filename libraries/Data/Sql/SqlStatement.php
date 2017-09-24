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
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\SqlStatementException;

/**
 * ISqlStatement.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SqlStatement extends Object implements ISqlStatement
{
	/**
	 * The internal statement handler.
	 *
	 * @type PDOStatement
	 */
	private $pdoStatement;

	/**
	 * The sql connection.
	 *
	 * @type ISqlConnection
	 */
	private $sqlConnection;

	/**
	 * The statement.
	 *
	 * @type string
	 */
	private $statement;

	/**
	 * Constructor.
	 *
	 * @param SqlConnection $sqlConnection
	 *	The sql statement connection.
	 *
	 * @param string $statement
	 *	The sql statement.
	 *
	 * @param array $configuration
	 *	The sql statement configuration.
	 */
	public function __construct(SqlConnection $sqlConnection, string $statement, array $configuration = null)
	{
		$this->sqlConnection = $sqlConnection;
		$this->statement = $statement;

		try
		{
			$this->pdoStatement = $sqlConnection->getPdo()->prepare($statement);
		}
		catch (\PDOException $e)
		{
			throw new SqlStatementException
			(
				$this,
				sprintf('Can not prepare statement for execution: statement "%s"', $statement),
				$e
			);
		}

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}

	/**
	 * Closes the statement.
	 */
	public final function close() : void
	{
		$this->pdoStatement = null;
	}

	/**
	 * Executes the statement.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public final function execute(array $arguments = null) : int
	{
		if (!$this->pdoStatement)
		{
			throw new SqlStatementException
			(
				$this,
				'Statement execution failure: statement is closed'
			);
		}

		if ($arguments)
		{
			$this->setArguments($arguments);
		}

		try
		{
			$this->pdoStatement->execute();
			return $this->pdoStatement->rowCount();
		}
		catch (\PDOException $e)
		{
			throw new SqlStatementException
			(
				$this,
					sprintf('Statement execution error: %s', lcfirst($e->getMessage())),
				$e
			);
		}
	}

	private final function getPdoParamDataType($value) : int
	{
		switch (gettype($value))
		{
			case 'boolean':
				return \PDO::PARAM_BOOL;

			case 'NULL':
				return \PDO::PARAM_NULL;

			case 'integer':
				return \PDO::PARAM_INT;
		}

		return \PDO::PARAM_STR;
	}

	/**
	 * Gets the internal statement handle.
	 *
	 * @return PDOStatement
	 *	The internal statement handle.
	 */
	public final function getPdoStatement() : ?\PDOStatement
	{
		return $this->pdoStatement;
	}

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public final function getSqlConnection() : ISqlConnection
	{
		return $this->sqlConnection;
	}

	/**
	 * Checks the statement status.
	 *
	 * @return bool
	 *	The statement status.
	 */
	public final function isClosed() : bool
	{
		return !$this->pdoStatement;
	}

	/**
	 * Executes the statement as a scalar query.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function scalar(array $arguments = null) // : mixed;
	{
		return $this->query($arguments)->scalar();
	}

	/**
	 * Sets the arguments.
	 *
	 * @param array $arguments
	 *	The arguments.
	 */
	public final function setArguments(array $arguments) : void
	{
		$parameter;

		try
		{
			foreach ($arguments as $parameter => $argument)
			{
				$this->pdoStatement->bindValue($parameter, $argument, $this->getPdoParamDataType($argument));
			}
		}
		catch (\PDOException $e)
		{
			throw new SqlStatementException
			(
				$this,
				sprintf('Statement argument can not be set: parameter "%s", because %s', $parameter, lcfirst($e->getMessage())),
				$e
			);
		}
	}

	/**
	 * Executes the statement as a query that's meant to fetch a single result.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The result.
	 */
	public final function single(array $arguments = null, bool $numeric = false) : ?array
	{
		return $this->query($arguments)->single($numeric);
	}

	/**
	 * Executes the statement as a query.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return ISqlReader
	 *	The sql reader.
	 */
	public final function query(array $arguments = null) : ISqlReader
	{
		if ($arguments)
		{
			$this->setArguments($arguments);
		}

		return new SqlReader($this);
	}

	/**
	 * Creates a string representation of this object.
	 *
	 * @return string
	 *	The string representation of this object.
	 */
	public final function toString() : string
	{
		return $this->statement;
	}
}
