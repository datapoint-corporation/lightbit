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

use \Lightbit\Http\HttpServerRequestQueryString;

use \Lightbit\Http\IHttpRequest;

/**
 * HttpServerRequest.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpServerRequest implements IHttpRequest
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServerRequest
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServerRequest
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServerRequest
	{
		return (self::$instance ?? (self::$instance = new HttpServerRequest()));
	}

	/**
	 * The header list.
	 *
	 * @var array
	 */
	private $headerList;

	/**
	 * The header map.
	 *
	 * @var array
	 */
	private $headerMap;

	/**
	 * The path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The uniform resource location.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the header list.
	 *
	 * @return array
	 *	The header list.
	 */
	public final function getHeaderList() : array
	{
		if (!isset($this->headerList))
		{
			foreach ($this->getHeaderMap() as $i => $headerList)
			{
				foreach ($headerList as $i => $header)
				{
					$this->headerList[] = $header;
				}
			}
		}

		return $this->headerList;
	}

	/**
	 * Gets the header map.
	 *
	 * @return array
	 *	The header map.
	 */
	public final function getHeaderMap() : array
	{
		if (!isset($this->headerMap))
		{
			foreach ($_SERVER as $property => $value)
			{
				if (strpos($property, 'HTTP_') === 0)
				{
					$name = strtr(ucwords(strtolower(strtr(substr($property, 5), [ '_' => ' ' ]))), [ ' ' => '-' ]);

					if (is_string($value))
					{
						$this->headerMap[$name][] = new HttpHeader($name, $value);
					}

					else if (is_array($value))
					{
						foreach ($value as $i => $valueListItem)
						{
							$this->headerMap[$name][] = new HttpHeader($name, $valueListItem);
						}
					}
				}
			}
		}

		return $this->headerMap;
	}

	/**
	 * Gets a header.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return string
	 *	The header content.
	 */
	public final function getHeader(string $name) : ?IHttpHeader
	{
		return ($this->getHeaderMap()[$name][0] ?? null);
	}

	/**
	 * Gets a header content.
	 *
	 * @return string
	 *	The header content.
	 */
	public final function getHeaderContent(string $name) : ?string
	{
		$headerMap = $this->getHeaderMap();

		if (isset($headerMap[$name][0]))
		{
			return ($headerMap[$name][0])->getContent();
		}

		return null;
	}

	/**
	 * Gets the method.
	 *
	 * @return string
	 *	The method.
	 */
	public final function getMethod() : string
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		if (!isset($this->path))
		{
			$this->path = $this->getUrl();

			if ($i = strpos($this->path, '?'))
			{
				$this->path = substr($this->path, 0, $i);
			}

			if ($this->path = trim($this->path, '/'))
			{
				$this->path = '/' . $this->path . '/';
			}
			else
			{
				$this->path = '/';
			}
		}

		return $this->path;
	}

	/**
	 * Gets the uniform resource location.
	 *
	 * @return string
	 *	The uniform resource location.
	 */
	public final function getUrl() : string
	{
		return ($this->url ?? ($this->url = $_SERVER['REQUEST_URI']));
	}

	/**
	 * Gets the query string.
	 *
	 * @return IHttpQueryString
	 *	The query string.
	 */
	public final function getQueryString() : IHttpQueryString
	{
		return HttpServerRequestQueryString::getInstance();
	}
}
