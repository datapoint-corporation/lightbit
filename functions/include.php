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

function __include(string $library, array $variables = null) // : mixed
{
	$path = __asset_path_resolve(null, 'php', $library);

	if (!is_file($path))
	{
		$breakpoint = __debug_trace_call(null, __FUNCTION__)[0];

		__throw_file_not_found
		(
			$path,
			sprintf
			(
				'Can not include library, file not found: file %s, at %s (:%d)', 
				$path,
				$breakpoint['file'],
				$breakpoint['line']
			)
		);
	}

	return __include_file_ex($path, $variables);
}

function __include_as($scope, string $library, array $variables = null) // : mixed
{
	$path = __asset_path_resolve(null, 'php', $library);

	if (!is_file($path))
	{
		$breakpoint = __debug_trace_call(null, __FUNCTION__)[0];

		__throw_file_not_found
		(
			$path,
			sprintf
			(
				'Can not include library, file not found: file %s, at %s (:%d)', 
				$path,
				$breakpoint['file'],
				$breakpoint['line']
			)
		);
	}

	return __include_file_as_ex($scope, $path, $variables);
}

function __include_file(string $path, array $variables = null) // : mixed
{
	if (!is_file($path))
	{
		__throw_file_not_found
		(
			$path,
			sprintf
			(
				'Can not include library, file not found: file %s', 
				$path
			)
		);
	}

	return __include_file_ex($path, $variables);
}

function __include_file_as($object, string $path, array $variables = null) // : mixed
{
	if (!is_file($path))
	{
		__throw_file_not_found
		(
			$path,
			sprintf
			(
				'Can not include library, file not found: file %s', 
				$path
			)
		);
	}

	return __include_file_as_ex($object, $path, $variables);
}

function __include_file_ex(string $__PATH__, array $__DATA__ = null) // : mixed
{
	if (isset($__DATA__))
	{
		foreach ($__DATA__ as $__K__ => $__V__)
		{
			$__K__ = lcfirst(strtr(ucwords(strtr($__K__, [ '-' => ' ' ])), [ ' ' => '' ]));

			${$__K__} = $__V__;
			unset($__K__);
			unset($__V__);
		}
	}

	unset($__DATA__);

	return include($__PATH__);
}

function __include_file_as_ex($__THIS__, string $__PATH__, array $__DATA__ = null) // : mixed
{
	static $closure;

	if (!$closure)
	{
		$closure = function(string $__PATH__, array $__DATA__ = null) // : mixed
		{
			if (isset($__DATA__))
			{
				foreach ($__DATA__ as $__K__ => $__V__)
				{
					$__K__ = lcfirst(strtr(ucwords(strtr($__K__, [ '-' => ' ' ])), [ ' ' => '' ]));

					${$__K__} = $__V__;
					unset($__K__);
					unset($__V__);
				}
			}

			unset($__DATA__);

			return include($__PATH__);
		};
	}

	return $closure->bindTo($__THIS__, null)($__PATH__, $__DATA__);
}