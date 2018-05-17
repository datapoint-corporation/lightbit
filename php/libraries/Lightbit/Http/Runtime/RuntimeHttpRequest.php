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

namespace Lightbit\Http\Runtime;

use \Lightbit\Http\HttpMethod;
use \Lightbit\Http\HttpUserAgent;
use \Lightbit\Http\HttpUserAgentProvider;
use \Lightbit\Http\IHttpContext;
use \Lightbit\Http\IHttpHeaderCollection;
use \Lightbit\Http\IHttpMethod;
use \Lightbit\Http\IHttpForm;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpUserAgent;
use \Lightbit\Runtime\RuntimeEnvironment;

/**
 * RuntimeHttpRequest.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class RuntimeHttpRequest implements IHttpRequest
{
	/**
	 * The content.
	 *
	 * @var string
	 */
	private $content;

	/**
	 * The content length.
	 *
	 * @return string
	 *	The content length.
	 */
	private $contentLength;

	/**
	 * The content type.
	 *
	 * @return string
	 *	The content type.
	 */
	private $contentType;

	/**
	 * The cookies.
	 *
	 * @return array
	 *	The cookies.
	 */
	private $cookies;

	/**
	 * The url.
	 *
	 * @return string
	 *	The url.
	 */
	private $url;

	/**
	 * The user agent.
	 *
	 * @var IHttpUserAgent
	 */
	private $userAgent;

	/**
	 * Construct.
	 */
	public final function __construct()
	{

	}

	/**
	 * Gets the content.
	 *
	 * @return string
	 *	The content.
	 */
	public final function getContent() : ?string
	{
		if (!isset($this->content) && !$this->getMethod()->isSafe())
		{
			$this->content = file_get_contents('php://input');
		}

		return $this->content;
	}

	/**
	 * Gets the content length.
	 *
	 * @return int
	 *	The content length.
	 */
	public final function getContentLength() : int
	{
		return ($this->contentLength ?? ($this->contentLength = strlen($this->getContent())));
	}

	/**
	 * Gets the content type.
	 *
	 * @return string
	 *	The content type.
	 */
	public final function getContentType() : ?string
	{
		if (!isset($this->contentType) && !$this->getMethod()->isSafe())
		{
			foreach ([ 'CONTENT_TYPE', 'HTTP_CONTENT_TYPE', '$$CONTENT_TYPE' ] as $i => $token)
			{
				if (isset($_SERVER[$token]))
				{
					$this->contentType = $_SERVER[$token];
					return $this->contentType;
				}
			}

			$this->contentType = 'plain/text';
		}

		return $this->contentType;
	}

	/**
	 * Gets the context.
	 *
	 * @return IHttpContext
	 *	The context.
	 */
	public final function getContext() : IHttpContext
	{
		return RuntimeHttpContext::getInstance();
	}

	/**
	 * Gets the cookies.
	 *
	 * @return array
	 *	The cookies.
	 */
	public final function getCookies() : array
	{
		if (!isset($this->cookies))
		{
			$this->cookies = [];

			if (isset($_COOKIE))
			{
				foreach ($_COOKIE as $name => $value)
				{
					if (is_string($value))
					{
						$this->cookies[$name] = new HttpCookie($name, $value);
					}
				}
			}
		}

		return $this->cookies;
	}

	/**
	 * Gets the files.
	 *
	 * @return array
	 *	The files.
	 */
	public final function getFiles() : array
	{
		return [];
	}

	/**
	 * Gets the form.
	 *
	 * @return IHttpForm
	 *	The form.
	 */
	public final function getForm() : ?IHttpForm
	{
		return null;
	}

	/**
	 * Gets the headers.
	 *
	 * @return IHttpHeaderCollection
	 *	The headers.
	 */
	public final function getHeaders() : IHttpHeaderCollection
	{
		return RuntimeHttpHeaderCollection::getInstance();
	}

	/**
	 * Gets the method.
	 *
	 * @return IHttpMethod
	 *	The method.
	 */
	public final function getMethod() : IHttpMethod
	{
		return HttpMethod::getInstance($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return RuntimeEnvironment::getInstance()->getApplicationScriptPath();
	}

	/**
	 * Gets the uniform resource location (URI) relative to the host
	 * document root.
	 *
	 * @return string
	 *	The uniform resource location.
	 */
	public final function getUrl() : string
	{
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * Gets the user agent.
	 *
	 * @return IHttpUserAgent
	 *	The user agent.
	 */
	public final function getUserAgent() : IHttpUserAgent
	{
		if (!$this->userAgent)
		{
			switch (true)
			{
				case (isset($_SERVER['HTTP_USER_AGENT'])):
					$this->userAgent = HttpUserAgentProvider::getInstance()->getUserAgent($_SERVER['HTTP_USER_AGENT']);
					break;

				default:
					$this->userAgent = HttpUserAgentProvider::getInstance()->getUserAgent('Unknown/1.0.0');
					break;
			}
		}

		return $this->userAgent;
	}

	/**
	 * Gets the user languages.
	 *
	 * @return array
	 *	The user languages.
	 */
	public final function getUserLanguages() : array
	{
		return [];
	}
}
