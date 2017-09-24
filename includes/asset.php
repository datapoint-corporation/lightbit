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

$_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'] = [];

function __asset_bundle_exists(string $id) : bool
{
	return (isset($_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id]));
}

function __asset_bundle_get_path(string $id) : string
{
	if (!isset($_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id]))
	{
		__throw('Asset bundle does not exist: bundle "%s"', $id);
	}

	return $_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id];
}

function __asset_bundle_register(string $id, string $path) : void
{
	if (isset($_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id]))
	{
		__throw
		(
			'Asset bundle already exists: bundle "%s", to path "%s"',
			$id,
			$_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id]
		);
	}

	$_SERVER['__LIGHTBIT_ASSET_BUNDLE_PATH'][$id] = __path_resolve($path);
}

function __asset_path_resolve(?string $context, ?string $extension, string $reference) : string
{
	$path;

	if ($i = strpos($reference, '://'))
	{
		$path = __asset_bundle_get_path(substr($reference, 0, $i));
		$path .= substr($reference, $i + 2);
	}
	else if ($context)
	{
		$path = $context . DIRECTORY_SEPARATOR . $reference;
	}
	else
	{
		__throw('Can not resolve asset from static context: asset "%s"', $reference);
	}

	if ($extension)
	{
		$path .= '.' . $extension;
	}

	return $path;
}

function __asset_path_resolve_array(array $context, ?string $extension, string $reference) : string
{
	if ($i = strpos($reference, '://'))
	{
		$path = __asset_bundle_get_path(substr($reference, 0, $i));
		$path .= substr($reference, $i + 2);

		if ($extension)
		{
			$path .= '.' . $extension;
		}

		return $path;
	}

	if (!$context)
	{
		__throw('Can not resolve asset from static context: asset "%s"', $reference);
	}

	$path;
	$suffix = $reference;

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

	return $path;
}
