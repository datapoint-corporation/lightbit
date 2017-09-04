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
use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlObject;
use \Lightbit\Data\Sql\ISqlTable;


/**
 * MySqlSqlTable.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlSqlTable extends Object implements ISqlTable
{
	/**
	 * The catalog.
	 *
	 * @type string
	 */
	private $catalog;

	/**
	 * The columns.
	 *
	 * @type array
	 */
	private $columns;

	/**
	 * The default collation.
	 *
	 * @type string
	 */
	private $defaultCollation;

	/**
	 * The default character set.
	 *
	 * @type string
	 */
	private $defaultCharacterSet;

	/**
	 * The name.
	 *
	 * @type string
	 */
	private $name;

	/**
	 * Constructor.
	 *
	 * @param array $table
	 *	The table schema.
	 *
	 * @param array $columns
	 *	The columns schema.
	 */
	public function __construct(array $table, array $columns)
	{
		$this->catalog = $table['TABLE_CATALOG'];
		$this->defaultCollation = $table['TABLE_COLLATION'];
		$this->name = $table['TABLE_NAME'];

		$this->columns = [];
		foreach ($columns as $i => $column)
		{
			if ($column['TABLE_NAME'] === $this->name && $column['TABLE_CATALOG'] === $this->catalog)
			{
				$this->columns[$column['COLUMN_NAME']] = new MySqlSqlColumn($column);
			}
		}
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
	 * Gets the column.
	 *
	 * @return ISqlColumn
	 *	The column.
	 */
	public function getColumn(string $column) : ISqlColumn
	{
		if (!isset($this->columns[$column]))
		{
			throw new Exception(sprintf('Column is not defined: table "%s", column "%s"', $this->name, $column));
		}

		return $this->columns[$column];
	}

	/**
	 * Checks for a column availability.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasColumn(string $column) : bool
	{
		return isset($this->columns[$column]);
	}
}