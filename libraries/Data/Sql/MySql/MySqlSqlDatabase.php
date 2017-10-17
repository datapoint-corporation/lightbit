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

namespace Lightbit\Data\Sql\MySql;

use \Lightbit\Base\Object;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlTable;
use \Lightbit\Exception;

/**
 * MySqlSqlDatabase.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class MySqlSqlDatabase extends Object implements ISqlDatabase
{
	/**
	 * The default character set.
	 *
	 * @type string
	 */
	private $defaultCharacterSet;

	/**
	 * The default collation.
	 *
	 * @type string
	 */
	private $defaultCollation;

	/**
	 * The name.
	 *
	 * @type string
	 */
	private $name;

	/**
	 * The tables.
	 *
	 * @type array
	 */
	private $tables;

	/**
	 * Constructor.
	 *
	 * @param array $database
	 *	The database schema.
	 *
	 * @param array $tables
	 *	The tables schema.
	 *
	 * @param array $columns
	 *	The columns schema.
	 *
	 * @param array $keys
	 *	The keys usage schema.
	 */
	public function __construct(array $database, array $tables, array $columns, array $keys)
	{
		$this->defaultCharacterSet = $database['DEFAULT_CHARACTER_SET_NAME'];
		$this->defaultCollation = $database['DEFAULT_COLLATION_NAME'];
		$this->name = $database['SCHEMA_NAME'];

		$this->tables = [];

		foreach ($tables as $i => $table)
		{
			if (($table['TABLE_SCHEMA'] === $database['SCHEMA_NAME'])
				&& ($table['TABLE_CATALOG'] === $database['CATALOG_NAME']))
			{
				$this->tables[$table['TABLE_NAME']] = new MySqlSqlTable($table, $columns, $keys);
			}
		}
	}

	private function getTablesCI() : array
	{
		return $this->tablesCI;
	}

	/**
	 * Gets the tables name.
	 *
	 * @return array
	 *	The tables name.
	 */
	public function getTablesName() : array
	{
		return array_values($this->tablesCI);
	}

	/**
	 * Gets the default character set.
	 *
	 * @return string
	 *	The default character set.
	 */
	public function getDefaultCharacterSet() : ?string
	{
		return $this->defaultCharacterSet;
	}

	/**
	 * Gets the default collation.
	 *
	 * @return string
	 *	The default collation.
	 */
	public function getDefaultCollation() : ?string
	{
		return $this->defaultCollation;
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Gets the table.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable(string $table) : ISqlTable
	{
		if (!isset($this->tables[$table]))
		{
			throw new Exception(sprintf('Database table not found: database %s, table %s', $this->name, $table));
		}

		return $this->tables[$table];
	}

	/**
	 * Checks for a table availability.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasTable(string $table) : bool
	{
		return isset($this->tables[$table]);
	}
}