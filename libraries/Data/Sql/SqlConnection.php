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

use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Data\Sql\MySql\MySqlSqlDriver;
use \Lightbit\Exception;
use \Lightbit\Helpers\ObjectHelper;

/**
 * SqlConnection.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SqlConnection extends Component implements ISqlConnection
{
	/**
	 * The sql connection drivers class name.
	 *
	 * @type array
	 */
	private static $driversClassName = 
	[
		'mysql' => MySqlSqlDriver::class
	];

	/**
	 * Sets a sql connection driver class name.
	 *
	 * @param string $id
	 *	The sql connection driver identifier.
	 *
	 * @param string $className
	 *	The sql connection driver class name.
	 */
	public static function setDriverClassName(string $id, string $className) : void
	{
		self::$driversClassName[$id] = $className;
	}

	/**
	 * Gets a sql connection driver class name.
	 *
	 * @param string $id
	 *	The sql connection driver identifier.
	 *
	 * @return string
	 *	The sql connection driver class name
	 */
	public static function getDriverClassName(string $id) : string
	{
		if (!isset(self::$driversClassName[$id]))
		{
			throw new Exception(sprintf('Sql connection driver is not available: "%s"', $id));
		}

		return self::$driversClassName[$id];
	}

	/**
	 * The sql connection driver
	 *
	 * @type ISqlDriver
	 */
	private $driver;

	/**
	 * The sql connection driver configuration.
	 *
	 * @type array
	 */
	private $driverConfiguration;

	/**
	 * The sql connection string.
	 *
	 * @type string
	 */
	private $dsn;

	/**
	 * The password for authentication.
	 *
	 * @type string
	 */
	private $password;

	/**
	 * The internal connection handle.
	 *
	 * @type PDO
	 */
	private $pdo;

	/**
	 * The statement factory.
	 *
	 * @type ISqlStatementFactory
	 */
	private $statementFactory;

	/**
	 * The user for authentication.
	 *
	 * @type string
	 */
	private $user;

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public final function __construct(Context $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, null);

		$this->driverConfiguration = [];
		$this->dsn = 'mysql:host=127.0.0.1; dbname=Lightbit';
		$this->user = 'lightbit';

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * all results.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(string $statement, array $arguments = null, bool $numeric = false) : array
	{
		return $this->statement($statement)->query($arguments)->all($numeric);
	}

	/**
	 * Closes the channel.
	 */
	public final function close() : void
	{
		$this->pdo = null;
	}

	/**
	 * Checks the channel status.
	 *
	 * @return bool
	 *	The status.
	 */
	public final function isClosed() : bool
	{
		return !$this->pdo;
	}

	/**
	 * Starts the channel.
	 */
	public final function start() : void
	{
		if ($this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, already started: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		$driverClassName = self::getDriverClassName(explode(':', $this->dsn)[0]);

		$driver;
		$pdo;

		try
		{
			$pdo = new \PDO
			(
				$this->dsn,
				$this->user,
				$this->password,
				[
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_EMULATE_PREPARES => false
				]
				+ $this->driverConfiguration
				+ [ \PDO::ATTR_AUTOCOMMIT => true ]
			);

			$driver = new $driverClassName($this);
		}
		catch (\PDOException $e)
		{
			throw new SqlConnectionException($this, sprintf('Can not start sql connection: %s', lcfirst($e->getMessage())), $e);
		}

		$this->driver = $driver;
		$this->pdo = $pdo;
	}

	/**
	 * Starts a transaction.
	 *
	 * @return ISqlTransaction
	 *	The transaction.
	 */
	public function startTransaction() : ISqlTransaction
	{
		$transaction = $this->getDriver()->transaction();
		$transaction->start();

		return $transaction;
	}

	/**
	 * Gets the database.
	 *
	 * @return ISqlDatabase
	 *	The database.
	 *
	 */
	public final function getDatabase() : ISqlDatabase
	{
		return $this->getDriver()->getDatabase();
	}

	/**
	 * Gets the last insert row identifier.
	 *
	 * @return int
	 *	The last insert row identifier.
	 */
	public final function getLastInsertID() : int
	{
		if (!$this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, already closed: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		return $this->driver->getLastInsertID();
	}

	/**
	 * Gets the internal connection handler.
	 *
	 * @return PDO
	 *	The internal connection handler.
	 */
	public final function getPdo() : \PDO
	{
		if (!$this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, closed: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		return $this->pdo;
	}

	/**
	 * Gets the sql connection driver.
	 *
	 * @return ISqlDriver
	 *	The sql connection driver.
	 */
	public final function getDriver() : ISqlDriver
	{
		if (!$this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, closed: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		return $this->driver;
	}

	/**
	 * Gets the sql statement factory.
	 *
	 * @return ISqlStatementFactory
	 *	The sql statement factory.
	 */
	public function getStatementFactory() : ISqlStatementFactory
	{
		if (!$this->statementFactory)
		{
			$this->statementFactory = $this->getDriver()->statementFactory();
		}

		return $this->statementFactory;
	}

	/**
	 * Gets the user for authentication.
	 *
	 * @return string
	 *	The user for authentication.
	 */
	public final function getUser() : ?string
	{
		return $this->user;
	}

	/**
	 * Creates, prepares and executes a statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public final function execute(string $statement, array $arguments = null) : int
	{
		return $this->statement($statement)->execute($arguments);
	}

	/**
	 * Checks for a password for authentication.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasPassword() : bool
	{
		return isset($this->password);
	}

	/**
	 * Checks for a user for authentication.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasUser() : bool
	{
		return isset($this->user);
	}

	/**
	 * Creates, prepares and executes a query statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public final function query(string $statement, array $arguments = null) : ISqlReader
	{
		return $this->statement($statement)->query($arguments);
	}

	/**
	 * Runs a command.
	 *
	 * Please beaware that this function does not perform any kind of escape
	 * procedures on the given command.
	 *
	 * For security reasons, you should never use this function with user
	 * input, even after validation.
	 *
	 * @param string $command
	 *	The command.
	 *
	 * @return int
	 *	The number of affected rows.
	 */
	public function run(string $command) : int
	{
		if (!$this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, closed: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		try
		{
			return $this->pdo->exec($command);
		}
		catch (\PDOException $e)
		{
			throw new SqlException($this, sprintf('Can not run SQL command: %s', lcfirst($e->getMessage())), $e);
		}
	}

	/**
	 * Creates, prepares and executes a scalar query statement.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function scalar(string $statement, array $arguments = null) // : mixed
	{
		return $this->statement($statement)->scalar($arguments);
	}

	/**
	 * Sets the driver configuration.
	 *
	 * @param array $driverConfiguration
	 *	The driver configuration.
	 *
	 * @param bool $merge
	 *	The driver configuration merge flag.
	 */
	public final function setDriverConfiguration(array $driverConfiguration, bool $merge = true) : void
	{
		$this->driverConfiguration = $merge
			? array_replace_recursive($this->driverConfiguration, $driverConfiguration)
			: $driverConfiguration;
	}

	/**
	 * Sets the dsn connection string.
	 *
	 * @param string $dsn
	 *	The dsn connection string.
	 */
	public final function setDsn(string $dsn) : void
	{
		$this->dsn = $dsn;
	}

	/**
	 * Sets the password for authentication.
	 *
	 * @param string $password
	 *	The password for authentication.
	 */
	public final function setPassword(?string $password) : void
	{
		$this->password = $password;
	}

	/**
	 * Sets the user for authentication.
	 *
	 * @param string $user
	 *	The user for authentication.
	 */
	public final function setUser(?string $user) : void
	{
		$this->user = $user;
	}

	/**
	 * Creates, prepares and executes a query statement that's meant to fetch
	 * a single result.
	 *
	 * @param string $statement
	 *	The statement to create, prepare and execute, as a string.
	 *
	 * @param array $arguments
	 *	The statement arguments.
	 *
	 * @param bool $numeric
	 *	The fetch as a numeric array flag.
	 *
	 * @return array
	 *	The result.
	 */
	public final function single(string $statement, array $arguments = null, bool $numeric = false) : ?array
	{
		return $this->statement($statement)->single($arguments, $numeric);
	}

	/**
	 * Creates and prepares a statement.
	 *
	 * @param string $statement
	 *	The statement to create and prepare, as a string.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public final function statement(string $statement) : ISqlStatement
	{
		if (!$this->pdo)
		{
			throw new SqlConnectionException
			(
				$this, 
				sprintf
				(
					'Bad SQL connection status, closed: "%s", at context "%s"', 
					$this->getID(), 
					$this->getContext()->getPrefix()
				)
			);
		}

		return $this->driver->statement($statement);
	}

	/**
	 * Executes a transaction.
	 *
	 * @param \Closure $closure
	 *	The transaction closure.
	 *
	 * @return mixed
	 *	The transaction result.
	 */
	public function transaction(\Closure $closure) // : mixed;
	{
		$transaction = null;

		try
		{
			$transaction = $this->getDriver()->transaction();
			$transaction->start();
			$result = $closure($this);
			$transaction->commit();

			return $result;
		}
		catch (\Throwable $e)
		{
			if ($transaction)
			{
				if (!$transaction->isClosed())
				{
					try
					{
						$transaction->rollback();
					}
					catch (\Throwable $e2)
					{
						throw new SqlTransactionException($transaction, sprintf('Can not rollback transaction with error: %s', lcfirst($e->getMessage())), $e2);
					}
				}

				throw new SqlTransactionException($transaction, sprintf('Can not complete transaction, rollback issued: %s', lcfirst($e->getMessage())), $e);
			}

			throw new SqlConnectionException($this, sprintf('Can not complete transaction, start failure: %s', lcfirst($e->getMessage())), $e);
		}
	}
}