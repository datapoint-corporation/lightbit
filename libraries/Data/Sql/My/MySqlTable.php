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

namespace Lightbit\Data\Sql\My;

use \Lightbit\Data\Sql\My\MySqlDatabase;
use \Lightbit\Data\Sql\My\MySqlObject;

use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlTable;

/**
 * MySqlTable.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlTable extends MySqlObject implements ISqlTable
{
	/**
	 * The columns.
	 *
	 * @type array
	 */
	private $columns;

	/**
	 * The database.
	 *
	 * @type ISqlDatabase
	 */
	private $database;

	/**
	 * The primary key.
	 *
	 * @type array
	 */
	private $primaryKey;

	/**
	 * Constructor.
	 *
	 * @param MySqlDatabase $database
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
	public function __construct(MySqlDatabase $database, array $schemata, array $columns, array $constraints)
	{
		parent::__construct($schemata['TABLE_NAME']);

		$this->database = $database;
		$this->columns = [];
		$this->primaryKey = [];

		$scope = 
		[
			'TABLE_CATALOG' => 'def', 
			'TABLE_SCHEMA' => $schemata['TABLE_SCHEMA'],
			'TABLE_NAME' => $schemata['TABLE_NAME']
		];

		foreach ($columns as $i => $column)
		{
			if (__map_match($column, $scope))
			{
				$instance = new MySqlColumn($this, $column, $constraints);
				$this->columns[$instance->getName()] = $instance;
			}
		}

		$scope = 
		[
			'CONSTRAINT_CATALOG' => 'def', 
			'CONSTRAINT_SCHEMA' => $schemata['TABLE_SCHEMA'],
			'TABLE_CATALOG' => 'def',
			'TABLE_SCHEMA' => $schemata['TABLE_SCHEMA'],
			'TABLE_NAME' => $schemata['TABLE_NAME']
		];

		foreach ($constraints as $i => $constraint)
		{
			if (__map_match($constraint, $scope))
			{
				if ($constraint['CONSTRAINT_NAME'] === 'PRIMARY')
				{
					$this->primaryKey[] = $constraint['COLUMN_NAME'];
				}
			}
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
			throw new MySqlException
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