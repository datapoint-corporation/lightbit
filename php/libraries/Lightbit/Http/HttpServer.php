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

namespace Lightbit\Http;

use \Lightbit\Data\Collections\StringMap;

/**
 * HttpServer.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpServer
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServer
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServer
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServer
	{
		return (self::$instance ?? (self::$instance = new HttpServer()));
	}

	/**
	 * The document directory path.
	 *
	 * @var string
	 */
	private $documentDirectoryPath;

	/**
	 * The document directory uniform resource location.
	 *
	 * @var string
	 */
	private $documentDirectoryUrl;

	/**
	 * The document path.
	 *
	 * @var string
	 */
	private $documentPath;

	/**
	 * The document root path.
	 *
	 * @var string
	 */
	private $documentRootPath;

	/**
	 * The document uniform response location.
	 *
	 * @var string
	 */
	private $documentUrl;

	/**
	 * The map.
	 *
	 * @var StringMap
	 */
	private $map;

	/**
	 * The port.
	 *
	 * @var int
	 */
	private $port;

	/**
	 * The secure flag.
	 *
	 * @var bool
	 */
	private $secure;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->map = new StringMap($_SERVER ?? []);
	}

	/**
	 * Gets the document directory path.
	 *
	 * @return string
	 *	The document directory path.
	 */
	public final function getDocumentDirectoryPath() : string
	{
		return ($this->documentDirectoryPath ?? ($this->documentDirectoryPath = ltrim(dirname($this->getDocumentPath()), '/')));
	}

	/**
	 * Gets the document directory uniform resource location.
	 *
	 * @return string
	 *	The document directory uniform resource location.
	 */
	public final function getDocumentDirectoryUrl() : string
	{
		return ($this->documentDirectoryUrl ?? ($this->documentDirectoryUrl = (rtrim(dirname($this->getDocumentUrl()), '/') . '/')));
	}

	/**
	 * Gets the document path.
	 *
	 * @return string
	 *	The document path.
	 */
	public final function getDocumentPath() : string
	{
		return ($this->documentPath ?? ($this->documentPath = $this->map->getString('SCRIPT_FILENAME')));
	}

	/**
	 * Gets the document root path.
	 *
	 * @return string
	 *	The document root path.
	 */
	public final function getDocumentRootPath() : string
	{
		return ($this->documentRootPath ?? ($this->documentRootPath = ltrim($this->map->getString('DOCUMENT_ROOT'))));
	}

	/**
	 * Gets the document uniform resource location.
	 *
	 * @return string
	 *	The document uniform resource location.
	 */
	public final function getDocumentUrl() : string
	{
		return ($this->documentUrl ?? ($this->documentUrl = $this->map->getString('SCRIPT_NAME')));
	}

	/**
	 * Gets the port.
	 *
	 * @return int
	 *	The port.
	 */
	public final function getPort() : int
	{
		return ($this->port ?? ($this->port = $this->map->getInt('SERVER_PORT')));
	}

	/**
	 * Checks the secure flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isSecure() : bool
	{
		return ($this->secure ?? ($this->secure = (($https = $this->map->getString('HTTPS', true)) && strtolower($https) !== 'off')));
	}
}
