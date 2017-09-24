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

$_SERVER['__LIGHTBIT_ENVIRONMENT_DEBUG'] = false;

function __environment_debug_set(bool $debug) : void
{
	$_SERVER['__LIGHTBIT_ENVIRONMENT_DEBUG'] = $debug;
}

function __environment_debug_get() : bool
{
	return $_SERVER['__LIGHTBIT_ENVIRONMENT_DEBUG'];
}

function __environment_is_windows() : bool
{
	static $windows;

	if (!isset($windows))
	{
		$windows = (strpos(strtoupper(PHP_OS), 'WIN') === 0);
	}

	return $windows;
}

function __environment_is_cli() : bool
{
	static $cli;

	if (!isset($cli))
	{
		$cli = (strpos('cli', php_sapi_name()) !== false);
	}

	return $cli;
}

function __environment_is_linux() : bool
{
	static $linux;

	if (!isset($linux))
	{
		$linux = (strtoupper(PHP_OS) === 'linux');
	}
}
