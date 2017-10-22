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

use \Lightbit\Data\Sql\ISqlObject;

/**
 * ISqlSchema.
 *
 * Usage of this class should not be confused with the definiton of "schema"
 * from any database management system: this framework assumes one schema
 * contains many databases, each database contains tables, each table contains
 * columns and constraints and each column contains attributes.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlSchema extends ISqlObject
{
	/**
	 * Gets a database schema.
	 *
	 * @param string $database
	 *	The database name.
	 *
	 * @return ISqlDatabase
	 *	The database schema.
	 */
	public function getDatabase(string $database) : ISqlDatabase;

	/**
	 * Gets all databases schema.
	 *
	 * @return array
	 *	The databases.
	 */
	public function getDatabases() : array;

	/**
	 * Checks if a database schema is available.
	 *
	 * @param string $database
	 *	The database name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasDatabase(string $database) : bool;
}