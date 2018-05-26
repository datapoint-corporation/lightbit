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

final class HttpServer
{
	private static $instance;

	public static final function getInstance() : HttpServer
	{
		return (self::$instance ?? (self::$instance = new HttpServer()));
	}

	private $documentDirectoryPath;

	private $documentDirectoryUrl;

	private $documentPath;

	private $documentRootPath;

	private $documentUrl;

	private $port;

	private $secure;

	private $variables;

	private function __construct()
	{
		$this->variables = new StringMap($_SERVER ?? []);
	}

	public final function getDocumentDirectoryPath() : string
	{
		return ($this->documentDirectoryPath ?? ($this->documentDirectoryPath = ltrim(dirname($this->getDocumentPath()), '/')));
	}

	public final function getDocumentDirectoryUrl() : string
	{
		return ($this->documentDirectoryUrl ?? ($this->documentDirectoryUrl = (rtrim(dirname($this->getDocumentUrl()), '/') . '/')));
	}

	public final function getDocumentPath() : string
	{
		return ($this->documentPath ?? ($this->documentPath = $this->variables->getString('SCRIPT_FILENAME')));
	}

	public final function getDocumentRootPath() : string
	{
		return ($this->documentRootPath ?? ($this->documentRootPath = ltrim($this->variables->getString('DOCUMENT_ROOT'))));
	}

	public final function getDocumentUrl() : string
	{
		return ($this->documentUrl ?? ($this->documentUrl = $this->variables->getString('SCRIPT_NAME')));
	}

	public final function getPort() : int
	{
		return ($this->port ?? ($this->port = $this->variables->getInt('SERVER_PORT')));
	}

	public final function isSecure() : bool
	{
		return ($this->secure ?? ($this->secure = (($https = $this->variables->getString('HTTPS', true)) && strtolower($https) !== 'off')));
	}
}
