<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2018 Datapoint — Sistemas de Informação, Unipessoal, Lda.
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

// Constants
// -----------------------------------------------------------------------------
const LB_PATH_LIGHTBIT = __DIR__;

// Base
// -----------------------------------------------------------------------------
include (LB_PATH_LIGHTBIT . '/libraries/Lightbit.php');

(
	function(Lightbit $lightbit)
	{
		$lightbit->addIncludePath(__DIR__ . '/libraries');

		spl_autoload_register
		(
			function(string $subject) use ($lightbit)
			{
				if ($path = $lightbit->getClassPath($subject))
				{
					include $path;
				}
			}
		);

		set_error_handler
		(
			// int $level , string $errstr [, string $errfile [, int $errline [, array $errcontext ]]]
			function(int $level, string $message, string $filePath = null, int $line = null, array $context = null)
			{
				throw new Lightbit\ErrorException($level, $message, $filePath, $line);
			}
		);
	}
)

(Lightbit::getInstance());

// Bootstrap
// -----------------------------------------------------------------------------
(
	function()
	{
		// Debug flag.
		if (!defined('LB_DEBUG'))
		{
			define('LB_DEBUG', true);
		}

		// Configuration.
		if (!defined('LB_CONFIGURATION'))
		{
			define('LB_CONFIGURATION', (LB_DEBUG ? 'development' : 'production'));
		}

		// Script path.
		if (!isset($_SERVER['SCRIPT_FILENAME']) || !($path = realpath($_SERVER['SCRIPT_FILENAME'])))
		{
			throw new \Lightbit\ConstantNotSetBootstrapException('LB_PATH_APPLICATION');
		}

		define('LB_PATH_SCRIPT', $path);

		// Application path.
		if (!defined('LB_PATH_APPLICATION'))
		{
			define('LB_PATH_APPLICATION', dirname($path));
		}

		Lightbit::getInstance()->restore();
	}
)

();
