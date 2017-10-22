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

use \Lightbit\Base\IChannel;
use \Lightbit\Base\IComponent;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\ISqlStatementFactory;

/**
 * ISqlConnection.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlConnection extends IComponent, IChannel
{
	/**
	 * Creates, prepares and executes a query statement, pre-fetching
	 * all results within the first result set.
	 *
	 * @param string $statement
	 *	The statement to prepare.
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
	public function all(string $statement, array $parameters = null, bool $numeric = false) : array;

	public function close() : void;

	/**
	 * Creates, prepares and executes a statement, returning the number
	 * of affected rows.
	 *
	 * @param string $statement
	 *	The statement to prepare.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(string $statement, array $parameters = null) : int;

	/**
	 * Gets the database.
	 *
	 * Since this function fetches the schema for the database and all of its
	 * objects (tables, views, columns, etc.), it can become costly and the
	 * use of a fast memory cache component is highly recommended.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase;

	/**
	 * Gets the schema.
	 *
	 * Since this function fetches the schema for all databases and all of its
	 * objects (tables, views, columns, etc.), it can become costly and the
	 * use of a fast memory cache component is highly recommended.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema;

	/**
	 * Gets the statement factory.
	 *
	 * The statement factory can be used to automatically generate complex
	 * statements based on a dynamic criteria and/or other values - its most
	 * common usage is shown throughout the implementation of active records.
	 *
	 * @return ISqlStatementFactory
	 *	The statement factory.
	 */
	public function getStatementFactory() : ISqlStatementFactory;

	/**
	 * Prepares and executes a query statement.
	 *
	 * Since this function generates a reader, that reader should be manually
	 * closed and safely disposed before continuing to the next procedure.
	 *
	 * If the reader is not closed, depending on the underlying database
	 * management system, you might be unable to prepare and/or execute
	 * the next statement and memory leaks may have a negative impact on
	 * the application performance.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return ISqlReader
	 *	The reader.
	 */
	public function query(string $statement, array $parameters = null) : ISqlReader;

	/**
	 * Quotes a statement.
	 *
	 * The given statement should have its fields quoted with double quotes and
	 * this method will make the necessary replacements to ensure the quoting
	 * sequence matches the one expected by the underlying database management
	 * system.
	 *
	 * An example: $sql->quote('SELECT * FROM "USER"');
	 *
	 * @param string $statement
	 *	The statement to quote.
	 */
	public function quote(string $statement) : string;

	/**
	 * Prepares and executes a query statement, pre-fetching and returning
	 * the first cell within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return string
	 *	The result.
	 */
	public function scalar(string $statement, array $parameters = null) : ?string;

	/**
	 * Prepares and executes a query statement, pre-fetching and returning
	 * the first result within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(string $statement, array $parameters = null, bool $numeric = false) : ?array;

	/**
	 * Prepares a statement for multiple execution.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function statement(string $statement) : ISqlStatement;

	/**
	 * Starts a new transaction.
	 *
	 * Once the applicable transact procedures are complete, you must explicitly
	 * commit (or rollback) the transaction. 
	 *
	 * If the transaction is left open, a rollback is automatically issued
	 * during on connection close, more often than not, at the end of
	 * the current request, even if the persistent flag is set.
	 *
	 * @return ISqlTransaction
	 *	The transaction.
	 */
	public function transaction() : ISqlTransaction;
}
