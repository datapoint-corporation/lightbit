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
use \Lightbit\Http\HttpSessionException;

use \Lightbit\Base\IContext;
use \Lightbit\Base\IChannel;
use \Lightbit\Http\IHttpSession;

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
	 * @var string
	 */
	private $name = '__lb';

	/**
	 * Closes the resource.
	 */
	public final function close() : void
	{
		$this->onClose();

		session_write_close();

		$this->onAfterClose();
	}

	/**
	 * Deletes a attribute.
	 *
	 * @param string $property
	 *	The property.
	 */
	public final function delete(string $property) : void
	{
		unset($_SESSION[$property]);
	}

	/**
	 * Extracts a attribute.
	 *
	 * @param string $type
	 *	The property data type (e.g.: '?string').
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public function extract(?string $type, string $property) // : mixed
	{
		try
		{
			return __map_extract($_SESSION, $type, $property);
		}
		catch (\Throwable $e)
		{
			throw new HttpSessionException
			(
				$this,
				sprintf('Can not extract session attribute: property %s', $property),
				$e
			);
		}
	}

	/**
	 * Gets a attribute.
	 *
	 * @param string $type
	 *	The property data type (e.g.: '?string').
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public final function get(?string $type, string $property) // : mixed
	{
		try
		{
			return __map_get($_SESSION, $type, $property);
		}
		catch (\Throwable $e)
		{
			throw new HttpSessionException
			(
				$this,
				sprintf('Can not get session attribute: property %s', $property),
				$e
			);
		}
	}

	/**
	 * Gets the session client identifier.
	 *
	 * @return string
	 *	The session client identifier.
	 */
	public final function getClientID() : string
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
	 * Checks if a attribute is set.
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function has(string $property) : bool
	{
		return isset($_SESSION[$property]);
	}

	/**
	 * Checks the resource status.
	 *
	 * @return bool
	 *	The resource status.
	 */
	public final function isClosed() : bool
	{
		return (session_status() !== PHP_SESSION_ACTIVE);
	}

	/**
	 * Sets a attribute.
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @param mixed $attribute
	 *	The attribute.
	 */
	public final function set(string $property, $attribute) : void
	{
		$_SESSION[$property] = $attribute;
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
	public final function start() : void
	{
		$this->onStart();

		session_name($this->name);

		if (!session_start())
		{
			throw new HttpSessionException($this, sprintf('Can not start session, unexpected error.'));
		}

		$this->onAfterStart();
	}
	
	/**
	 * Creates an array from this map.
	 *
	 * @return array
	 *	The result.
	 */
	public final function toArray() : array
	{
		return $_SESSION;
	}

	/**
	 * On After Close.
	 *
	 * This method is called during the start procedure, after the session
	 * persistent storage is disposed.
	 */
	protected function onAfterClose() : void
	{
		$this->raise('lightbit.http.session.close.after');
	}

	/**
	 * On After Start.
	 *
	 * This method is called during the start procedure, after the session
	 * persistent storage is ready.
	 */
	protected function onAfterStart() : void
	{
		$this->raise('lightbit.http.session.start.after');
	}

	/**
	 * On Close.
	 *
	 * This method is called during the start procedure, before the session
	 * persistent storage is disposed.
	 */
	protected function onClose() : void
	{
		$this->raise('lightbit.http.session.close');
	}

	/**
	 * On Start.
	 *
	 * This method is called during the start procedure, before the session
	 * persistent storage is ready.
	 */
	protected function onStart() : void
	{
		$this->raise('lightbit.http.session.start');
	}
}