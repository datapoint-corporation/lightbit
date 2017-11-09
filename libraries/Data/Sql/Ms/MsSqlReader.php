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

use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * ISqlReader.
 *
 * A SQL reader is a basic stream reader that fetches one result at a time and,
 * as such, should be closed and safely disposed after use. If it's left open,
 * depending on the underlying database management system, you may not be able
 * to prepare and/or run the next statement and memory leaks may have a 
 * negative impact on the application performance.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlReader extends Object implements ISqlReader
{
	/**
	 * The state.
	 *
	 * @var bool
	 */
	private $closed;

	/**
	 * The fields.
	 *
	 * @var int
	 */
	private $fields;

	/**
	 * The fields count.
	 *
	 * @var int
	 */
	private $fieldsCount;

	/**
	 * The sql connection.
	 *
	 * @var ISqlConnection
	 */
	private $msSqlConnection;

	/**
	 * The sql statement.
	 *
	 * @var ISqlStatement
	 */
	private $msSqlStatement;

	/**
	 * The connection internal handle.
	 *
	 * @var resource
	 */
	private $sqlsrv;

	/**
	 * The statement internal handle.
	 *
	 * @var resource
	 */
	private $stmt;

	/**
	 * Constructor.
	 *
	 * @param MsSqlStatement $msSqlStatement
	 *	The statement.
	 */
	public function __construct(MsSqlStatement $msSqlStatement)
	{
		$this->closed = true;

		$this->sqlsrv = $msSqlStatement->getSqlsrv();
		$this->stmt = $msSqlStatement->getStmt();

		$this->msSqlConnection = $msSqlStatement->getSqlConnection();
		$this->msSqlStatement = $msSqlStatement;

		if (! ($this->fields = sqlsrv_field_metadata($this->stmt)))
		{
			throw new MsSqlStatementException
			(
				$this->msSqlStatement,
				sprintf
				(
					'Can not read through statement result sets, not available: statement %s', 
					$this->msSqlStatement->toString()
				),
				$this->msSqlStatement->getSqlConnection()->getExceptionStack()
			);
		}

		$this->fieldsCount = count($this->fields);
		$this->closed = false;
	}

	/**
	 * Fetches the remaining results in the current result set and safely
	 * closes the reader.
	 *
	 * @param bool $numeric
	 *	The numeric fetch flag which, when set, causes all results to be
	 *	fetched as numeric arrays.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(bool $numeric = false) : array
	{
		$result = [];

		while ($row = $this->next($numeric))
		{
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Safely closes the reader.
	 *
	 * After closing, some information will persist regarding the parent
	 * transaction and any encountered errors. It has a neglible impact on
	 * memory consumption but, if you want to completely dispose of the reader,
	 * just unset it (unset($reader)).
	 */
	public function close() : void
	{
		if (!sqlsrv_cancel($this->stmt))
		{
			throw new MsSqlStatementException
			(
				$this->msSqlStatement,
				sprintf('Can not close statement reader, unknown error: statement %s', $this->msSqlStatement->toString()),
				$this->msSqlConnection->getExceptionStack()
			);
		}

		$this->closed = true;
	}

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection
	{
		return $this->msSqlConnection;
	}

	/**
	 * Gets the sql statement.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public function getSqlStatement() : ISqlStatement
	{
		return $this->msSqlStatement;
	}

	/**
	 * Checks if the reader is closed.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClosed() : bool
	{
		return $this->closed;
	}

	/**
	 * Fetches the next result in the current result set.
	 *
	 * Be aware that even if there is no more rows in the current result set,
	 * in which case null is returned instead, that does not mean the reader
	 * is, will be or has been closed!
	 *
	 * @return array
	 *	The next result.
	 */
	public function next(bool $numeric = false) : ?array
	{
		if ($this->closed)
		{
			throw new MsSqlStatementException
			(
				$this->msSqlStatement,
				sprintf
				(
					'Can not read next result in result set, reader is closed: statement %s',
					$this->msSqlStatement->toString()
				)
			);
		}

		$result = sqlsrv_fetch_array
		(
			$this->stmt, 
			($numeric ? SQLSRV_FETCH_NUMERIC : SQLSRV_FETCH_ASSOC)
		);

		if ($result)
		{
			foreach ($result as $i => $value)
			{
				$result[$i] = 
				(
					isset($value) 
					? __type_to_string($value) 
					: null
				);
			}

			return $result;
		}

		if ($result === false)
		{
			throw new MsSqlStatementException
			(
				$this->msSqlStatement,
				sprintf
				(
					'Can not read next result in result set, unexpected error: statement %s',
					$this->msSqlStatement->toString()
				),
				$this->msSqlConnection->getExceptionStack()
			);
		}

		return null;
	}

	/**
	 * Fetches the next result in the current result set, returning the
	 * first cell after closing the reader.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * @return string
	 *	The result.
	 */
	public function scalar() : ?string
	{
		$row = $this->next(true);
		$result = ($row ? $row[0] : null);

		$this->close();

		return $result;
	}

	/**
	 * Fetches the next result in the current result set and returns it 
	 * after closing the reader.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * In this case, if there are no more rows within the current result set,
	 * the reader is still implicitly closed.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(bool $numeric = false) : ?array
	{
		$result = $this->next($numeric);
		$this->close();
		
		return $result;
	}
}