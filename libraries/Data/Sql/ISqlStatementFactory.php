<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2018 Datapoint — Sistemas de Informação, Unipessoal, Lda.
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
use \Lightbit\Data\Sql\ISqlCriteria;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * ISqlStatementFactory.
 *
 * A statement factory is, more often than not, used by the abstract active
 * record implementation in order to quickly and reliably generate the delete,
 * insert, select and update statements necessary to make the applicable
 * changes to the database records.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlStatementFactory
{
	/**
	 * Creates a count statement.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The select criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function count(string $table, ?ISqlCriteria $criteria) : ISqlStatement;

	/**
	 * Creates a delete statement.
	 *
	 * @param array $table
	 *	The table name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The delete criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function delete(string $table, ?ISqlCriteria $criteria) : ISqlStatement;

	/**
	 * Creates an insert statement.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @param array $values
	 *	The values, indexed by field name.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function insert(string $table, array $values) : ISqlStatement;

	/**
	 * Creates a select statement.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The select criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function select(string $table, ?ISqlCriteria $criteria) : ISqlStatement;

	/**
	 * Creates an update statement.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @param array $values
	 *	The values, indexed by field name.
	 *
	 * @param ISqlCriteria $criteria
	 *	The update criteria.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function update(string $table, array $values, ?ISqlCriteria $criteria) : ISqlStatement;
}