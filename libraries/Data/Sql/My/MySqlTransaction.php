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
use \Lightbit\Data\Sql\My\MySqlConnection;
use \Lightbit\Data\Sql\SqlException;

use \Lightbit\Data\Sql\ISqlTransaction;

/**
 * MySqlTransaction.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlTransaction extends Object implements ISqlTransaction
{
	/**
	 * The state.
	 *
	 * @type bool
	 */
	private $closed;

	/**
	 * The MySQL connection.
	 *
	 * @type MySqlConnection;
	 */
	private $mySqlConnection;

	/**
	 * Constructor.
	 *
	 * @param MySqlConnection
	 *	The MySQL connection.
	 */
	public function __construct(MySqlConnection $mySqlConnection)
	{
		$this->closed = true;
		$this->mySqlConnection = $mySqlConnection;
		$this->mySqlConnection->run('START TRANSACTION');
		$this->closed = false;
	}

	/**
	 * Performs a commit.
	 *
	 * Be aware once the procedure completes, the transaction is closed and any
	 * future statements are not part of it and might be impossible to rollback.
	 */
	public function commit() : void
	{
		$this->mySqlConnection->run('COMMIT');
	}

	/**
	 * Checks the transaction state.
	 *
	 * Be aware if the transaction is closed, any future statements are not part
	 * of it and might be impossible to rollback.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClosed() : bool
	{
		return $this->closed;
	}

	/**
	 * Performs a rollback.
	 *
	 * Be aware once the procedure completes, the transaction is closed and any
	 * future statements are not part of it and might be impossible to rollback.
	 */
	public function rollback() : void
	{
		$this->mySqlConnection->run('ROLLBACK');
	}
}