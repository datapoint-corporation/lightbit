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

namespace Lightbit\Data\Sql\Ms;

use \Lightbit\Base\Component;
use \Lightbit\Data\Sql\Ms\MsSqlStatement;
use \Lightbit\Data\Sql\Ms\MsSqlTransaction;

use \Lightbit\Base\IContext;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\ISqlStatementFactory;
use \Lightbit\Data\Sql\ISqlTransaction;

/**
 * ISqlConnection.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MsSqlConnection extends Component implements ISqlConnection
{
	/**
	 * The base option set.
	 *
	 * @type array
	 */
	private const BASE_OPTION_SET = 
	[
		'ApplicationIntent' => 'ReadWrite',
		'CharacterSet' => 'UTF-8',
		'ConnectionPooling' => true,
		'UID' => '',
		'PWD' => ''
	];

	/**
	 * The character set.
	 *
	 * @type string
	 */
	private $charset;

	/**
	 * The database name.
	 *
	 * @type string
	 */
	private $database;

	/**
	 * The instance.
	 *
	 * @type string
	 */
	private $instance;

	/**
	 * The options.
	 *
	 * @type array
	 * @see http://msdn.microsoft.com/en-us/library/ff628167.aspx
	 */
	private $options;

	/**
	 * The password.
	 *
	 * @type string
	 */
	private $password;

	/**
	 * The schema.
	 *
	 * @type ISqlSchema
	 */
	private $schema;

	/**
	 * The sqlsrv resource.
	 *
	 * @type resource
	 */
	private $sqlsrv;

	/**
	 * The statement factory.
	 *
	 * @type ISqlStatementFactory
	 */
	private $statementFactory;

	/**
	 * The user.
	 *
	 * @type string
	 */
	private $user;

	/**
	 * Creates, prepares and executes a query statement, pre-fetching
	 * all results within the first result set.
	 *
	 * @param string $statement
	 *	The statement to prepare.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @param bool $numeric
	 *	The numeric flag which, when set, causes the results to be fetched
	 *	as a numeric array.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(string $statement, array $parameters = null, bool $numeric = false) : array
	{
		return $this->statement($statement)->all($parameters, $numeric);
	}

	/**
	 * Closes the connection.
	 *
	 * If a connection is persistent, this method will be limited to the
	 * relevant clean up procedures but the connection itself may stay
	 * open and ready to use in the future.
	 */
	public function close() : void
	{
		throw new \Lightbit\Exception(sprintf('Class %s method %s is not implemented.', static::class, __FUNCTION__));
	}

	/**
	 * Creates, prepares and executes a statement, returning the number
	 * of affected rows.
	 *
	 * @param string $statement
	 *	The statement to prepare.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function execute(string $statement, array $parameters = null) : int
	{
		return $this->statement($statement)->execute($parameters);
	}

	/**
	 * Gets the database.
	 *
	 * Since this function fetches the schema for the database and all of its
	 * objects (tables, views, columns, etc.), it can become costly and the
	 * use of a fast memory cache component is highly recommended.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 */
	public function getDatabase() : ISqlDatabase
	{
		return $this->getSchema()->getDatabase($this->database);
	}

	/**
	 * Gets the current exception stack.
	 *
	 * @return MsSqlException
	 *	The first exception.
	 */
	public function getExceptionStack() : ?MsSqlException
	{
		$throwable = null;

		if ($errors = sqlsrv_errors())
		{
			foreach (array_reverse($errors) as $i => $schemata)
			{
				$throwable = new MsSqlException(sprintf('SQLSTATE %s: %s (%s)', $schemata['SQLSTATE'], $schemata['message'], $schemata['code']), $throwable);
			}
		}

		return $throwable;
	}

	/**
	 * Gets the schema.
	 *
	 * Since this function fetches the schema for all databases and all of its
	 * objects (tables, views, columns, etc.), it can become costly and the
	 * use of a fast memory cache component is highly recommended.
	 *
	 * @return ISqlSchema
	 *	The schema.
	 */
	public function getSchema() : ISqlSchema
	{
		if (!$this->schema)
		{
			$memory = $this->getMemoryCache();
			$mkey = '__lightbit.data.sql.connection://ms/'
				. $this->user
				. '@'
				. strtr($this->instance, '\\', ':')
				. '/'
				. $this->database;

			if (! ($this->schema = $memory->get('?' . MsSqlSchema::class, $mkey)))
			{
				// Get the current schema name.
				$schema = $this->scalar('SELECT SCHEMA_NAME()');

				$memory->set
				(
					$mkey,
					$this->schema = new MsSqlSchema
					(
						$schema,

						// Databases
						$this->all
						(
							'SELECT 
								S.SCHEMA_NAME SCHEMA_NAME,
								S.CATALOG_NAME DATABASE_NAME,
								S.DEFAULT_CHARACTER_SET_NAME DATABASE_DEFAULT_CHARACTER_SET_NAME
							FROM INFORMATION_SCHEMA.SCHEMATA S
							WHERE S.CATALOG_NAME = :CatalogName 
								AND S.SCHEMA_NAME = :SchemaName
							ORDER BY
								S.CATALOG_NAME ASC',
							[ 
								':CatalogName' => $this->database, 
								':SchemaName' => $schema
							]
						),

						// Tables
						$this->all
						(
							'SELECT
								T.TABLE_SCHEMA SCHEMA_NAME,
								T.TABLE_CATALOG DATABASE_NAME,
								T.TABLE_NAME TABLE_NAME
							FROM INFORMATION_SCHEMA.TABLES T
							WHERE T.TABLE_CATALOG = :CatalogName
								AND T.TABLE_SCHEMA = :SchemaName
								AND T.TABLE_TYPE = :TableType
							ORDER BY
								T.TABLE_CATALOG ASC,
								T.TABLE_SCHEMA ASC,
								T.TABLE_NAME ASC',
							[ 
								':CatalogName' => $this->database, 
								':SchemaName' => $schema,
								':TableType' => 'BASE TABLE'
							]
						),

						// Columns
						$this->all
						(
							'SELECT
								C.TABLE_SCHEMA SCHEMA_NAME,
								C.TABLE_CATALOG DATABASE_NAME,
								C.TABLE_NAME TABLE_NAME,
								C.COLUMN_NAME COLUMN_NAME,
								C.DATA_TYPE DATA_TYPE,
								C.IS_NULLABLE IS_NULLABLE,
								(
									CASE WHEN
									(
										SELECT CA.is_identity 
										FROM sys.columns CA
										WHERE CA.object_id = object_id(T.TABLE_NAME, \'U\')
											AND CA.name = C.TABLE_NAME
									) 
									IS NULL 
										THEN \'NO\'
										ELSE \'YES\'
									END
								) IS_SEQUENTIAL
							FROM INFORMATION_SCHEMA.TABLES T
							INNER JOIN INFORMATION_SCHEMA.COLUMNS C
								ON C.TABLE_CATALOG = T.TABLE_CATALOG
									AND C.TABLE_SCHEMA = T.TABLE_SCHEMA
									AND C.TABLE_NAME = T.TABLE_NAME
							WHERE T.TABLE_CATALOG = :CatalogName
								AND T.TABLE_SCHEMA = :SchemaName
								AND T.TABLE_TYPE = :TableType
							ORDER BY C.TABLE_SCHEMA ASC,
								C.TABLE_CATALOG ASC,
								C.TABLE_NAME ASC,
								C.COLUMN_NAME ASC',
							[ 
								':CatalogName' => $this->database, 
								':SchemaName' => $schema,
								':TableType' => 'BASE TABLE'
							]
						),

						// Constraints
						$this->all
						(
							'SELECT
								T.TABLE_SCHEMA SCHEMA_NAME,
								T.TABLE_CATALOG DATABASE_NAME,
								T.TABLE_NAME TABLE_NAME,
								KCU.COLUMN_NAME COLUMN_NAME,
								TC.CONSTRAINT_NAME CONSTRAINT_NAME,
								TC.CONSTRAINT_TYPE CONSTRAINT_TYPE
							FROM INFORMATION_SCHEMA.TABLES T
							INNER JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC
								ON TC.TABLE_CATALOG = T.TABLE_CATALOG
									AND TC.TABLE_SCHEMA = T.TABLE_SCHEMA
									AND TC.TABLE_NAME = T.TABLE_NAME
									AND TC.CONSTRAINT_CATALOG = T.TABLE_CATALOG
									AND TC.CONSTRAINT_SCHEMA = T.TABLE_SCHEMA
							INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU
								ON KCU.TABLE_CATALOG = TC.TABLE_CATALOG
									AND KCU.TABLE_SCHEMA = TC.TABLE_SCHEMA
									AND KCU.CONSTRAINT_CATALOG = TC.CONSTRAINT_CATALOG
									AND KCU.CONSTRAINT_SCHEMA = TC.CONSTRAINT_SCHEMA
									AND KCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
									AND KCU.TABLE_NAME = TC.TABLE_NAME
							WHERE T.TABLE_CATALOG = :CatalogName
								AND T.TABLE_SCHEMA = :SchemaName
								AND T.TABLE_TYPE = :TableType
							ORDER BY T.TABLE_SCHEMA ASC,
								T.TABLE_CATALOG ASC,
								T.TABLE_NAME ASC,
								KCU.COLUMN_NAME ASC',
							[ 
								':CatalogName' => $this->database, 
								':SchemaName' => $schema,
								':TableType' => 'BASE TABLE'
							]
						)
					)
				);
			}
		}

		return $this->schema;
	}

	/**
	 * Gets the statement factory.
	 *
	 * The statement factory can be used to automatically generate complex
	 * statements based on a dynamic criteria and/or other values - its most
	 * common usage is shown throughout the implementation of active records.
	 *
	 * @return ISqlStatementFactory
	 *	The statement factory.
	 */
	public function getStatementFactory() : ISqlStatementFactory
	{
		return $this->statementFactory;
	}

	/**
	 * Checks the connection state.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClosed() : bool
	{
		return !$this->sqlsrv;
	}

	/**
	 * Prepares and executes a query statement.
	 *
	 * Since this function generates a reader, that reader should be manually
	 * closed and safely disposed before continuing to the next procedure.
	 *
	 * If the reader is not closed, depending on the underlying database
	 * management system, you might be unable to prepare and/or execute
	 * the next statement and memory leaks may have a negative impact on
	 * the application performance.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return ISqlReader
	 *	The reader.
	 */
	public function query(string $statement, array $parameters = null) : ISqlReader
	{
		return $this->statement($statement)->query($parameters);
	}

	/**
	 * Quotes a statement.
	 *
	 * The given statement should have its fields quoted with double quotes and
	 * this method will make the necessary replacements to ensure the quoting
	 * sequence matches the one expected by the underlying database management
	 * system.
	 *
	 * An example: $sql->quote('SELECT * FROM "USER"');
	 *
	 * @param string $statement
	 *	The statement to quote.
	 */
	public function quote(string $statement) : string
	{
		return preg_replace('/"([^"]+)"/', '[$1]', $statement);
	}

	/**
	 * Executes a command.
	 *
	 * The integer returned by this command can have different meanings 
	 * depending on the underlying database management system and the command
	 * in execution but, more often than not, is related to the number
	 * of affected rows.
	 *
	 * @param string $command
	 *	The command to execute.
	 *
	 * @return int
	 *	The result.
	 */
	public function run(string $command) : int
	{
		if ($stmt = sqlsrv_query($this->sqlsrv, $command))
		{
			$result = ((($result = sqlsrv_rows_affected($stmt)) === false || $result < 0) ? 0 : $result);
			sqlsrv_free_stmt($stmt);
			return $result;
		}

		throw $this->getExceptionStack();
	}

	/**
	 * Prepares and executes a query statement, pre-fetching and returning
	 * the first cell within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return string
	 *	The result.
	 */
	public function scalar(string $statement, array $parameters = null) : ?string
	{
		return $this->statement($statement)->scalar($parameters);
	}

	/**
	 * Sets the character set.
	 *
	 * @param string $charset
	 *	The character set name.
	 */
	public function setCharset(string $charset) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection character set, it is active: instance %s', 
					$this->instance
				)
			);
		}

		$this->charset = $charset;
	}

	/**
	 * Sets the database.
	 *
	 * @param string $database
	 *	The database name.
	 */
	public function setDatabase(string $database) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection database, it is active: instance %s', 
					$this->instance
				)
			);
		}

		$this->database = $database;
	}

	/**
	 * Sets the instance.
	 *
	 * @param string $instance
	 *	The instance.
	 */
	public function setInstance(string $instance) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection instance, it is active: instance %s', 
					$instance
				)
			);
		}

		$this->instance = $instance;
	}

	/**
	 * Sets the options.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ff628167.aspx
	 * @param array $options
	 *	The options.
	 */
	public function setOptions(array $options) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection instance, it is active: instance %s', 
					$instance
				)
			);
		}

		$this->options = $options;
	}

	/**
	 * Sets the password.
	 *
	 * @param string $password
	 *	The password.
	 */
	public function setPassword(?string $password) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection user password, it is active: instance %s', 
					$this->instance
				)
			);
		}

		$this->password = $password;
	}

	/**
	 * Sets the user.
	 *
	 * @param string $user
	 *	The user.
	 */
	public function setUser(?string $user) : void
	{
		if ($this->sqlsrv)
		{
			throw new MsSqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection user, it is active: instance %s', 
					$this->instance
				)
			);
		}

		$this->user = $user;
	}

	/**
	 * Prepares and executes a query statement, pre-fetching and returning
	 * the first result within the first result set.
	 *
	 * Since this query generates a reader, know that reader is automatically
	 * closed and safely disposed before the value is returned for use.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @param array $parameters
	 *	The statement parameters.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(string $statement, array $parameters = null, bool $numeric = false) : ?array
	{
		return $this->statement($statement)->single($parameters, $numeric);
	}

	/**
	 * Gets the internal connection handle.
	 *
	 * @return resource
	 *	The internal connection handle.
	 */
	public function getSqlsrv() // : resource
	{
		return $this->sqlsrv;
	}

	/**
	 * Starts the connection.
	 */
	public function start() : void
	{
		try
		{
			$options = $this->options;
			$options['Database'] = $this->database;
			$options['ReturnDatesAsStrings'] = true;

			if ($this->charset)
			{
				$options['CharacterSet'] = $this->charset;
			}
			
			if ($this->user)
			{
				$options['UID'] = $this->user;
			}

			if ($this->password)
			{
				$options['PWD'] = $this->password;
			}

			$options += self::BASE_OPTION_SET;

			if (! ($this->sqlsrv = sqlsrv_connect($this->instance, $options)))
			{
				throw $this->getExceptionStack();
			}
		}
		catch (\Throwable $e)
		{
			if ($this->sqlsrv)
			{
				sqlsrv_close($this->sqlsrv);
			}

			$this->sqlsrv = null;

			throw new MsSqlConnectionException($this, $e->getMessage(), $e);
		}
	}

	/**
	 * Prepares a statement for multiple execution.
	 *
	 * @param string $statement
	 *	The statement to prepare and execute.
	 *
	 * @return ISqlStatement
	 *	The statement.
	 */
	public function statement(string $statement) : ISqlStatement
	{
		return new MsSqlStatement($this, $statement);
	}

	/**
	 * Starts a new transaction.
	 *
	 * Once the applicable transact procedures are complete, you must explicitly
	 * commit (or rollback) the transaction. 
	 *
	 * If the transaction is left open, a rollback is automatically issued
	 * during on connection close, more often than not, at the end of
	 * the current request, even if the persistent flag is set.
	 *
	 * @return ISqlTransaction
	 *	The transaction.
	 */
	public function transaction() : ISqlTransaction
	{
		return new MsSqlTransaction($this);
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the component construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		parent::onConstruct();

		$this->charset = 'UTF-8';
		$this->database = 'Lightbit';
		$this->instance = '127.0.0.1';
		$this->user = 'Lightbit';

		$this->options = [];
		$this->statementFactory = new MsSqlStatementFactory($this);
	}
}
