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
use \Lightbit\Data\Sql\ISqlReader;

/**
 * ISqlStatement.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlStatement
{
	/**
	 * Closes the statement.
	 */
	public function close() : void;

	/**
	 * Executes the statement.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(array $arguments = null) : int;

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection;

	/**
	 * Checks the statement status.
	 *
	 * @return bool
	 *	The statement status.
	 */
	public function isClosed() : bool;

	/**
	 * Executes the statement as a scalar query.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function scalar(array $arguments = null); // : mixed;

	/**
	 * Executes the statement as a query.
	 *
	 * @param array $arguments
	 *	The arguments.
	 *
	 * @return ISqlReader
	 *	The sql reader.
	 */
	public function query(array $arguments = null) : ISqlReader;
}