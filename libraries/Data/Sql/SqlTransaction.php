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
use \Lightbit\Data\Sql\ISqlTransaction;

/**
 * SqlTransaction.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class SqlTransaction extends Object implements ISqlTransaction
{
	/**
	 * Performs a commit, closing the transaction.
	 *
	 * Once a transaction is closed, it can not be modified: any future commit
	 * and rollback procedures will result in an exception being thrown.
	 */
	abstract public function commit() : void;

	/**
	 * Checks if the transaction is closed.
	 *
	 * @return bool
	 *	The result.
	 */
	abstract public function isClosed() : bool;

	/**
	 * Performs a rollback, closing the transaction.
	 *
	 * Once a transaction is closed, it can not be modified: any future commit
	 * and rollback procedures will result in an exception being thrown.
	 */
	abstract public function rollback() : void;

	/**
	 * Starts the transaction.
	 */
	abstract public function start() : void;

	/**
	 * The connection.
	 *
	 * @type ISqlConnection
	 */
	private $connection;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param ISqlConnection $connection
	 *	The connection.
	 */
	public function __construct(ISqlConnection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
	{
		if (!$this->id)
		{
			$this->id = 'lb' . __lightbit_next_id();
		}

		return $this->id;
	}

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public function getConnection() : ISqlConnection
	{
		return $this->connection;
	}
}
