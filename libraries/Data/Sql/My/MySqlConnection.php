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

use \Lightbit\Base\Component;
use \Lightbit\Data\Sql\My\MySqlSchema;

use \Lightbit\Base\IContext;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\ISqlDatabase;
use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlSchema;
use \Lightbit\Data\Sql\ISqlStatement;
use \Lightbit\Data\Sql\ISqlStatementFactory;
use \Lightbit\Data\Sql\ISqlTransaction;

/**
 * MySqlConnection.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MySqlConnection extends Component implements ISqlConnection
{
	/**
	 * The charset.
	 *
	 * @type string
	 */
	private $charset;

	/**
	 * The database.
	 *
	 * @type string
	 */
	private $database;

	/**
	 * The host.
	 *
	 * @type string
	 */
	private $host;

	/**
	 * The mysqli resource.
	 *
	 * @type mysqli
	 */
	private $mysqli;

	/**
	 * The password.
	 *
	 * @type string
	 */
	private $password;

	/**
	 * The persistent connection flag.
	 *
	 * @type bool
	 */
	private $persistent;

	/**
	 * The port.
	 *
	 * @type int
	 */
	private $port;

	/**
	 * The schema.
	 *
	 * @type ISqlSchema
	 */
	private $schema;

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
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id);

		$this->database = 'lightbit';
		$this->host = '127.0.0.1';
		$this->persistent = false;
		$this->port = 3306;

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}

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
		if (!mysqli_close($this->mysqli))
		{
			throw new MySqlConnectionException
			(
				$this,
				sprintf
				(
					'%s (%d)',
					mysqli_error($this->mysqli),
					mysqli_errno($this->mysqli)
				)
			);
		}

		$this->mysqli = null;
		$this->schema = null;
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
	 * Gets the character set.
	 *
	 * @return string
	 *	The character set.
	 */
	public function getCharset() : ?string
	{
		if ($this->mysqli)
		{
			return mysqli_get_charset($this->mysqli)->name;
		}

		return $this->charset;
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
	 * Gets the host.
	 *
	 * @return string
	 *	The host.
	 */
	public function getHost() : string
	{
		return $this->host;
	}

	/**
	 * Gets the internal connection handler.
	 *
	 * @return mysqli
	 *	The internal connection handler.
	 */
	public function getMysqli() : \mysqli
	{
		return $this->mysqli;
	}

	/**
	 * Gets the port.
	 *
	 * @return int
	 *	The port.
	 */
	public function getPort() : int
	{
		return $this->port;
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
			$mkey = '__lightbit.data.sql.connection://my/'
				. $this->user
				. '@'
				. $this->host
				. ':'
				. $this->port
				. '/'
				. $this->database;

			if (! ($this->schema = $memory->get('?' . MySqlSchema::class, $mkey)))
			{
				$memory->set
				(
					$mkey,
					$this->schema = new MySqlSchema
					(
						'def',

						// Databases
						$this->all
						(
							'SELECT 
								S.CATALOG_NAME SCHEMA_NAME,
								S.SCHEMA_NAME DATABASE_NAME,
								S.DEFAULT_CHARACTER_SET_NAME DATABASE_DEFAULT_CHARACTER_SET_NAME
							FROM INFORMATION_SCHEMA.SCHEMATA S
							WHERE CONCAT(S.CATALOG_NAME) = :CatalogName 
								AND CONCAT(S.SCHEMA_NAME) = :SchemaName
							ORDER BY
								S.CATALOG_NAME ASC,
							    S.SCHEMA_NAME ASC',
							[ 
								':CatalogName' => 'def',
								':SchemaName' => $this->database
							]
						),

						// Tables
						$this->all
						(
							'SELECT
								T.TABLE_CATALOG SCHEMA_NAME,
								T.TABLE_SCHEMA DATABASE_NAME,
								T.TABLE_NAME TABLE_NAME
							FROM INFORMATION_SCHEMA.TABLES T
							WHERE CONCAT(T.TABLE_CATALOG) = :CatalogName
								AND CONCAT(T.TABLE_SCHEMA) = :SchemaName
								AND CONCAT(T.TABLE_TYPE) = :TableType
							ORDER BY
								T.TABLE_CATALOG ASC,
								T.TABLE_SCHEMA ASC,
								T.TABLE_NAME ASC',
							[ 
								':CatalogName' => 'def',
								':SchemaName' => $this->database,
								':TableType' => 'BASE TABLE'
							]
						),

						// Columns
						$this->all
						(
							'SELECT
								C.TABLE_CATALOG SCHEMA_NAME,
								C.TABLE_SCHEMA DATABASE_NAME,
								C.TABLE_NAME TABLE_NAME,
								C.COLUMN_NAME COLUMN_NAME,
								C.DATA_TYPE DATA_TYPE,
								C.IS_NULLABLE IS_NULLABLE,
								(
									CASE 
										WHEN C.EXTRA LIKE \'%auto_increment%\'
										THEN \'YES\'
										ELSE \'NO\'
									END
								) IS_SEQUENTIAL
							FROM INFORMATION_SCHEMA.TABLES T
							INNER JOIN INFORMATION_SCHEMA.COLUMNS C
								ON C.TABLE_CATALOG = T.TABLE_CATALOG
									AND C.TABLE_SCHEMA = T.TABLE_SCHEMA
									AND C.TABLE_NAME = T.TABLE_NAME
							WHERE CONCAT(T.TABLE_CATALOG) = :CatalogName
								AND CONCAT(T.TABLE_SCHEMA) = :SchemaName
								AND CONCAT(T.TABLE_TYPE) = :TableType
							ORDER BY C.TABLE_SCHEMA ASC,
								C.TABLE_CATALOG ASC,
								C.TABLE_NAME ASC,
								C.COLUMN_NAME ASC',
							[ 
								':CatalogName' => 'def',
								':SchemaName' => $this->database,
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
								ON TC.TABLE_SCHEMA = T.TABLE_SCHEMA
									AND TC.TABLE_NAME = T.TABLE_NAME
									AND TC.CONSTRAINT_CATALOG = T.TABLE_CATALOG
									AND TC.CONSTRAINT_SCHEMA = T.TABLE_SCHEMA
							INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU
								ON KCU.TABLE_CATALOG = T.TABLE_CATALOG
									AND KCU.TABLE_SCHEMA = TC.TABLE_SCHEMA
									AND KCU.CONSTRAINT_CATALOG = TC.CONSTRAINT_CATALOG
									AND KCU.CONSTRAINT_SCHEMA = TC.CONSTRAINT_SCHEMA
									AND KCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
									AND KCU.TABLE_NAME = TC.TABLE_NAME
							WHERE CONCAT(T.TABLE_CATALOG) = :CatalogName
								AND CONCAT(T.TABLE_SCHEMA) = :SchemaName
								AND CONCAT(T.TABLE_TYPE) = :TableType
							ORDER BY T.TABLE_SCHEMA ASC,
								T.TABLE_CATALOG ASC,
								T.TABLE_NAME ASC,
								KCU.COLUMN_NAME ASC',
							[ 
								':CatalogName' => 'def',
								':SchemaName' => $this->database,
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
		return $this->statementFactory ?? 
		(
			$this->statementFactory = new MySqlStatementFactory($this)
		);
	}

	/**
	 * Gets the user.
	 *
	 * @return string
	 *	The user.
	 */
	public function getUser() : ?string
	{
		return $this->user;
	}

	/**
	 * Checks for a password.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPassword() : bool
	{
		return isset($this->password);
	}

	/**
	 * Checks the connection state.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClosed() : bool
	{
		return !$this->mysqli;
	}

	/**
	 * Checks the persistance.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isPersistent() : bool
	{
		return $this->persistent;
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
		return $this->statement($parameters)->query();
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
	public function quote(string $quote) : string
	{
		return strtr($quote, '"', '`');
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
		$result = mysqli_query($this->mysqli, $command);

		if ($result)
		{
			mysqli_result_free($result);
		}

		return mysqli_num_rows($this->mysqli);
	}

	/**
	 * Sets the character set.
	 *
	 * @param string $charset
	 *	The character set.
	 */
	public function setCharset(?string $charset) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				sprintf
				(
					'Can not set connection charset, unknown: charset %s', 
					$this->charset
				)
			);
		}

		$this->charset = $charset;
	}

	/**
	 * Sets the database.
	 *
	 * @return string
	 *	The database.
	 */
	public function setDatabase(string $database) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				sprintf('Can not change active connection default database: database %s', $database)
			);
		}

		$this->database = $database;
	}

	/**
	 * Sets the host.
	 *
	 * @param string $host
	 *	The host.
	 */
	public function setHost(string $host) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				sprintf('Can not change active connection host: host %s', $host)
			);
		}

		$this->host = $host;
	}

	/**
	 * Sets the password.
	 *
	 * @param string $password
	 *	The password.
	 */
	public function setPassword(?string $password) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				'Can not change active connection password'
			);
		}

		$this->password = $password;
	}

	/**
	 * Sets the persistence.
	 *
	 * @param bool $persistent
	 *	The persistence.
	 */
	public function setPersistent(bool $persistent) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				'Can not change active connection persistence'
			);
		}

		$this->persistent = $persistent;
	}

	/**
	 * Sets the port.
	 *
	 * @param int $port
	 *	The port.
	 */
	public function setPort(int $port) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				'Can not change active connection port'
			);
		}

		$this->port = $port;
	}

	/**
	 * Sets the user.
	 *
	 * @param string $user
	 *	The user.
	 */
	public function setUser(?string $user) : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				'Can not change active connection persistence'
			);
		}

		$this->user = $user;
	}

	/**
	 * Starts the connection.
	 */
	public function start() : void
	{
		if ($this->mysqli)
		{
			throw new MySqlConnectionException
			(
				$this,
				'Can not start active connection'
			);
		}

		try
		{
			if (!($this->mysqli = mysqli_init()))
			{
				throw new MySqlConnectionException
				(
					$this,
					sprintf
					(
						'%s (%d)', 
						mysqli_init_error(),
						mysqli_init_errno()
					)
				);
			}

			if (!@mysqli_real_connect($this->mysqli, ($this->persistent ? 'p:' : '') . $this->host, 
				$this->user, $this->password, $this->database, $this->port))
			{
				throw new MySqlConnectionException
				(
					$this,
					sprintf
					(
						'%s (%d)', 
						mysqli_connect_error(),
						mysqli_connect_errno()
					)
				);
			}

			if ($this->charset && !mysqli_set_charset($this->mysqli, $this->charset))
			{
				throw new MySqlConnectionException
				(
					$this,
					sprintf
					(
						'Can not set connection charset, unknown: charset %s', 
						$this->charset
					)
				);
			}
		}
		catch (\Throwable $e)
		{
			if ($this->mysqli && !$this->persistent)
			{
				mysqli_close($this->mysqli);
			}

			$this->mysqli = null;
			
			throw new MySqlConnectionException($this, 'Can not start connection', $e);
		}
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
		$position = -1;
		$parameters = [];

		if (preg_match_all('/@\\w[\\w0-9]+/', $statement, $matches, PREG_OFFSET_CAPTURE))
		{
			if ($matches)
			{
				foreach ($matches[0] as $i => $match)
				{
					$parameters[$match[0]][] = ++$position;
				}
			}
		}

		return new MySqlStatement($this, $statement);
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
		return new MySqlTransaction($this);
	}
}
