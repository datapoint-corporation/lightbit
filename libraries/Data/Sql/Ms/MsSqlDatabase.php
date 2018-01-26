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

use \Lightbit\Data\Traversing\ArrayIterator;
use \Lightbit\Data\Sql\Ms\MsSqlColumn;
use \Lightbit\Data\Sql\Ms\MsSqlObject;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlTable;

/**
 * MsSqlDatabase.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlDatabase extends MsSqlObject implements ISqlDatabase
{
	/**
	 * The schema.
	 *
	 * @var ISqlSchema
	 */
	private $schema;

	/**
	 * The tables.
	 *
	 * @var array
	 */
	private $tables;

	/**
	 * Constructor.
	 *
	 * @param MsSqlSchema $schema
	 *	The schema.
	 *
	 * @param array $schemata
	 *	The database schemata.
	 *
	 * @param array $columns
	 *	The columns schemata.
	 *
	 * @param array $constraints
	 *	The constraints schemata.
	 */
	public function __construct(MsSqlSchema $schema, array $schemata, array $tables, array $columns, array $constraints)
	{
		parent::__construct($schemata['DATABASE_NAME']);

		$this->schema = $schema;
		$this->tables = [];

		$scope = [];
		$scope['SCHEMA_NAME'] = $schemata['SCHEMA_NAME'];
		$scope['DATABASE_NAME'] = $schemata['DATABASE_NAME'];

		foreach ((new ArrayIterator($tables))->with($scope) as $i => $table)
		{
			$instance = new MsSqlTable($this, $table, $columns, $constraints);
			$this->tables[$instance->getName()] = $instance;
		}
	}

	/**
	 * Gets the schema.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema
	{
		return $this->schema;
	}

	/**
	 * Gets a table.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable(string $table) : ISqlTable
	{
		if (!isset($this->tables[$table]))
		{
			throw new MsSqlException
			(
				sprintf
				(
					'Can not get table from database, not set: table %s, database %s, schema %s',
					$table,
					$this->getName(),
					$this->schema->getName()
				)
			);
		}

		return $this->tables[$table];
	}

	/**
	 * Gets all tables.
	 *
	 * @return array
	 *	The tables.
	 */
	public function getTables() : array
	{
		return $this->tables;
	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $table
	 *	The table name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasTable(string $table) : bool
	{
		return (isset($this->tables[$table]));
	}
}