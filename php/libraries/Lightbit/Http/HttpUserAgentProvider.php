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

use \Lightbit\Http\IHttpUserAgent;

/**
 * HttpUserAgentProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class HttpUserAgentProvider implements IHttpUserAgentProvider
{
	/**
	 * The user agent provider.
	 *
	 * @var IHttpUserAgentProvider
	 */
	private static $instance;

	/**
	 * Gets the user agent provider.
	 *
	 * @return IHttpUserAgentProvider
	 *	The user agent provider.
	 */
	public static final function getInstance() : IHttpUserAgentProvider
	{
		return (self::$instance ?? (self::$instance = new HttpUserAgentProvider()));
	}

	/**
	 * The user agent factory.
	 *
	 * @var array
	 */
	private $userAgentFactory;

	/**
	 * The instances.
	 *
	 * @var array
	 */
	private $userAgents;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->userAgentFactory = new HttpUserAgentFactory();
		$this->userAgents = [];
	}

	/**
	 * Gets a user agent.
	 *
	 * @param string $userAgentString
	 *	The user agent string.
	 *
	 * @return IHttpUserAgent
	 *	The user agent.
	 */
	public final function getUserAgent(string $userAgentString) : IHttpUserAgent
	{
		if (!isset($this->userAgents[$userAgentString]))
		{
			$this->userAgents[$userAgentString] = $this->userAgentFactory->createUserAgent($userAgentString);
		}

		return $this->userAgents[$userAgentString];
	}

	/**
	 * Sets the user agent factory.
	 *
	 * @param IHttpUserAgentFactory $userAgentFactory
	 *	The user agent factory.
	 */
	public final function setUserAgentFactory(IHttpUserAgentFactory $userAgentFactory) : void
	{
		$this->userAgentFactory = $userAgentFactory;
	}
}
