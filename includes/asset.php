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

$__LIGHTBIT_ASSET_PREFIX = [];
$__LIGHTBIT_ASSET = [];

function __asset_guid(?string $context, ?string $extension, string $asset) : string
{
	return ($context ?? '*') . '//$//' . ($extension ?? '*') . '//$//'. $asset;
}

function __asset_prefix_exists(string $id) : bool
{
	global $__LIGHTBIT_ASSET_PREFIX;
	return (isset($__LIGHTBIT_ASSET_PREFIX[$id]));
}

function __asset_prefix_get_path(string $id) : string
{
	global $__LIGHTBIT_ASSET_PREFIX;

	if (!isset($__LIGHTBIT_ASSET_PREFIX[$id]))
	{
		__throw(sprintf('Can not get asset prefix path, it is not set: prefix %s', $id));
	}

	return $__LIGHTBIT_ASSET_PREFIX[$id];
}

function __asset_prefix_register(string $id, string $path) : void
{
	global $__LIGHTBIT_ASSET_PREFIX;

	if (isset($__LIGHTBIT_ASSET_PREFIX[$id]))
	{
		__throw
		(
			sprintf
			(
				'Can not set asset prefix path, it is already set: prefix %s, to path %s',
				$id,
				$__LIGHTBIT_ASSET_PREFIX[$id]
			)
		);
	}

	$__LIGHTBIT_ASSET_PREFIX[$id] = __path_resolve($path);
}

function __asset_path_resolve(?string $context, ?string $extension, string $asset) : string
{
	global $__LIGHTBIT_ASSET;

	$guid = __asset_guid($context, $extension, $asset);

	if (!isset($__LIGHTBIT_ASSET[$guid]))
	{
		$path;

		if ($i = strpos($asset, '://'))
		{
			$path = __asset_prefix_get_path(substr($asset, 0, $i));
			$path .= substr($asset, $i + 2);
		}
		else if ($context)
		{
			$path = $context . DIRECTORY_SEPARATOR . $asset;
		}
		else
		{
			$backtrace = debug_backtrace(0, 1)[0];

			__throw
			(
				sprintf
				(
					'Can not get asset path from static context, please use a prefix: asset %s, from %s (:%d)',
					$backtrace['file'],
					$backtrace['line']
				)
			);
		}

		if ($extension)
		{
			$path .= '.' . $extension;
		}

		$__LIGHTBIT_ASSET[$guid] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	return $__LIGHTBIT_ASSET[$guid];
}

function __asset_path_resolve_array(array $context, ?string $extension, string $asset) : string
{
	global $__LIGHTBIT_ASSET;

	$guid = __asset_guid(implode(':', $context), $extension, $asset);

	if (!isset($__LIGHTBIT_ASSET[$guid]))
	{
		if ($i = strpos($asset, '://'))
		{
			$path = __asset_prefix_get_path(substr($asset, 0, $i));
			$path .= substr($asset, $i + 2);

			if ($extension)
			{
				$path .= '.' . $extension;
			}

			return $__LIGHTBIT_ASSET[$guid] = $path;
		}

		if (!$context)
		{
			__throw('Can not resolve asset from static context: asset %s', $asset);
		}

		$path;
		$suffix = $asset;

		if ($extension)
		{
			$suffix .= '.' . $extension;
		}

		foreach ($context as $i => $prefix)
		{
			$path = ($prefix . DIRECTORY_SEPARATOR . $suffix);

			if (file_exists($path))
			{
				break;
			}
		}

		$__LIGHTBIT_ASSET[$guid] = $path;
	}

	return $__LIGHTBIT_ASSET[$guid];
}

function __asset_path_resolve_token(string $context, ?string $extension, string $token) : string
{
	global $__LIGHTBIT_ASSET;

	$guid = __asset_guid($context, $extension, $token);

	if (!isset($__LIGHTBIT_ASSET[$guid]))
	{

		if (!preg_match('%^[a-zA-Z][a-zA-Z0-9]*(\\-[a-zA-Z0-9]+)*$%', $token))
		{
			$backtrace = debug_backtrace(0, 1)[0];

			__throw
			(
				sprintf
				(
					'Can not get asset path from token, bad format: token %s, from %s (:%d)',
					$backtrace['file'],
					$backtrace['line']
				)
			);
		}

		$result = $context . DIRECTORY_SEPARATOR . $token;

		if ($extension)
		{
			$result .= '.' . $extension;
		}

		$__LIGHTBIT_ASSET[$guid] = $result;
	}

	return $__LIGHTBIT_ASSET[$guid];
}
