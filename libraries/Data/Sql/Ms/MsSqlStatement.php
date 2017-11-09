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

namespace Lightbit\Data\Sql\Ms;

use \Lightbit\Base\Object;
use \Lightbit\Data\Sql\Ms\MsSqlConnection;
use \Lightbit\Data\Sql\Ms\MsSqlConnectionException;
use \Lightbit\Data\Sql\Ms\MsSqlReader;

use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * ISqlStatement.
 *
 * It's a statement, prepared through the underlying database management system
 * during construction procedure so that if it fails, an exception is thrown
 * during construction and the object is not made available.
 *
 * The choice has been made to always natively prepare statements through
 * the underlying database management system in order to increase the overall
 * security of applications developed on top of this framework despite the
 * slight impact it gives on performance.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlStatement extends Object implements ISqlStatement
{
	/**
	 * The sql connection.
	 *
	 * @var MsSqlConnection
	 */
	private $msSqlConnection;

	/**
	 * The names.
	 *
	 * @var array
	 */
	private $names;

	/**
	 * The positions.
	 *
	 * @var array
	 */
	private $positions;

	/**
	 * The references.
	 *
	 * @var array
	 */
	private $references;

	/**
	 * The statement.
	 *
	 * @var string
	 */
	private $statement;

	/**
	 * The statement, quoted.
	 *
	 * @var string
	 */
	private $statementQ;

	/**
	 * The sqlsrv resource.
	 *
	 * @var resource
	 */
	private $sqlsrv;

	/**
	 * The stmt resource.
	 *
	 * @var resource
	 */
	private $stmt;

	/**
	 * The values.
	 *
	 * @var array
	 */
	private $values;

	/**
	 * The variables.
	 *
	 * It contains the variables bound by reference to the internal statement
	 * handle.
	 *
	 * @var array
	 */
	private $variables;

	/**
	 * Constructor.
	 *
	 * @param MsSqlConnection $msSqlConnection
	 *	The sql connection.
	 *
	 * @param string $statement
	 *	The sql statement.
	 */
	public function __construct(MsSqlConnection $msSqlConnection, string $statement)
	{
		$this->sqlsrv = $msSqlConnection->getSqlsrv();

		$this->msSqlConnection = $msSqlConnection;
		$this->statement = $statement;
		$this->statementQ = $msSqlConnection->quote($statement);

		$this->names = [];
		$this->positions = [];
		$this->variables = [];
		$this->values = [];
		$this->variables = [];

		$position = 0;
		$references = [];

		if (preg_match_all('/:\\w[\\w0-9]+/', $statement, $matches, PREG_OFFSET_CAPTURE) && $matches)
		{
			foreach ($matches[0] as $i => $match)
			{
				$this->names[] = $match[0];
				$this->positions[$match[0]][] = ++$position;
				$this->variables[$match[0]] = null;

				$references[] = &$this->variables[$match[0]];
			}

			$this->statementQ = preg_replace('/:\\w[\\w0-9]+/', '?', $this->statementQ);
		}

		if (! ($this->stmt = sqlsrv_prepare($this->sqlsrv, $this->statementQ, $references)))
		{
			throw new MsSqlConnectionException
			(
				$this->msSqlConnection,
				sprintf
				(
					'Can not prepare statement: statement %s',
					$this->statement
				),
				$this->msSqlConnection->getExceptionStack()
			);
		}
	}

	/**
	 * Executes as query statement, pre-fetching all results within the
	 * first result set.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @param bool $numeric
	 *	The numeric flag which, when set, causes the results to be fetched
	 *	as a numeric array.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(array $parameters = null, bool $numeric = false) : array
	{
		return $this->query($parameters)->all($numeric);
	}

	/**
	 * Executes the statement, returning the number of affected rows.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(array $parameters = null) : int
	{
		$this->run($parameters);

		$result = sqlsrv_rows_affected($this->stmt);
		sqlsrv_free_stmt($this->stmt);

		if ($result === false)
		{
			throw new MsSqlStatementException
			(
				$this,
				sprintf('Can not execute statement: statement %s', $this->statement),
				$this->msSqlConnection->getExceptionStack()
			);
		}

		return ($result < 0 ? 0 : $result);
	}

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public function getSqlConnection() : ISqlConnection
	{
		return $this->msSqlConnection;
	}

	/**
	 * Gets sql connection internal handle.
	 *
	 * @return resource
	 *	The sql connection internal handle.
	 */
	public function getSqlsrv() // : resource
	{
		return $this->sqlsrv;
	}

	/**
	 * Gets sql statement internal handle.
	 *
	 * @return resource
	 *	The sql statement internal handle.
	 */
	public function getStmt() // : resource
	{
		return $this->stmt;
	}

	/**
	 * Executes as a query statement.
	 *
	 * Since this function generates a reader, that reader should be manually
	 * closed and safely disposed before continuing to the next procedure.
	 *
	 * If the reader is not closed, depending on the underlying database
	 * management system, you might be unable to prepare and/or execute
	 * the next statement and memory leaks may have a negative impact on
	 * the application performance.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return ISqlReader
	 *	The reader.
	 */
	public function query(array $parameters = null) : ISqlReader
	{
		$this->run($parameters);
		return new MsSqlReader($this);
	}

	/**
	 * Runs the statement.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 */
	private function run(array $parameters = null) : void
	{
		if ($parameters)
		{
			$this->setAll($parameters);
		}

		if (count($this->values) !== count($this->positions))
		{
			throw new MsSqlStatementException
			(
				$this,
				sprintf
				(
					'Can not execute statement, bad parameter count: statement %s',
					$this->statement
				)
			);
		}

		foreach ($this->values as $parameter => $value)
		{
			$this->variables[$parameter] = $value;
		}

		if (!sqlsrv_execute($this->stmt))
		{
			throw new MsSqlStatementException
			(
				$this,
				sprintf
				(
					'Can not execut statement: statement %s',
					$this->statement
				),
				$this->msSqlConnection->getExceptionStack()
			);
		}
	}

	/**
	 * Executes as a query statement, pre-fetching and returning the first cell
	 * within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return string
	 *	The result.
	 */
	public function scalar(array $parameters = null) : ?string
	{
		return $this->query($parameters)->scalar();
	}

	/**
	 * Executes as a query statement, pre-fetching and returning the first
	 * result within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @param bool $numeric
	 *	The numeric flag which, when set, causes the result to be fetched as 
	 *	a numeric array.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(array $parameters = null, bool $numeric = false) : ?array
	{
		return $this->query($parameters)->single($numeric);
	}

	/**
	 * Sets a parameter.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param mixed $value
	 *	The parameter value.
	 */
	public function set(string $parameter, $value) : void
	{
		$this->values[$parameter] = $value;
	}

	/**
	 * Sets all parameters.
	 *
	 * @param array $parameters
	 *	The parameters value, indexed by name.
	 */
	public function setAll(array $parameters) : void
	{
		$this->values = [];

		foreach ($parameters as $parameter => $value)
		{
			$this->set($parameter, $value);
		}
	}

	/**
	 * Creates a string representation of this statement.
	 *
	 * @return string
	 *	The result.
	 */
	public function toString() : string
	{
		return $this->statement;
	}
}