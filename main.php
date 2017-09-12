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

/**
 * The Lightbit main script execution micro timestamp.
 *
 * @type float
 */
define('LIGHTBIT', microtime(true));

/**
 * The Lightbit install path.
 *
 * @type string
 */
define('LIGHTBIT_PATH', __DIR__);

/**
 * The Lightbit math fixed point precision.
 *
 * @type int
 */
defined('LIGHTBIT_PRECISION') || define('LIGHTBIT_PRECISION', 6);

// Include the Lightbit class file manually to enable path resolution,
// autoloading and other core features.
require __DIR__ . '/includes/lightbit.php';
require __DIR__ . '/includes/helpers/environment.php';
require __DIR__ . '/includes/helpers/globalization.php';
require __DIR__ . '/includes/helpers/html.php';
require __DIR__ . '/includes/helpers/http.php';
require __DIR__ . '/includes/helpers/math.php';
require __DIR__ . '/includes/helpers/string.php';

// Register the Lightbit namespace and file system alias prefix path as
// required by the framework.
Lightbit::setNamespacePath('Lightbit', __DIR__ . '/libraries');
Lightbit::setPrefixPath('lightbit', __DIR__);

// Register the lightbit autoloader, exception and error handler
// to enable the expected core behaviours.
spl_autoload_register
(
	function(string $className) : bool
	{
		Lightbit::loadClass($className);
		return true;
	},
	true,
	true
);

set_error_handler
(
	function(int $code, string $message, string $filePath, int $line) : bool
	{
		throw new \Lightbit\Exception(sprintf('%s (%d) at %s, line %d', $message, $code, $filePath, $line));
		return true;
	},
	E_ALL
);

set_exception_handler
(
	function(\Throwable $e)
	{
		Lightbit::throwable($e);
	}
);

