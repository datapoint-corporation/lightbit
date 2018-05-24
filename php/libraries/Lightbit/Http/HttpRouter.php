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

use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\HttpServer;

/**
 * HttpRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpRouter implements IHttpRouter
{
	/**
	 * The base uniform resource location.
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		ConfigurationProvider::getInstance()->getConfiguration(
			'lightbit.http.router'
		)

		->accept($this, [
			'base_url' => 'setBaseUrl',
		]);
	}

	/**
	 * Gets the base uniform resource location.
	 *
	 * @return string
	 *	The base uniform resource location.
	 */
	public final function getBaseUrl() : string
	{
		return ($this->baseUrl ?? ($this->baseUrl = HttpServer::getInstance()->getDocumentDirectoryUrl()));
	}

	/**
	 * Sets the base uniform resource location.
	 *
	 * @param string $baseUrl
	 *	The base uniform resource location.
	 */
	public final function setBaseUrl(string $baseUrl) : void
	{
		if ($baseUrl = trim($baseUrl, '/'))
		{
			$this->baseUrl = '/' . $baseUrl . '/';
		}
		else
		{
			$this->baseUrl = '/';
		}
	}
}
