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

$__LIGHTBIT_CLASS = [];

function __class_exists(string $class) : bool
{
	return is_file(__class_path_resolve($class));
}

function __class_is_a(string $subject, string $candidate) : bool
{
	return ($subject === $candidate || is_subclass_of($candidate, $subject, true));
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
	global $__LIGHTBIT_CLASS;

	if (!isset($__LIGHTBIT_CLASS[$class]))
	{
		if ($i = strrpos($class, '\\'))
		{
			return $__LIGHTBIT_CLASS[$class]
				= __namespace_path_resolve(substr($class, 0, $i))
			 	. strtr(substr($class, $i), [ '\\' => DIRECTORY_SEPARATOR ])
				. '.php';
		}

		return $__LIGHTBIT_CLASS[$class]
			= ($i = resolve_stream_include_path($j = ($class . '.php')))
			? $i
			: $j;
	}

	return $__LIGHTBIT_CLASS[$class];
}

function __class_register(string $class, string $path) : void
{
	global $__LIGHTBIT_CLASS;

	if (!isset($__LIGHTBIT_CLASS[$class]))
	{
		$__LIGHTBIT_CLASS[$class] = $path;
	}
}
