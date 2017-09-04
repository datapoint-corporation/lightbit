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

namespace \Lightbit\Data\Sql;

use \Lightbit\Data\Sql\ISqlModel;

/**
 * ISqlActiveRecord.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlActiveRecord extends ISqlModel
{
	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * all results as an instance of this model, optionally based on a given
	 * select criteria.
	 *
	 * @param array $criteria
	 *	The select criteria configuration.
	 *
	 * @return array
	 *	The result.
	 */
	public function all(array $criteria = null) : array;

	/**
	 * Creates, prepares and executes a delete statement matching the
	 * model instance identity.
	 *
	 * If the instance is new (see: isNew), this method performs 
	 * no action at all.
	 */
	public function delete() : void;

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
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * a single result matching the given attributes as an instance of this
	 * model.
	 *
	 * @param array $attributes
	 *	The attributes to match.
	 *
	 * @return ISqlModel
	 *	The result.
	 */
	public function match(array $attributes) : ?ISqlModel;

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * the first result as an instance of this model, optionally based on a
	 * given select criteria.
	 *
	 * @param array $criteria
	 *	The select criteria configuration.
	 *
	 * @return ISqlModel
	 *	The result.
	 */
	public function one(array $criteria = null) : ?ISqlModel;

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
}