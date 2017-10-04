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

$_LIGHTBIT_IMPORT = [];

function __import(string $library) // : mixed
{
	global $_LIGHTBIT_IMPORT;

	$context = dirname(debug_backtrace(0, 1)[0]['file']);
	$path;

	if (($i = strpos($library, './')) === 0)
	{
		$path = $context . strtr(substr($library, 1), [ '/' => DIRECTORY_SEPARATOR ]) . '.php';
	}
	else
	{
		$path = __asset_path_resolve($context, 'php', $library);
	}

	if (!isset($_LIGHTBIT_IMPORT[$library]) && !array_key_exists($path, $_LIGHTBIT_IMPORT))
	{
		if (!is_file($path))
		{
			$breakpoint = __debug_trace_call(null, __FUNCTION__)[0];

			__throw_file_not_found
			(
				$path,
				sprintf
				(
					'Can not import library, file not found: file %s', 
					$path
				)
			);
		}

		$_LIGHTBIT_IMPORT[$library] = __include_file_ex($path);
	}

	return $_LIGHTBIT_IMPORT[$library];
}