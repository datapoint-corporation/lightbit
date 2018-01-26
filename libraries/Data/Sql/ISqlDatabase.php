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

use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlObject;
use \Lightbit\Data\Sql\ISqlSchema;

/**
 * ISqlDatabase.
 *
 * Instances of this class represent an existing database and contain 
 * information all of its objects, including any tables, views, constraints
 * and more.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlDatabase extends ISqlObject
{
	/**
	 * Gets the schema.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema;

	/**
	 * Gets a table.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable(string $table) : ISqlTable;

	/**
	 * Gets all tables.
	 *
	 * @return array
	 *	The tables.
	 */
	public function getTables() : array;

	/**
	 * Checks if a table exists.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasTable(string $table) : bool;
}