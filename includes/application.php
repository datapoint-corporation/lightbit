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

use \Lightbit\Base\Application;

$_SERVER['__LIGHTBIT_APPLICATION'] = null;

function __application() : Application
{
	if (!isset($_SERVER['__LIGHTBIT_APPLICATION']))
	{
		__throw_state('Can not get application, it does not exist.');
	}

	return $_SERVER['__LIGHTBIT_APPLICATION'];
}

function __application_get() : ?Application
{
	return $_SERVER['__LIGHTBIT_APPLICATION'];
}

function __application_register(Application $application) : void
{
	if (isset($_SERVER['__LIGHTBIT_APPLICATION']))
	{
		__throw('Can not register application because it already exists.');
	}

	$_SERVER['__LIGHTBIT_APPLICATION'] = $application;
}

function __application_run(string $class, string $private, string $public, string $configuration = null) : int
{
	__asset_bundle_register('private', $private);
	__asset_bundle_register('public', $public);

	$properties = null;

	if ($configuration)
	{
		$properties = __include(__asset_path_resolve($private, 'php', $configuration));

		if (!is_array($properties))
		{
			__throw('Configuration script did not return properties array: asset "%s", script "%s"', $configuration, __asset_path_resolve($private, 'php', $configuration));
		}
	}

	$application = __object_construct_a(Application::class, $class, $private, $properties);
	__application_register($application);

	__context_set($application);
	__action_set(null);
	$result = $application->run();
	__context_set(null);
	__action_set(null);

	return $result;
}
