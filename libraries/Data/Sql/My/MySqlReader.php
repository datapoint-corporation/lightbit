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
 * MySqlReader.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlReader extends Object implements ISqlReader
{
	/**
	 * The closed state.
	 *
	 * @type bool
	 */
	private $closed;

	/**
	 * The fields schemata.
	 *
	 * @type array
	 */
	private $fields;

	/**
	 * The fields count.
	 *
	 * @type int
	 */
	private $fieldsCount;

	/**
	 * The row.
	 *
	 * @type array
	 */
	private $row;

	/**
	 * The result meta data.
	 *
	 * @type mysqli_result
	 */
	private $meta;

	/**
	 * The sql statement.
	 *
	 * @type ISqlStatement
	 */
	private $mySqlStatement;

	/**
	 * The sql statement handle.
	 *
	 * @type mysqli_stmt
	 */
	private $mysqliStatement;

	/**
	 * Constructor.
	 *
	 * @param MySqlStatement $mySqlStatement
	 *	The statement to read from.
	 */
	public function __construct(MySqlStatement $mySqlStatement)
	{
		$this->closed = true;
		$this->mySqlStatement = $mySqlStatement;

		$this->mysqliStatement = $mySqlStatement->getMysqliStatement();

		if (! ($this->meta = mysqli_stmt_result_metadata($this->mysqliStatement)))
		{
			throw new MySqlStatementException
			(
				$this->mySqlStatement,
				sprintf
				(
					'Can not get result set meta data from statement: statement %s',
					$this->mySqlStatement->toString()
				)
			);
		}

		if (! ($this->fields = mysqli_fetch_fields($this->meta)))
		{
			throw new MySqlStatementException
			(
				$this->mySqlStatement,
				sprintf
				(
					'Can not get result set fields meta data from statement: statement %s',
					$this->mySqlStatement->toString()
				)
			);
		}

		$this->fieldsCount = count($this->fields);

		// We are going to use a little memory hack here to get the statement
		// to bind to a numeric array matching the existing fields.
		$t = array_pad([], $this->fieldsCount, null);

		if (!mysqli_stmt_bind_result($this->mysqliStatement, ...$t))
		{
			throw new MySqlStatementException
			(
				$this->mySqlStatement,
				sprintf
				(
					'Can not bind result set to memory: statement %s',
					$this->mySqlStatement->toString()
				)
			);
		}

		$this->row = & $t;
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

		$this->close();

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
		if ($result = mysqli_stmt_result_metadata($this->mysqliStatement))
		{
			mysqli_stmt_free_result($this->mysqliStatement);
		}

		if (!mysqli_stmt_close($this->mysqliStatement))
		{
			throw new MySqlStatementException
			(
				$this,
				sprintf
				(
					'%s %d', 
					mysqli_stmt_error($this->mysqliStatement),
					mysqli_stmt_errno($this->mysqliStatement)
				)
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
		return $this->mySqlStatement->getSqlConnection();
	}

	/**
	 * Gets the sql statement.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public function getSqlStatement() : ISqlStatement
	{
		return $this->mySqlStatement;
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
		return !mysqli_stmt_result_metadata($this->mysqliStatement);
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
		if (mysqli_stmt_fetch($this->mysqliStatement))
		{
			$result = [];

			if ($numeric)
			{
				for ($i = 0; $i < $this->fieldsCount; ++$i)
				{
					$result[$i] = __type_to_string($this->row[$i]);
				}
			}
			else
			{
				for ($i = 0; $i < $this->fieldsCount; ++$i)
				{
					$result[($this->fields[$i])->name] = __type_to_string($this->row[$i]);
				}
			}

			return $result;
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
		$result = $this->next(true);
		$this->close();

		if ($result)
		{
			return $result[0];
		}

		return null;
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