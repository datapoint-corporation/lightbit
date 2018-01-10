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
 * @var float
 */
define('LIGHTBIT', microtime(true));

/**
 * The lightbit install path.
 *
 * @var string
 */
const LIGHTBIT_PATH = __DIR__;

/**
 * The lightbit decimal precision.
 *
 * @var int
 */
const LIGHTBIT_DECIMAL_PRECISION = 6;

/**
 * The lightbit version.
 *
 * @var string
 */
const LIGHTBIT_VERSION = '1.0.0';

/**
 * The lightbit version build number.
 *
 * @var int
 */
const LIGHTBIT_VERSION_BUILD = '201709240118';

// Include the Lightbit class file manually to enable path resolution,
// autoloading and other core features.
require __DIR__ . '/functions/lightbit.php';

require __DIR__ . '/functions/action.php';
require __DIR__ . '/functions/application.php';
require __DIR__ . '/functions/asset.php';
require __DIR__ . '/functions/class.php';
require __DIR__ . '/functions/context.php';
require __DIR__ . '/functions/data.php';
require __DIR__ . '/functions/debug.php';
require __DIR__ . '/functions/environment.php';
require __DIR__ . '/functions/event.php';
require __DIR__ . '/functions/globalization.php';
require __DIR__ . '/functions/html.php';
require __DIR__ . '/functions/http.php';
require __DIR__ . '/functions/include.php';
require __DIR__ . '/functions/json.php';
require __DIR__ . '/functions/map.php';
require __DIR__ . '/functions/math.php';
require __DIR__ . '/functions/namespace.php';
require __DIR__ . '/functions/number.php';
require __DIR__ . '/functions/object.php';
require __DIR__ . '/functions/path.php';
require __DIR__ . '/functions/state.php';
require __DIR__ . '/functions/string.php';
require __DIR__ . '/functions/throw.php';
require __DIR__ . '/functions/type.php';
require __DIR__ . '/functions/url.php';

// Include the lightbit type filters manually, enabling basic support for
// automatic query string argument binding.
require __DIR__ . '/functions/type-filters/array.php';
require __DIR__ . '/functions/type-filters/bool.php';
require __DIR__ . '/functions/type-filters/float.php';
require __DIR__ . '/functions/type-filters/int.php';

// Register the lightbit autoloader, exception and error handler
// to enable the expected core behaviours.
spl_autoload_register('__lightbit_autoload', true, true);
set_error_handler('__lightbit_error_handler', E_ALL);
set_exception_handler('__lightbit_exception_handler');

// Resume the internal state, which should save a lot of trouble with
// tasks that would otherwise repeat for every request.
__state_resume();

// Register the Lightbit namespace and file system alias prefix path as
// required by the framework.
__asset_prefix_register('lightbit', __DIR__);
__namespace_register('Lightbit', __DIR__ . '/libraries');
