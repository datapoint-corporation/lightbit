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

use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlDatabase;

/**
 * ISqlTable.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlTable extends ISqlObject
{
	/**
	 * Gets a column.
	 *
	 * @param string $column
	 *	The column name.
	 *
	 * @return ISqlColumn
	 *	The column.
	 */
	public function getColumn(string $column) : ISqlColumn;

	/**
	 * Gets all columns.
	 *
	 * @return array
	 *	The columns.
	 */
	public function getColumns() : array;

	/**
	 * Gets the database.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase;

	/**
	 * Gets the table primary key.
	 *
	 * The primary key is represented by a numeric array of column names which,
	 * if the table has no primary key, is going to be empty.
	 *
	 * @return array
	 *	The table primary key. 
	 */
	public function getPrimaryKey() : array;

	/**
	 * Gets the schema.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema;

	/**
	 * Checks if a column exists.
	 *
	 * @param string $column
	 *	The column name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasColumn(string $column) : bool;

	/**
	 * Checks if the primary key exists.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPrimaryKey() : bool;
}