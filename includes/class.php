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

use \Lightbit\ClassNotFoundException;
use \Lightbit\IO\FileSystem\FileNotFoundException;

$_SERVER['__LIGHTBIT_CLASS_PATH'] = [];

function __class_load(string $class) : void
{
	try
	{
		$path = __class_path_resolve($class);

		if (!is_file($path))
		{
			throw new FileNotFoundException($path, sprintf('File not found: path "%s"', $path));
		}

		__include($path);
	}
	catch (Throwable $e)
	{
		throw new ClassNotFoundException($class, sprintf('Class not found: %s', $class), $e);
	}
}

function __class_exists(string $class) : bool
{
	if (isset($_SERVER['__LIGHTBIT_CLASS_PATH'][$class]))
	{
		return true;
	}

	return class_exists($class, false)
		|| interface_exists($class, false)
		|| is_file(__class_path_resolve($class));
}

function __class_is_a(string $subject, string $candidate) : string
{
	return ($subject === $candidate || is_subclass_of($candidate, $subject));
}

function __class_namespace(string $class) : string
{
	if ($i = strrpos($class, '\\'))
	{
		return substr($class, 0, $i);
	}

	return '';
}

function __class_path_resolve(string $class) : string
{
	if (!isset($_SERVER['__LIGHTBIT_CLASS_PATH'][$class]))
	{
		if ($i = strrpos($class, '\\'))
		{
			return $_SERVER['__LIGHTBIT_CLASS_PATH'][$class]
				= __namespace_path_resolve(substr($class, 0, $i))
			 	. strtr(substr($class, $i), [ '\\' => DIRECTORY_SEPARATOR ])
				. '.php';
		}

		return $_SERVER['__LIGHTBIT_CLASS_PATH'][$class]
			= ($i = resolve_stream_include_path($j = ($class . '.php')))
			? $i
			: $j;
	}

	return $_SERVER['__LIGHTBIT_CLASS_PATH'][$class];
}

function __class_register(string $class, string $path) : void
{
	if (isset($_SERVER['__LIGHTBIT_CLASS_PATH'][$class]))
	{
		__throw
		(
			'Class already exists: class "%s", at path "%s"',
			$class,
			$_SERVER['__LIGHTBIT_CLASS_PATH'][$class]
		);
	}

	$_SERVER['__LIGHTBIT_CLASS_PATH'][$class] = $path;
}
