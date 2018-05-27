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

namespace Lightbit;

use \Lightbit\Data\Collections\StringMap;

/**
 * Environment.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class Environment
{
	/**
	 * The singleton instance.
	 *
	 * @var Environment
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return Environment
	 *	The singleton instance.
	 */
	public static final function getInstance() : Environment
	{
		return (self::$instance ?? (self::$instance = new Environment()));
	}

	/**
	 * The map.
	 *
	 * @var StringMap
	 */
	private $map;

	/**
	 * The name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The linux flag.
	 *
	 * @var bool
	 */
	private $linux;

	/**
	 * The production flag.
	 *
	 * @var bool
	 */
	private $production;

	/**
	 * The web flag.
	 *
	 * @var bool
	 */
	private $web;

	/**
	 * The windows flag.
	 *
	 * @var bool
	 */
	private $windows;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->map = new StringMap(getenv());
	}

	/**
	 * Checks the command line interface flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isCli() : bool
	{
		return !$this->isWeb();
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public final function getName() : string
	{
		return ($this->name ?? ($this->name = strtolower($this->map->getString('LB_ENVIRONMENT', true) ?? LB_ENVIRONMENT)));
	}

	/**
	 * Checks the linux flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isLinux() : bool
	{
		return ($this->linux ?? ($this->linux = (PHP_OS_FAMILY === 'Linux')));
	}

	/**
	 * Checks the production flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isProduction() : bool
	{
		return ($this->production ??  ($this->production = ($this->getName() === 'production')));
	}

	/**
	 * Checks the web flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isWeb() : bool
	{
		return ($this->web ?? ($this->web = (isset($_SERVER['HTTP_HOST']))));
	}

	/**
	 * Checks the windows flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isWindows() : bool
	{
		return ($this->windows ?? ($this->windows = (PHP_OS_FAMILY === 'Windows')));
	}
}
