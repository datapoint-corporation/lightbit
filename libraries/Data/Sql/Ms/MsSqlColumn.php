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

use \Lightbit\Data\Sql\Ms\MsSqlObject;

use \Lightbit\Data\Sql\ISqlColumn;
use \Lightbit\Data\Sql\ISqlTable;

/**
 * MsSqlColumn.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlColumn extends MsSqlObject implements ISqlColumn
{
	/**
	 * The nullable flag.
	 *
	 * @var bool
	 */
	private $nullable;

	/**
	 * The sequential flag.
	 *
	 * @var bool
	 */
	private $sequential;

	/**
	 * The table.
	 *
	 * @var ISqlTable
	 */
	private $table;

	/**
	 * The column type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Constructor.
	 *
	 * @param MsSqlTable $table
	 *	The table.
	 *
	 * @param array $schemata
	 *	The column schemata.
	 *
	 * @param array $constraints
	 *	The constraints schemata.
	 */
	public function __construct(MsSqlTable $table, array $schemata, array $constraints)
	{
		parent::__construct($schemata['COLUMN_NAME']);

		$this->table = $table;
		$this->nullable = ($schemata['IS_NULLABLE'] === 'YES');
		$this->type = $schemata['DATA_TYPE'];
		$this->sequential = ($schemata['IS_SEQUENTIAL'] === 'YES');
	}

	/**
	 * Gets the table.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable() : ISqlTable
	{
		return $this->table;
	}

	/**
	 * Gets the type.
	 *
	 * @return string
	 *	The type.
	 */
	public function getType() : string
	{
		return $this->type;
	}

	/**
	 * Checks if the column is nullable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNullable() : bool
	{
		return $this->nullable;
	}

	/**
	 * Checks if the column is sequential.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isSequential() : bool
	{
		return $this->sequential;
	}
}