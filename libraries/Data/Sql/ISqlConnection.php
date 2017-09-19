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
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * ISqlConnection.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlConnection extends IComponent, IChannel
{
	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * all results.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(string $statement, array $arguments = null, bool $numeric = false) : array;

	/**
	 * Gets the last insert row identifier.
	 *
	 * @return int
	 *	The last insert row identifier.
	 */
	public function getLastInsertID() : int;

	/**
	 * Gets the database.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase;

	/**
	 * Gets the sql connection driver.
	 *
	 * @return ISqlDriver
	 *	The sql connection driver.
	 */
	public function getDriver() : ISqlDriver;

	/**
	 * Gets the sql statement factory.
	 *
	 * @return ISqlStatementFactory
	 *	The sql statement factory.
	 */
	public function getStatementFactory() : ISqlStatementFactory;

	/**
	 * Gets the user for authentication.
	 *
	 * @return string
	 *	The user for authentication.
	 */
	public function getUser() : ?string;

	/**
	 * Creates, prepares and executes a statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(string $statement, array $arguments = null) : int;

	/**
	 * Checks for a password for authentication.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPassword() : bool;

	/**
	 * Checks for a user for authentication.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasUser() : bool;

	/**
	 * Creates, prepares and executes a query statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public function query(string $statement, array $arguments = null) : ISqlReader;

	/**
	 * Runs a command.
	 *
	 * Please beaware that this function does not perform any kind of escape
	 * procedures on the given command.
	 *
	 * For security reasons, you should never use this function with user
	 * input, even after validation.
	 *
	 * @param string $command
	 *	The command.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function run(string $command) : int;

	/**
	 * Creates, prepares and executes a scalar query statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function scalar(string $statement, array $arguments = null); // : mixed

	/**
	 * Sets the driver configuration.
	 *
	 * @param array $driverConfiguration
	 *	The driver configuration.
	 *
	 * @param bool $merge
	 *	The driver configuration merge flag.
	 */
	public function setDriverConfiguration(array $driverConfiguration, bool $merge = true) : void;

	/**
	 * Sets the dsn connection string.
	 *
	 * @param string $dsn
	 *	The dsn connection string.
	 */
	public function setDsn(string $dsn) : void;

	/**
	 * Sets the password for authentication.
	 *
	 * @param string $password
	 *	The password for authentication.
	 */
	public function setPassword(?string $password) : void;

	/**
	 * Sets the user for authentication.
	 *
	 * @param string $user
	 *	The user for authentication.
	 */
	public function setUser(?string $user) : void;

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * a single result.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(string $statement, array $arguments = null, bool $numeric = false) : ?array;

	/**
	 * Starts a transaction.
	 *
	 * @return ISqlTransaction
	 *	The transaction.
	 */
	public function startTransaction() : ISqlTransaction;

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
	 * Executes a transaction.
	 *
	 * @param \Closure $closure
	 *	The transaction closure.
	 *
	 * @return mixed
	 *	The transaction result.
	 */
	public function transaction(\Closure $closure); // : mixed;
}
