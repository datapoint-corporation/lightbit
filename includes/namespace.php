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

use \Lightbit\NamespacePathResolutionException;
use \Lightbit\IllegalStateException;

$__LIGHTBIT_NAMESPACE = [];

function __namespace_path_resolve(string $namespace) : string
{
	global $__LIGHTBIT_NAMESPACE;

	if (!isset($__LIGHTBIT_NAMESPACE[$namespace]))
	{
		$i;
		$parent = $namespace;

		while (($i = strrpos($parent, '\\')) !== false)
		{
			$parent = substr($namespace, 0, $i);

			if (isset($__LIGHTBIT_NAMESPACE[$parent]))
			{
				return $__LIGHTBIT_NAMESPACE[$namespace]
					= $__LIGHTBIT_NAMESPACE[$parent]
					. DIRECTORY_SEPARATOR
					. strtr(substr($namespace, $i + 1), [ '\\' => DIRECTORY_SEPARATOR ]);
			}
		}

		throw new NamespacePathResolutionException($namespace, sprintf('Can not resolve namespace path: namespace %s', $namespace));
	}

	return $__LIGHTBIT_NAMESPACE[$namespace];
}

function __namespace_register(string $namespace, string $path) : void
{
	global $__LIGHTBIT_NAMESPACE;

	if (!isset($__LIGHTBIT_NAMESPACE[$namespace]))
	{
		$__LIGHTBIT_NAMESPACE[$namespace] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}
}
