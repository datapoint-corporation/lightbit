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

namespace Lightbit\Data\Sql\My;

use \Lightbit\Base\Object;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * MySqlStatement.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlStatement extends Object implements ISqlStatement
{
	/**
	 * The mysqli internal handle.
	 *
	 * @type mysqli
	 */
	private $mysqli;

	/**
	 * The mysqli statement internal handle.
	 *
	 * @type mysqli_stmt
	 */
	private $mysqliStatement;
	
	/**
	 * The sql connection.
	 *
	 * @type ISqlConnection
	 */
	private $mySqlConnection;

	/**
	 * The parameter names.
	 *
	 * @type array
	 */
	private $names;

	/**
	 * The parameter positions.
	 *
	 * @type array
	 */
	private $positions;

	/**
	 * The statement.
	 *
	 * @type string
	 */
	private $statement;

	/**
	 * The quoted statement.
	 *
	 * @type string
	 */
	private $statementQ;

	/**
	 * The parameters value.
	 *
	 * @type array
	 */
	private $values;

	/**
	 * Constructor.
	 *
	 * @param MySqlConnection $mySqlConnection
	 *	The sql connection.
	 *
	 * @param string $statement
	 *	The sql statement.
	 */
	public function __construct(MySqlConnection $mySqlConnection, string $statement)
	{
		$this->mysqli = $mySqlConnection->getMysqli();
		$this->mySqlConnection = $mySqlConnection;
		$this->names = [];
		$this->positions = [];
		$this->values = [];

		// Since mysqli does not support named parameters in the expected 
		// format, we need to extract them to match.
		$position = -1;
		$matches;

		if (preg_match_all('/:\\w[\\w0-9]+/', $statement, $matches, PREG_OFFSET_CAPTURE) && $matches)
		{
			foreach ($matches[0] as $i => $match)
			{
				$this->positions[$match[0]][] = ++$position;
				$this->names[] = $match[0];
			}
		}

		// Now that we have all positions, it's time to replace the markers
		// with the question mark placeholders expected by mysqli.
		$this->statement = $statement;
		$this->statementQ = preg_replace('/:\\w[\\w0-9]+/', '?', $statement);
		$this->statementQ = $mySqlConnection->quote($this->statementQ);

		// Everything's ready for the dark voodoo that goes on outside
		// the constructor, time to prepare the actual statement.
		$this->mysqliStatement = mysqli_stmt_init($this->mysqli);

		if (!mysqli_stmt_prepare($this->mysqliStatement, $this->statementQ))
		{
			throw new MySqlConnectionException
			(
				$this->mySqlConnection,
				sprintf
				(
					'%s (%d)',
					mysqli_stmt_error($this->mysqliStatement),
					mysqli_stmt_errno($this->mysqliStatement)
				)
			);
		}
	}

	/**
	 * Executes the statement.
	 *
	 * @param array $parameters
	 *	The statement execution parameters.
	 */
	private function _execute(array $parameters = null) : void
	{
		if ($parameters)
		{
			$this->setAll($parameters);
		}

		if (count($this->values) !== count($this->positions))
		{
			throw new MySqlStatementException
			(
				$this,
				sprintf
				(
					'Can not execute statement, bad parameter count: statement %s',
					$this->statement
				)
			);
		}

		if ($this->names)
		{
			$types = '';
			$variables = [];

			foreach ($this->names as $i => $parameter)
			{
				switch (gettype($this->values[$parameter]))
				{
					case 'integer':
						$types .= 'i';
						break;

					case 'double':
						$types .= 'd';
						break;

					default:
						$types .= 's';
						break;
				}

				$variables[] = $this->values[$parameter];
			}

			if (!mysqli_stmt_bind_param($this->mysqliStatement, $types, ...$variables))
			{
				throw new MySqlStatementException
				(
					$this,
					sprintf
					(
						'%s (%d)',
						mysqli_stmt_error($this->mysqliStatement),
						mysqli_stmt_errno($this->mysqliStatement)
					)
				);
			}
		}

		if (!mysqli_stmt_execute($this->mysqliStatement))
		{
			throw new MySqlStatementException
			(
				$this,
				sprintf
				(
					'%s (%d)',
					mysqli_stmt_error($this->mysqliStatement),
					mysqli_stmt_errno($this->mysqliStatement)
				)
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
		$this->_execute($parameters);

		if ($meta = mysqli_stmt_result_metadata($this->mysqliStatement))
		{
			mysqli_stmt_free_result($this->mysqliStatement);
		}

		return mysqli_stmt_affected_rows($this->mysqliStatement);
	}

	/**
	 * Gets the mysqli statement internal handle.
	 *
	 * @return mysqli_stmt
	 *	The mysqli statement internal handle.
	 */
	public function getMysqliStatement() : \mysqli_stmt
	{
		return $this->mysqliStatement;
	}

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public function getSqlConnection() : ISqlConnection
	{
		return $this->mySqlConnection;
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
		$this->_execute($parameters);
		return new MySqlReader($this);
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
		if (!isset($this->positions[$parameter]))
		{
			throw new MySqlStatementException
			(
				$this,
				sprintf
				(
					'Can not set parameter by value, not in statement: parameter %s, statement %s',
					$parameter,
					$this->statement
				)
			);
		}

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