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

namespace Lightbit\Data\Sql\Ms;

use \Lightbit\Data\Sql\Ms\MsSqlDatabase;
use \Lightbit\Data\Sql\Ms\MsSqlObject;
use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlTable;
use \Lightbit\Data\Traversing\ArrayIterator;

/**
 * MsSqlTable.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlTable extends MsSqlObject implements ISqlTable
{
	/**
	 * The columns.
	 *
	 * @var array
	 */
	private $columns;

	/**
	 * The database.
	 *
	 * @var ISqlDatabase
	 */
	private $database;

	/**
	 * The primary key.
	 *
	 * @var array
	 */
	private $primaryKey;

	/**
	 * Constructor.
	 *
	 * @param MsSqlDatabase $database
	 *	The database.
	 *
	 * @param array $schemata
	 *	The table schemata.
	 *
	 * @param array $columns
	 *	The columns schemata.
	 *
	 * @param array $constraints
	 *	The constraints schemata.
	 */
	public function __construct(MsSqlDatabase $database, array $schemata, array $columns, array $constraints)
	{
		parent::__construct($schemata['TABLE_NAME']);

		$this->database = $database;
		$this->columns = [];
		$this->primaryKey = [];

		$scope = [];
		$scope['SCHEMA_NAME'] = $database->getSchema()->getName();
		$scope['DATABASE_NAME'] = $database->getName();
		$scope['TABLE_NAME'] = $schemata['TABLE_NAME'];

		foreach ((new ArrayIterator($columns))->with($scope) as $i => $column)
		{
			$instance = new MsSqlColumn($this, $column, $constraints);
			$this->columns[$instance->getName()] = $instance;
		}

		$scope['CONSTRAINT_TYPE'] = 'PRIMARY KEY';

		foreach ((new ArrayIterator($constraints))->with($scope) as $i => $constraint)
		{
			$this->primaryKey[] = $constraint['COLUMN_NAME'];
		}
	}

	/**
	 * Gets a column.
	 *
	 * @param string $column
	 *	The column name.
	 *
	 * @return ISqlColumn
	 *	The column.
	 */
	public function getColumn(string $column) : ISqlColumn
	{
		if (!isset($this->columns[$column]))
		{
			throw new MsSqlException
			(
				sprintf
				(
					'Can not get column from database table, not set: column %s, table %s, database %s, schema %s',
					$column,
					$this->getName(),
					$this->database->getName(),
					$this->database->getSchema()->getName()
				)
			);
		}

		return $this->columns[$column];
	}

	/**
	 * Gets all columns.
	 *
	 * @return array
	 *	The columns.
	 */
	public function getColumns() : array
	{
		return $this->columns;
	}

	/**
	 * Gets the database.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase
	{
		return $this->database;
	}

	/**
	 * Gets the table primary key.
	 *
	 * The primary key is represented by a numeric array of column names which,
	 * if the table has no primary key, is going to be empty.
	 *
	 * @return array
	 *	The table primary key. 
	 */
	public function getPrimaryKey() : array
	{
		return $this->primaryKey;
	}

	/**
	 * Gets the schema.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema
	{
		return $this->database->getSchema();
	}

	/**
	 * Checks if a column exists.
	 *
	 * @param string $column
	 *	The column name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasColumn(string $column) : bool
	{
		return (isset($this->columns[$column]));
	}

	/**
	 * Checks if the primary key exists.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPrimaryKey() : bool
	{
		return !!$this->primaryKey;
	}
}