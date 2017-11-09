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
use \Lightbit\Data\Sql\ISqlTable;
use \Lightbit\Data\Sql\SqlCriteria;
use \Lightbit\Data\Sql\SqlModel;

/**
 * ISqlActiveRecord.
 *
 * It's an active record, directly attached to a table in the database, which
 * columns should be declared as non-static public properties of each class 
 * that implements it.
 *
 * The database records are deleted, inserted, selected and updated through
 * the statement factory provided by a sql connection component..
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
	 * The attributes.
	 *
	 * @type array
	 */
	private $attributes;

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
		if (isset($criteria))
		{
			$criteria = new SqlSelectCriteria($criteria);
		}

		$results = $this->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $criteria)
					->query()
						->all();

		foreach ($results as $i => $result)
		{
			$results[$i] = $this->construct($result);
		}

		return $results;
	}

	/**
	 * Performs a commit.
	 */
	public function commit() : void
	{
		$this->attributes = $this->getAttributes();

		// Update the current active record identity, requiring all primary
		// key attributes to be set.
		$this->id = [];

		foreach ($this->getPrimaryKey() as $i => $attribute)
		{
			$identity = $this->getAttribute($attribute);

			if (!$identity)
			{
				$this->id = null;
				break;
			}

			$this->id[$attribute] = $identity;
		}
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * the number of matching results, optionally based on a given
	 * select criteria.
	 *
	 * @param array $criteria
	 *	The select criteria configuration.
	 *
	 * @return int
	 *	The result.
	 */
	public function count(array $criteria = null) : int
	{
		if (isset($criteria))
		{
			$criteria = new SqlSelectCriteria($criteria);
		}

		return $this->getSqlConnection()
			->getStatementFactory()
				->count($this->getTableName(), $criteria)
					->scalar();
	}

	/**
	 * Constructs a new instance for update.
	 *
	 * @param array $attributes
	 *	The instance attributes.
	 *
	 * @return ISqlActiveRecord
	 *	The instance.
	 */
	private function construct(array $attributes) : ISqlActiveRecord
	{
		$instance = static::model('update');
		$instance->setAttributes($attributes);
		$instance->commit();

		return $instance;
	}

	/**
	 * Creates, prepares and executes a delete statement matching the
	 * model instance identity.
	 *
	 * If the instance is new (see: isNew), this method performs
	 * no action at all.
	 */
	public final function delete() : void
	{
		$this->onDelete();

		if ($this->id)
		{
			$criteria = new SqlCriteria();
			$criteria->addComparisons($this->id);

			$statement = $this->getSqlConnection()->getStatementFactory()->delete
			(
				$this->getTableName(),
				$criteria
			);

			$statement->execute();
			$this->commit();
		}

		$this->onAfterDelete();
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * the existance of any matching results, optionally based on a given
	 * select criteria.
	 *
	 * @param array $criteria
	 *	The select criteria configuration.
	 *
	 * @return bool
	 *	The result.
	 */
	public function exists(array $criteria = null) : bool
	{
		return $this->count($criteria) > 0;
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * all results matching the given attributes as an instance of this model.
	 *
	 * @param array $attributes
	 *	The attributes to match.
	 *
	 * @return array
	 *	The results.
	 */
	public function filter(array $attributes) : array
	{
		$criteria = new SqlCriteria();
		$criteria->addComparisons($attributes);

		$results = $this->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $criteria)
					->query()
						->all();

		foreach ($results as $i => $result)
		{
			$results[$i] = $this->construct($result);
		}

		return $results;
	}

	/**
	 * Gets the attributes with update.
	 *
	 * If a commit was performed prior to invoking this function, the
	 * attributes that have been modified since then will be returned as an
	 * associative array. However, if a commit was not performed, all
	 * attributes will be returned instead.
	 *
	 * @param bool $inverse
	 *	When set, the original attributes are returned instead.
	 *
	 * @return array
	 *	The difference.
	 */
	public final function getAttributesWithUpdate(bool $inverse = false) : array
	{
		if (isset($this->attributes))
		{
			$result = [];

			if ($inverse)
			{
				foreach ($this->getAttributes() as $attribute => $value)
				{
					if ($value !== $this->attributes[$attribute])
					{
						$result[$attribute] = $this->attributes[$attribute];
					}
				}
			}
			else
			{
				foreach ($this->getAttributes() as $attribute => $value)
				{
					if ($value !== $this->attributes[$attribute])
					{
						$result[$attribute] = $value;
					}
				}
			}

			return $result;
		}

		return $this->getAttributes();
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
			throw new Exception(sprintf('Active record identity is not available: instance is new, class %s', static::class));
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
				throw new Exception(sprintf('Active record primary key is not available: %s', static::class));
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
			self::$schema[static::class]['table-name'] = $this->table();
		}

		return self::$schema[static::class]['table-name'];
	}

	/**
	 * Checks if an attribute has been modified.
	 *
	 * @param string $attribute
	 *	The attribute name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributeUpdate(string $attribute) : bool
	{
		if (isset($this->attributes))
		{
			return ($this->getAttribute($attribute) !== $this->attributes[$attribute]);
		}

		return true;
	}

	/**
	 * Checks if an attribute has been modified.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasAttributesUpdate() : bool
	{
		if (isset($this->attributes))
		{
			foreach ($this->attributes as $property => $attribute)
			{
				if ($attribute !== $this->getAttribute($property))
				{
					return true;
				}
			}
			
			return false;
		}

		return true;
	}

	/**
	 * Checks if it is new.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNew() : bool
	{
		return !$this->id;
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
		$criteria = new SqlCriteria();
		$criteria->addComparisons($attributes);

		$result = $this->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $criteria)
					->single();

		if ($result)
		{
			$result = $this->construct($result);
		}

		return $result;
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
		if (isset($criteria))
		{
			$criteria = new SqlSelectCriteria($criteria);
		}

		$result = $this->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $criteria)
					->single();

		if ($result)
		{
			$result = $this->construct($result);
		}

		return $result;
	}

	/**
	 * Performs a rollback.
	 */
	public function rollback() : void
	{
		if (!isset($this->attributes))
		{
			throw Exception(sprintf('Can not rollback, no commit: class %s', static::class));
		}

		$this->setAttributes($this->attributes);
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
	public final function save() : void
	{
		$this->onSave();

		// Get the table columns and limit the attributes to those matching
		// existing columns for this active record.
		$table = $this->getTable();
		$columns = $table->getColumns();
		$attributes = array_intersect_key($this->getAttributesWithUpdate(), array_reverse($columns));

		if ($attributes)
		{
			if ($this->id)
			{
				$criteria = new SqlCriteria();
				$criteria->addComparisons($this->id);

				$this->getSqlConnection()
					->getStatementFactory()
						->update($table->getName(), $attributes, $criteria)
							->execute();
			}
			else
			{
				$sql = $this->getSqlConnection();
				$sql->getStatementFactory()
					->insert($table->getName(), $attributes)
						->execute();

				$primaryKey = $this->getPrimaryKey();

				if (!isset($primaryKey[1]) && ($columns[$primaryKey[0]])->isSequential())
				{
					$this->setAttribute($primaryKey[0], $sql->getLastInsertID());
				}
			}

			$this->commit();
		}

		else if (!$this->id)
		{
			throw new Exception(sprintf('Active record can not be saved: insufficient number of properties, class %s', static::class));
		}

		$this->onAfterSave();
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * a single result as an instance of this model.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return ISqlModel
	 *	The result.
	 */
	public function single(string $statement, array $arguments = null) : ?ISqlModel
	{
		$instance = parent::single($statement, $arguments);

		if ($instance)
		{
			$instance->commit();
		}

		return $instance;
	}

	/**
	 * Creates, prepares and executes a query statement to fetch all results
	 * as instances of this model.
	 *
	 * @param string $statement
	 *	The statement to prepare, execute and read from.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return array
	 *	The results.
	 */
	public function query(string $statement, array $arguments = null) : array
	{
		$instances = parent::query($statement, $arguments);

		foreach ($instances as $i => $instance)
		{
			$instance->commit();
		}

		return $instances;
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
	protected function table() : string
	{
		$className = static::class;

		if ($i = strrpos($className, '\\'))
		{
			return substr($className, $i + 1);
		}

		return $className;
	}

	/**
	 * After Delete.
	 *
	 * This method is invoked at the end of the active record delete procedure
	 * (see: delete), before any validation takes place and the changes are
	 * written to the database.
	 *
	 * The default implementation raises the global and generic
	 * "Lightbit.Data.Sql.SqlActiveRecord.AfterDelete" event, in addition to a
	 * global and dynamic event based on the class name (e.g.:
	 * "MyNamespace.MyClassName.AfterDelete").
	 */
	protected function onAfterDelete() : void
	{
		$dynamic = strtr(static::class, [ '\\' => '.' ]) . '.AfterDelete';

		$this->raise('Lightbit.Data.Sql.ActiveRecord.AfterDelete', $this);
		$this->raise($dynamic, $this);
	}

	/**
	 * After Save.
	 *
	 * This method is invoked at the end of the active record save procedure
	 * (see: save), before any validation takes place and the changes are
	 * written to the database.
	 *
	 * The default implementation raises the global and generic
	 * "Lightbit.Data.Sql.SqlActiveRecord.AfterSave" event, in addition to a
	 * global and dynamic event based on the class name (e.g.:
	 * "MyNamespace.MyClassName.AfterSave").
	 */
	protected function onAfterSave() : void
	{
		$dynamic = strtr(static::class, [ '\\' => '.' ]) . '.AfterSave';

		$this->raise('Lightbit.Data.Sql.ActiveRecord.AfterSave', $this);
		$this->raise($dynamic, $this);
	}

	/**
	 * Delete.
	 *
	 * This method is invoked at the beginning of the active record save
	 * procedure (see: save), before any validation takes place and the changes
	 * are written to the database.
	 *
	 * The default implementation raises the global and generic
	 * "Lightbit.Data.Sql.SqlActiveRecord.Delete" event, in addition to a
	 * global and dynamic event based on the class name (e.g.:
	 * "MyNamespace.MyClassName.Delete").
	 */
	protected function onDelete() : void
	{
		$dynamic = strtr(static::class, [ '\\' => '.' ]) . '.Delete';

		$this->raise('Lightbit.Data.Sql.ActiveRecord.Delete', $this);
		$this->raise($dynamic, $this);
	}

	/**
	 * Save.
	 *
	 * This method is invoked at the beginning of the active record save
	 * procedure (see: save), before any validation takes place and the changes
	 * are written to the database.
	 *
	 * The default implementation raises the global and generic
	 * "Lightbit.Data.Sql.SqlActiveRecord.Save" event, in addition to a
	 * global and dynamic event based on the class name (e.g.:
	 * "MyNamespace.MyClassName.Save").
	 */
	protected function onSave() : void
	{
		$dynamic = strtr(static::class, [ '\\' => '.' ]) . '.Save';

		$this->raise('Lightbit.Data.Sql.ActiveRecord.Save', $this);
		$this->raise($dynamic, $this);
	}
}
