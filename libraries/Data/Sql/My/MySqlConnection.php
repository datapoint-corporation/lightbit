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
	 * 
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

	public function getHost() : string
	{
		return $this->host;
	}

	public function getMysqli() : \mysqli
	{
		return $this->mysqli;
	}

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
			$mkey = '__lightbit.data.sql.connection://mysql/'
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
						$this->all('SELECT * FROM information_schema.SCHEMATA'),
						$this->all('SELECT * FROM information_schema.TABLES'),
						$this->all('SELECT * FROM information_schema.COLUMNS'),
						$this->all('SELECT * FROM information_schema.KEY_COLUMN_USAGE')
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

	public function getUser() : ?string
	{
		return $this->user;
	}

	public function hasPassword() : bool
	{
		return isset($this->password);
	}

	public function isClosed() : bool
	{
		return !$this->mysqli;
	}

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
