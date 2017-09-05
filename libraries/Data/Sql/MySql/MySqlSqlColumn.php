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

/**
 * ISqlColumn.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlSqlColumn extends Object implements ISqlColumn
{
	/**
	 * The auto incrementable flag.
	 *
	 * @type bool
	 */
	private $autoIncrementable;

	/**
	 * The character set.
	 *
	 * @type array
	 */
	private $characterSet;

	/**
	 * The collation.
	 *
	 * @type string
	 */
	private $collation;

	/**
	 * The name.
	 *
	 * @type string
	 */
	private $name;

	/**
	 * The nullable.
	 *
	 * @type bool
	 */
	private $nullable;

	/**
	 * The type name.
	 *
	 * @type string
	 */
	private $typeName;

	/**
	 * Constructor.
	 *
	 * @param array $column
	 *	The column schema.
	 */
	public function __construct(array $column)
	{
		$extras = explode(',', $column['EXTRA']);

		$this->autoIncrementable = in_array('auto_increment', $extras);
		$this->characterSet = $column['CHARACTER_SET_NAME'];
		$this->collation = $column['COLLATION_NAME'];
		$this->name = $column['COLUMN_NAME'];
		$this->nullable = $column['IS_NULLABLE'] === 'YES';
		$this->typeName = $column['DATA_TYPE'];
	}

	/**
	 * Gets the character set.
	 *
	 * @return string
	 *	The character set.
	 */
	public function getCharacterSet() : ?string
	{
		return $this->characterSet;
	}

	/**
	 * Gets the collation.
	 *
	 * @return string
	 *	The collation.
	 */
	public function getCollation() : ?string
	{
		return $this->collation;
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
	 * Gets the type name.
	 *
	 * @return string
	 *	The type name.
	 */
	public function getTypeName() : string
	{
		return $this->typeName;
	}

	/**
	 * Checks if it is auto incrementable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAutoIncrementable() : bool
	{
		return $this->autoIncrementable;
	}

	/**
	 * Checks if is nullable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNullable() : bool
	{
		return $this->nullable;
	}
}