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

namespace Lightbit\Data\Sql;

use \Lightbit\Exception;
use \Lightbit\Data\Model;
use \Lightbit\Data\Sql\ISqlActiveRecord;
use \Lightbit\Data\Sql\ISqlTable;
use \Lightbit\Data\Sql\SqlCriteria;

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
abstract class SqlActiveRecord extends Model implements ISqlActiveRecord
{
	/**
	 * The schema.
	 *
	 * @var array
	 */
	private static $schema = [];

	/**
	 * The attributes.
	 *
	 * @var array
	 */
	private $attributes;
	
	/**
	 * The criteria.
	 * 
	 * @var ISqlSelectCriteria
	 */
	private $criteria;

	/**
	 * The identity.
	 *
	 * @var array
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
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve all records matching the current criteria.
	 * 
	 * @return array
	 *	The results.
	 */
	public function all() : array
	{
		$result = $this->getContext()->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $this->criteria)
					->all(null, false);
		
		foreach ($result as $i => $single)
		{
			$result[$i] = $this->construct($single);
		}
		
		return $result;
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
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve the number of records matching the current criteria.
	 * 
	 * @return int
	 *	The result.
	 */
	public function count() : int
	{
		return $this->getContext()->getSqlConnection()
			->getStatementFactory()
				->count($this->getTableName(), $this->criteria)
					->scalar();
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

			$statement = $this->getContext()->getSqlConnection()->getStatementFactory()->delete
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
	 * Creates, prepares and executes a query statement that is meant to
	 * check the existence of records matching the current criteria.
	 * 
	 * @return bool
	 *	The result.
	 */
	public function exists() : bool
	{
		return $this->count() > 0;
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
	 * Gets the criteria.
	 * 
	 * @return ISqlCriteria
	 *	The criteria.
	 */
	public function getCriteria(): ISqlCriteria 
	{
		if (!$this->criteria)
		{
			$this->criteria = new SqlSelectCriteria();
		}
		
		return $this->criteria;
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
	public final function getPrimaryKey() : array
	{
		if (!isset(self::$schema[static::class]['primary-key']))
		{
			self::$schema[static::class]['primary-key'] = $this->primaryKey();
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
			self::$schema[static::class]['table'] = $this->getContext()->getSqlConnection()->getDatabase()->getTable($this->getTableName());
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
	 * Creates the primary key.
	 *
	 * The default implementation creates the primary key to match the table
	 * primary key constraint.
	 *
	 * @return array
	 *	The primary key.
	 */
	protected function primaryKey() : array
	{
		$primaryKey = $this->getTable()->getPrimaryKey();

		if (!$primaryKey)
		{
			throw new Exception(sprintf('Active record primary key is not available: %s', static::class));
		}

		return $primaryKey;
	}

	/**
	 * Creates, prepares and executes a query statement that is meant to
	 * retrieve a single record matching the current criteria.
	 * 
	 * @return ISqlActiveRecord
	 *	The result.
	 */
	public function single() : ?ISqlActiveRecord
	{
		$result = $this->getContext()->getSqlConnection()
			->getStatementFactory()
				->select($this->getTableName(), $this->criteria)
					->single(null, false);
		
		if ($result)
		{
			return $this->construct($result);
		}
		
		return null;
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

				$this->getContext()->getSqlConnection()
					->getStatementFactory()
						->update($table->getName(), $attributes, $criteria)
							->execute();
			}
			else
			{
				$sql = $this->getContext()->getSqlConnection();
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
	 * Sets additional comparisons.
	 * 
	 * @param array $comparisons
	 *	The comparisons.
	 * 
	 * @return ISqlActiveRecord
	 *	This instance.
	 */
	public function with(array $comparisons) : ISqlActiveRecord
	{
		$this->getCriteria()->addComparisons($comparisons);
		return $this;
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
