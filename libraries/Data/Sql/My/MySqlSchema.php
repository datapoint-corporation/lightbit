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

use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlSchema;

/**
 * MySqlSchema.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlSchema extends MySqlObject implements ISqlSchema
{
	/**
	 * The databases.
	 *
	 * @var array
	 */
	private $databases;

	/**
	 * Constructor.
	 *
	 * @param string $schema
	 *	The schema name.
	 *
	 * @param string $databases
	 *	The databases schemata.
	 *
	 * @param array $tables
	 *	The tables schemata.
	 *
	 * @param array $columns
	 *	The columns schemata.
	 *
	 * @param array $constraints
	 *	The constraints schemata.
	 */
	public function __construct(string $schema, array $databases, array $tables, array $columns, array $constraints)
	{
		parent::__construct($schema);

		$this->databases = [];

		$scope = [];
		$scope['SCHEMA_NAME'] = $schema;

		foreach ($databases as $i => $database)
		{
			if (__map_match($scope, $database))
			{
				$instance = new MySqlDatabase($this, $database, $tables, $columns, $constraints);
				$this->databases[$instance->getName()] = $instance;
			}
		}
	}

	/**
	 * Gets a database schema.
	 *
	 * @param string $database
	 *	The database name.
	 *
	 * @return ISqlDatabase
	 *	The database schema.
	 */
	public function getDatabase(string $database) : ISqlDatabase
	{
		if (!isset($this->databases[$database]))
		{
			throw new MySqlException
			(
				sprintf
				(
					'Can not get database from schema, not set: database %s, schema %s',
					$database,
					$this->getName()
				)
			);
		}

		return $this->databases[$database];
	}

	/**
	 * Gets all databases schema.
	 *
	 * @return array
	 *	The databases.
	 */
	public function getDatabases() : array
	{
		return $this->databases;
	}

	/**
	 * Checks if a database schema is available.
	 *
	 * @param string $database
	 *	The database name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasDatabase(string $database) : bool
	{
		return (isset($this->databases[$database]));
	}
}