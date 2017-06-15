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

namespace Lightbit\Http;

use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IChannel;
use \Lightbit\Http\HttpSessionException;
use \Lightbit\Http\IHttpSession;
use \Lightbit\Http\KeyNotFoundHttpSessionException;

/**
 * HttpSession.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpSession extends Component implements IHttpSession, IChannel
{
	/**
	 * The session name.
	 *
	 * @type string
	 */
	private $name = 'lightbit-session';

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
		parent::__construct($context, $id, $configuration);
	}

	/**
	 * Closes the resource.
	 */
	public function close() : void
	{
		session_write_close();
	}

	/**
	 * Checks for a value availability.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return bool
	 *	The check result.
	 */
	public function contains($key) : bool
	{
		return isset($_SESSION[$key]);
	}

	/**
	 * Attempts to read a value and, if not set, the default value
	 * is returned instead.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @param mixed $default
	 *	The default value.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function fetch($key, $default = null) // : mixed
	{
		return (isset($_SESSION[$key]) || array_key_exists($key, $_SESSION))
			? $_SESSION[$key]
			: $default;
	}

	/**
	 * Gets the session global unique identifier.
	 *
	 * @return string
	 *	The session global unique identifier.
	 */
	public final function getGuid() : string
	{
		return session_id();
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public final function getName() : string
	{
		return $this->name;
	}

	/**
	 * Checks the resource status.
	 *
	 * @return bool
	 *	The resource status.
	 */
	public function isClosed() : bool
	{
		return session_status() !== PHP_SESSION_ACTIVE;
	}

	/**
	 * Reads a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function read($key) // : mixed
	{
		if ($this->isClosed())
		{
			throw new HttpSessionException($this, sprintf('Http session status error: resource is closed'));
		}

		if (!isset($_SESSION[$key]) && !array_key_exists($key, $_SESSION))
		{
			throw new KeyNotFoundHttpSessionException($this, sprintf('Http session key not found: "%s"', $key));
		}

		return $_SESSION[$key];
	}
	
	/**
	 * Removes a value.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @return mixed
	 *	The value.
	 */
	public function remove($key) // : mixed
	{
		$result = null;

		if (isset($_SESSION[$key]))
		{
			$result = $_SESSION[$key];
			unset($_SESSION[$key]);
		}

		return $result;
	}

	/**
	 * Sets the name.
	 *
	 * @param string $name
	 *	The name.
	 */
	public final function setName(string $name) : void
	{
		$this->name = $name;
	}

	/**
	 * Starts the resource.
	 */
	public function start() : void
	{
		if (!$this->isClosed())
		{
			throw new HttpSessionException($this, sprintf('Http session start failure: already active'));
		}

		session_name($this->name);

		if (!session_start())
		{
			throw new HttpSessionException($this, sprintf('Http session start failure: unknown error'));
		}
	}
	
	/**
	 * Creates an array from this map.
	 *
	 * @return array
	 *	The result.
	 */
	public function toArray() : array
	{
		return $_SESSION;
	}

	/**
	 * Writes a value.
	 *
	 * @param mixed $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 */
	public function write($key, $value) : void
	{
		$_SESSION[$key] = $value;
	}
}