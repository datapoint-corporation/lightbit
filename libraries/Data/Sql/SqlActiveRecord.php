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

use \Lightbit\Exception;
use \Lightbit\Data\Sql\ISqlActiveRecord;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlTable;
use \Lightbit\Data\Sql\SqlModel;

/**
 * SqlActiveRecord.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class SqlActiveRecord extends SqlModel implements ISqlActiveRecord
{
	/**
	 * The schema.
	 *
	 * @type array
	 */
	private static $schema = [];

	/**
	 * The identity.
	 *
	 * @type array
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param string $scenario
	 *	The model scenario.
	 *
	 * @param array $attributes
	 *	The model attributes.
	 *
	 * @param array $configuration
	 *	The model configuration.
	 */
	public function __construct(string $scenario = 'default', array $attributes = null, array $configuration = null)
	{
		parent::__construct($scenario, $attributes, $configuration);

		if (!isset(self::$schema[static::class]))
		{
			self::$schema[static::class] = [];
		}
	}

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
	public function all(array $criteria = null) : array
	{
		return [];
	}

	/**
	 * Creates, prepares and executes a delete statement matching the
	 * model instance identity.
	 *
	 * If the instance is new (see: isNew), this method performs 
	 * no action at all.
	 */
	public function delete() : void
	{

	}

	/**
	 * Gets the identity.
	 *
	 * @return array
	 *	The identity.
	 */
	public function getID() : array
	{
		if (!$this->id)
		{
			throw new Exception(sprintf('Active record identity is not available: instance of %s is new', static::class));
		}

		return $this->id;
	}

	/**
	 * Gets the primary key.
	 *
	 * @return array
	 *	The primary key.
	 */
	public function getPrimaryKey() : array
	{
		if (!isset(self::$schema[static::class]['primary-key']))
		{
			$primaryKey = $this->getTable()->getPrimaryKey();

			if (!$primaryKey)
			{
				throw new Exception(sprintf('Active record primary key is not available: "%s"', static::class));
			}

			self::$schema[static::class]['primary-key'] = $primaryKey;
		}
		
		return self::$schema[static::class]['primary-key'];
	}

	/**
	 * Gets the table.
	 *
	 * @return ISqlTable
	 *	The table.
	 */
	public function getTable() : ISqlTable
	{
		if (!isset(self::$schema[static::class]['table']))
		{
			self::$schema[static::class]['table'] = $this->getSqlConnection()->getDatabase()->getTable($this->getTableName());
		}

		return self::$schema[static::class]['table'];
	}

	/**
	 * Gets the table name.
	 *
	 * @return string
	 *	The table name.
	 */
	public function getTableName() : string
	{
		if (!isset(self::$schema[static::class]['table-name']))
		{
			self::$schema[static::class]['table-name'] = $this->tableName();
		}

		return self::$schema[static::class]['table-name'];
	}

	/**
	 * Checks if it is new.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNew() : bool
	{
		return !isset($this->id);
	}

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
	public function match(array $attributes) : ?ISqlModel
	{
		return null;
	}

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
	public function one(array $criteria = null) : ?ISqlModel
	{
		return null;
	}

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
	public function save() : void
	{

	}

	/**
	 * Creates the table name.
	 *
	 * The default implementation creates the table name based on the active
	 * record class name.
	 *
	 * @return string
	 *	The active record table name.
	 */
	protected function tableName() : string
	{
		$className = static::class;

		if ($i = strrpos($className, '\\'))
		{
			return substr($className, $i + 1);
		}

		return $className;
	}
}