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
interface ISqlStatement
{
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
	public function all(array $parameters = null, bool $numeric = false) : array;

	/**
	 * Executes the statement, returning the number of affected rows.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(array $parameters = null) : int;

	/**
	 * Gets the connection.
	 *
	 * @return ISqlConnection
	 *	The connection.
	 */
	public function getSqlConnection() : ISqlConnection;

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
	public function query(array $parameters = null) : ISqlReader;

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
	public function scalar(array $parameters = null) : ?string;

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
	public function single(array $parameters = null, bool $numeric = false) : ?array;

	/**
	 * Sets a parameter.
	 *
	 * @param string $parameter
	 *	The parameter name.
	 *
	 * @param mixed $value
	 *	The parameter value.
	 */
	public function set(string $parameter, $value) : void;

	/**
	 * Sets all parameters.
	 *
	 * @param array $parameters
	 *	The parameters value, indexed by name.
	 */
	public function setAll(array $parameters) : void;

	/**
	 * Creates a string representation of this statement.
	 *
	 * @return string
	 *	The result.
	 */
	public function toString() : string;
}
