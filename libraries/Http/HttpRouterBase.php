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

use \Lightbit\Base\IAction;
use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Base\Object;
use \Lightbit\Http\IHttpRouter;

/**
 * HttpRouterBase.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class HttpRouterBase extends Component implements IHttpRouter
{
	/**
	 * Resolves the current request to a controller action.
	 *
	 * @return IAction
	 *	The action.
	 */
	abstract public function resolve() : IAction;

	/**
	 * Creates an url.
	 *
	 * @param array $route
	 *	The route to resolve to.
	 *
	 * @param bool $absolute
	 *	The absolute flag which, when set, will cause the url to be
	 *	created as an absolute url.
	 *
	 * @return string
	 *	The result.
	 */
	abstract public function url(array $route, bool $absolute = false) : string;

	/**
	 * The base url.
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * Gets the address.
	 *
	 * @return string
	 *	The address.
	 */
	public final function getAddress() : string
	{
		static $address;

		if (!$address)
		{
			$address = $this->getHost() . ':' . $this->getPort();
		}

    	return $address;
	}

	/**
	 * Gets the base url.
	 *
	 * @return string
	 *	The base url.
	 */
	public final function getBaseUrl() : string
	{
		if (!$this->baseUrl)
        {
        	if (!isset($_SERVER['HTTP_HOST']))
        	{
				throw new Exception(sprintf('Bad environment, missing variable: %s', 'HTTP_HOST'));
        	}

        	if (!isset($_SERVER['SCRIPT_NAME']))
        	{
				throw new Exception(sprintf('Bad environment, missing variable: %s', 'SCRIPT_NAME'));
        	}

        	$this->baseUrl
        		= ($this->isHttps() ? 'https://' : 'http://')
        		. $this->getHost()
        		. ($this->isDefaultPort() ?  '' : (':' . $this->getPort()))
        		. (($x = dirname($_SERVER['SCRIPT_NAME'])) == '/' ? '/' : ($x . '/'));
        }

		return $this->baseUrl;
	}

	/**
	 * Gets the host.
	 *
	 * @return string
	 *	The host.
	 */
	public final function getHost() : string
	{
		static $host;

		if (!$host)
		{
			if (!isset($_SERVER['HTTP_HOST']))
        	{
				throw new Exception(sprintf('Bad environment, missing variable: %s', 'HTTP_HOST'));
        	}

        	$host = $_SERVER['HTTP_HOST'];
		}

		return $host;
	}

	/**
	 * Gets the port.
	 *
	 * @return int
	 *	The port.
	 */
	public final function getPort() : int
	{
		static $port;

		if (!$port)
		{
			if (!isset($_SERVER['SERVER_PORT']))
			{
				throw new Exception(sprintf('Bad environment, missing variable: %s', 'SERVER_PORT'));
			}

			$port = intval($_SERVER['SERVER_PORT']);
		}

		return $port;
	}

	/**
	 * Checks if the port is the default for the procotol.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDefaultPort() : bool
	{
		static $default;

		if (!isset($default))
		{
			$port = $this->getPort();
			$default = $this->isHttps()
				? ($port == 443)
				: ($port == 80);
		}

		return $default;
	}

	/**
	 * Checks if it's the secure hypertext transport protocol.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isHttps() : bool
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	}

	/**
	 * Sets the base url.
	 *
	 * @param string $baseUrl
	 *	The base url.
	 */
	public final function setBaseUrl(string $baseUrl) : void
	{
		$this->baseUrl = $baseUrl;
	}
}