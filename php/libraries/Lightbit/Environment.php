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

class Environment
{
	private static $instance;

	public static final function getInstance() : Environment
	{
		return (self::$instance ?? (self::$instance = new Environment()));
	}

	private $name;

	private $linux;

	private $production;

	private $web;

	private $windows;

	private function __construct()
	{
		$this->variables = new StringMap(getenv());
	}

	public final function isCli() : bool
	{
		return !$this->isWeb();
	}

	public final function getName() : string
	{
		return ($this->name ?? ($this->name = strtolower($this->variables->getString('LB_ENVIRONMENT', true) ?? LB_ENVIRONMENT)));
	}

	public final function isLinux() : bool
	{
		return ($this->linux ?? ($this->linux = (PHP_OS_FAMILY === 'Linux')));
	}

	public final function isProduction() : bool
	{
		return ($this->production ??  ($this->production = ($this->getName() === 'production')));
	}

	public final function isWeb() : bool
	{
		return ($this->web ?? ($this->web = (isset($_SERVER['HTTP_HOST']))));
	}

	public final function isWindows() : bool
	{
		return ($this->windows ?? ($this->windows = (PHP_OS_FAMILY === 'Windows')));
	}
}
