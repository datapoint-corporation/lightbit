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

function __exit(int $code = 0) : void
{
	if ($application = __application_get())
	{
		$application->dispose();
	}

	__state_save();
	exit($code);
}

function __lightbit_autoload(string $__CLASS__) : void
{
	try
	{
		__include_file(__class_path_resolve($__CLASS__));
	}
	catch (Throwable $e)
	{
		__throw_class_not_found($__CLASS__, sprintf('Can not load class: class %s', $__CLASS__), $e);
	}
}

function __lightbit_error_handler(int $code, string $message, string $file, int $line) : bool
{
	switch ($code)
	{
		case E_DEPRECATED:
		case E_NOTICE:
		case E_WARNING:
		case E_USER_DEPRECATED:
		case E_USER_NOTICE:
		case E_USER_WARNING:
			return false;
	}

	$category = 'UNKNOWN ERROR';

	switch ($code)
	{
		case E_ERROR:
		case E_USER_ERROR:
			$category = 'FATAL ERROR';
			break;

		case E_WARNING:
		case E_NOTICE:
		case E_DEPRECATED:
		case E_STRICT:
		case E_RECOVERABLE_ERROR:
		case E_USER_WARNING:
		case E_USER_NOTICE:
		case E_USER_DEPRECATED:
			$category = 'WARNING';
			break;

		case E_PARSE:
			$category = 'PARSE ERROR';
			break;

		case E_COMPILE_ERROR:
			$category = 'COMPILE ERROR';
			break;

		case E_CORE_ERROR:
			$category = 'CORE ERROR';
			break;

		case E_CORE_WARNING:
			$category = 'CORE WARNING';
			break;

		case E_COMPILE_ERROR:
			$category = 'COMPILE ERROR';
			break;

		case E_COMPILE_WARNING:
			$category = 'COMPILE WARNING';
			break;
	}

	if (__environment_is_cli())
	{
		echo sprintf('%s: %s at %s, line %d', $category, $message, $file, $line), PHP_EOL;
	}
	else
	{
		echo __html_element('h1', null, $category), PHP_EOL;
		echo __html_element('p', null, sprintf('%s at %s, line %d', $message, $file, $line)), PHP_EOL;
	}

	__exit(1);
	return true;
}

function __lightbit_exception_handler(Throwable $e) : bool
{
	if ($application = __application_get())
	{
		$application->throwable($e);
	}
	else
	{
		__lightbit_throwable($e);
	}

	__exit(1);
	return true;
}

function __lightbit_throwable(Throwable $e) : void
{
	if (__environment_is_web())
	{
		echo __html_element('h1', null, __type_of($e)), PHP_EOL;
		echo __html_element('p', null, $e->getMessage()), PHP_EOL;

		do
		{
			echo __html_element('p', [ 'style' => 'font-weight: bold; margin-top: 1.5em' ], __type_of($e)), PHP_EOL;
			echo __html_element('p', null, sprintf('%s at %s, line %d', $e->getMessage(), $e->getFile(), $e->getLine())), PHP_EOL;
			echo __html_element('pre', null, $e->getTraceAsString()), PHP_EOL;
			echo PHP_EOL;
		}
		while ($e = $e->getPrevious());
	}

	else
	{
		do
		{
			echo sprintf('%s: %s at %s, line %d', __type_of($e), $e->getMessage(), $e->getFile(), $e->getLine()), PHP_EOL;
			echo $e->getTraceAsString(), PHP_EOL;
			echo PHP_EOL;
		}
		while ($e = $e->getPrevious());
	}
}

function __lightbit_next_id() : int
{
	static $id = -1;
	return ++$id;
}

function __lightbit_version() : string
{
	return '1.0.0';
}
