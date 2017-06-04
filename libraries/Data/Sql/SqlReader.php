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
use \Lightbit\Data\Sql\SqlConnection;
use \Lightbit\Data\Sql\SqlStatement;
use \Lightbit\Helpers\ObjectHelper;

/**
 * ISqlReader.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SqlReader extends Object implements ISqlReader
{
	/**
	 * The internal statement handle.
	 *
	 * @type PDOStatement
	 */
	private $pdoStatement;

	/**
	 * The sql connection.
	 *
	 * @type SqlConnection
	 */
	private $sqlConnection;

	/**
	 * The sql statement.
	 *
	 * @type SqlStatement
	 */
	private $sqlStatement;

	/**
	 * Constructor.
	 *
	 * @param SqlStatement $sqlStatement
	 *	The sql statement to read from.
	 *
	 * @param array $configuration
	 *	The sql reader configuration.
	 */
	public function __construct(SqlStatement $sqlStatement, array $configuration = null)
	{
		$this->sqlConnection = $sqlStatement->getSqlConnection();
		$this->sqlStatement = $sqlStatement;

		$this->pdoStatement = $sqlStatement->getPdoStatement();

		try
		{
			$this->pdoStatement->execute();
		}
		catch (\PDOException $e)
		{
			throw new SqlReaderException
			(
				$this,
				sprintf('Can not execute query statement: %s', lcfirst($e->getMessage())),
				$e
			);
		}

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Fetches the remaining results in the current result set.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The results.
	 */
	public final function all(bool $numeric = false) : array
	{
		if (!$this->pdoStatement)
		{
			throw new SqlReaderException
			(
				$this,
				'Can not continue to next result: sql reader is closed'
			);
		}

		try
		{
			$results = [];
			$fetchStyle = ($numeric ? \PDO::FETCH_NUM : \PDO::FETCH_ASSOC);

			while ($result = $this->pdoStatement->fetch($fetchStyle))
			{
				$results[] = $result;
			}

			return $results;
		}
		catch (\PDOException $e)
		{
			throw new SqlReaderException
			(
				$this,
				sprintf('Can not continue to next result set: %s', lcfirst($e->getMessage())),
				$e
			);
		}
	}

	/**
	 * Closes the sql reader.
	 */
	public final function close() : void
	{
		if ($this->pdoStatement)
		{
			do while ($this->next());
			while ($this->continue());

			$this->pdoStatement = null;
		}		
	}

	/**
	 * Continues to the next result set, if available.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function continue() : bool
	{
		if (!$this->pdoStatement)
		{
			throw new SqlReaderException
			(
				$this,
				'Can not continue to next result set: sql reader is closed'
			);
		}

		try
		{
			return $this->pdoStatement->nextRowset();
		}
		catch (\PDOException $e)
		{
			throw new SqlReaderException
			(
				$this,
				sprintf('Can not continue to next result set: %s', lcfirst($e->getMessage())),
				$e
			);
		}
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
	 * Gets the sql statement.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public final function getSqlStatement() : ISqlStatement
	{
		return $this->sqlStatement;
	}

	/**
	 * Checks the sql reader status.
	 *
	 * @return bool
	 *	The sql reader status.
	 */
	public final function isClosed() : bool
	{
		return !isset($this->pdoStatement);
	}

	/**
	 * Fetches the next result in the current result set.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The result.
	 */
	public final function next(bool $numeric = false) : ?array
	{
		if (!$this->pdoStatement)
		{
			throw new SqlReaderException
			(
				$this,
				'Can not continue to next result: sql reader is closed'
			);
		}

		try
		{
			return ($result = $this->pdoStatement->fetch($numeric ? \PDO::FETCH_NUM : \PDO::FETCH_ASSOC)) ? $result	: null;
		}
		catch (\PDOException $e)
		{
			throw new SqlReaderException
			(
				$this,
				sprintf('Can not continue to next result set: %s', lcfirst($e->getMessage())),
				$e
			);
		}
	}

	/**
	 * Fetches the next cell in the current result set and returns it, 
	 * disposing all remaining resulsts by closing.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function scalar() // : mixed
	{
		$result = $this->next(true);

		$this->close();

		return $result[0];
	}

	/**
	 * Fetches the next result in the current result set and returns it, 
	 * disposing all remaining resulsts by closing.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The result.
	 */
	public final function single(bool $numeric = false) : ?array
	{
		$result = $this->next($numeric);

		$this->close();
		
		return $result;
	}
}