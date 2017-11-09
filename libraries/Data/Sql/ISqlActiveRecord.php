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

use \Lightbit\Data\IModel;
use \Lightbit\Data\Sql\ISqlTable;

/**
 * ISqlActiveRecord.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlActiveRecord extends IModel
{	
	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve all records matching the current criteria.
	 * 
	 * @return array
	 *	The results.
	 */
	public function all() : array;

	/**
	 * Performs a commit.
	 */
	public function commit() : void;

	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve the number of records matching the current criteria.
	 * 
	 * @return int
	 *	The result.
	 */
	public function count() : int;

	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * delete this active record.
	 */
	public function delete() : void;

	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * check the existence of records matching the current criteria.
	 * 
	 * @return bool
	 *	The result.
	 */
	public function exists() : bool;

	/**
	 * Gets the attributes with update.
	 *
	 * If a commit was performed prior to invoking this function, the
	 * attributes that have been modified since then will be returned as an
	 * associative array. However, if a commit was not performed, all
	 * attributes will be returned instead.
	 *
	 * @param bool $inverse
	 *	When set, the original attributes are returned instead.
	 *
	 * @return array
	 *	The difference.
	 */
	public function getAttributesWithUpdate(bool $inverse = false) : array;
	
	/**
	 * Gets the criteria.
	 * 
	 * @return ISqlCriteria
	 *	The criteria.
	 */
	public function getCriteria() : ISqlCriteria;

	/**
	 * Gets the identity.
	 *
	 * @return array
	 *	The identity.
	 */
	public function getID() : array;

	/**
	 * Gets the primary key.
	 *
	 * @return array
	 *	The primary key.
	 */
	public function getPrimaryKey() : array;

	/**
	 * Gets the table.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable() : ISqlTable;

	/**
	 * Gets the table name.
	 *
	 * @return string
	 *	The table name.
	 */
	public function getTableName() : string;

	/**
	 * Checks if an attribute has been modified.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributeUpdate(string $attribute) : bool;

	/**
	 * Checks if an attribute has been modified.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributesUpdate() : bool;

	/**
	 * Checks if it is new.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNew() : bool;

	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve a single record matching the current criteria.
	 * 
	 * @return ISqlModel
	 *	The result.
	 */
	public function single() : ?ISqlModel;

	/**
	 * Performs a rollback.
	 */
	public function rollback() : void;

	/**
	 * Creates, prepares and executes a insert or update statement matching
	 * the changes made to the model attributes.
	 *
	 * If the instance is new (see: isNew), a new record will be inserted
	 * into the applicable table and, if not, the matching record will be
	 * updated as necessary.
	 *
	 * The model identity (see: getID) will be updated if necessary
	 * at the end of this procedure.
	 */
	public function save() : void;
	
	/**
	 * Sets additional comparisons.
	 * 
	 * @param array $comparisons
	 *	The comparisons.
	 * 
	 * @return ISqlActiveRecord
	 *	This instance.
	 */
	public function with(array $comparisons) : ISqlActiveRecord;
}
