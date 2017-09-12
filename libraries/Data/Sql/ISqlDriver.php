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

use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\ISqlStatementFactory;

/**
 * ISqlDriver.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlDriver
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
	public function __construct(ISqlConnection $connection, array $configuration = null);

	/**
	 * Gets the database.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the last insert row identifier.
	 *
	 * @return int
	 *	The last insert row identifier.
	 */
	public function getLastInsertID() : int;

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public function getConnection() : ISqlConnection;

	/**
	 * Quotes a statement by replacing the ANSI double quotes with the
	 * proper quote character sequence.
	 *
	 * @param string $statement
	 *	The statement to quote.
	 *
	 * @return string
	 *	The result.
	 */
	public function quote(string $statement) : string;

	/**
	 * Creates and prepares a statement.
	 *
	 * @param string $statement
	 *	The statement to create and prepare, as a string.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public function statement(string $statement) : ISqlStatement;

	/**
	 * Creates a sql statement factory.
	 *
	 * @return ISqlStatementFactory
	 *	The sql statement factory.
	 */
	public function statementFactory() : ISqlStatementFactory;

	/**
	 * Creates a transaction.
	 *
	 * @return ISqlTransaction
	 *	The transaction.
	 */
	public function transaction() : ISqlTransaction;
}