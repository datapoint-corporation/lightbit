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

use \Lightbit\BootstrapException;
use \Lightbit\ErrorException;
use \Lightbit\Environment;
use \Lightbit\Cli\CliApplication;
use \Lightbit\Http\HttpApplication;

/**
 * The lightbit path.
 *
 * @var string
 */
const LB_PATH_LIGHTBIT = __DIR__;

(
	/**
	 * Core inclusions.
	 */
	function()
	{
		$__require = (function($__FILE__) {
			require ($__FILE__);
		})

		->bindTo(null, 'static');

		$__require(LB_PATH_LIGHTBIT . '/functions.php');

		$__require(LB_PATH_LIGHTBIT . '/libraries/Lightbit.php');
		$__require(LB_PATH_LIGHTBIT . '/libraries/Lightbit/Exception.php');
		$__require(LB_PATH_LIGHTBIT . '/libraries/Lightbit/ErrorException.php');
		$__require(LB_PATH_LIGHTBIT . '/libraries/Lightbit/BootstrapException.php');
	}
)();

return
(
	/**
	 * Bootstrap.
	 *
	 * Checks the current environment type, registers and/or verifies the
	 * applicable constants, sets the error and exception handlers and runs
	 * the application according to the detected environment type.
	 *
	 * @param Lightbit $lightbit
	 *	The lightbit instance.
	 *
	 * @return int
	 *	The application exit status code.
	 */
	function (Lightbit $lightbit) : int
	{
		/**
		 * The environment name.
		 *
		 * @var string
		 */
		defined('LB_ENVIRONMENT') || define('LB_ENVIRONMENT', 'production');

		/**
		 * The error report filter.
		 *
		 * @see http://php.net/manual/en/errorfunc.constants.php
		 * @var int
		 */
		defined('LB_ERROR_REPORT_FILTER') || define('LB_ERROR_REPORT_FILTER', E_ALL);

		/**
		 * The application path.
		 *
		 * @var string
		 */
		defined('LB_PATH_APPLICATION') || define('LB_PATH_APPLICATION', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));

		spl_autoload_register(

			/**
			 * Class autoloader.
			 *
			 * Gets the class path from lightbit and, if found, includes it
			 * in attempt to find the class definition.
			 *
			 * Please note no check is made against the class before or after
			 * the file inclusion and, as such, no exception is thrown if
			 * the class is not defined.
			 *
			 * @param string $className
			 *	The class name.
			 */
			function(string $className) use ($lightbit) : void
			{
				if ($filePath = $lightbit->getClassPath($className))
				{
					$lightbit->include($filePath);
				}
			}
		);

		set_error_handler(

			/**
			 * Error handler.
			 *
			 * @param int $level
			 *	The error level.
			 *
			 * @param string $message
			 *	The error message.
			 *
			 * @param string $filePath
			 *	The error file path.
			 *
			 * @param int $lineNumber
			 *	The error line number.
			 */
			function(int $level, string $message, string $filePath = null, int $lineNumber = null) : void
			{
				if (isset($filePath, $lineNumber))
				{
					$message = sprintf("%s in %s(%d)", $message, $filePath, $lineNumber);
				}

				throw new Lightbit\ErrorException($level, $filePath, $lineNumber, $message);
			},

			LB_ERROR_REPORT_FILTER
		);

		// Set the lightbit and the application paths as module paths,
		// effectively enabling class autoloading for both.
		$lightbit->addModulePathList([
			LB_PATH_LIGHTBIT,
			LB_PATH_APPLICATION
		]);

		// We need to ensure output buffering is turned on as we don't
		// want errors and exception stack traces ending on the client
		// screen and exposing information that might compromise the
		// application security.
		if (ob_get_level() < 1)
		{
			ob_start() || (exit (1));
		}

		// Get the environment type, get the singleton instance of the matching
		// application type and run it.
		$environment = Environment::getInstance();

		if ($environment->isWeb())
		{
			return HttpApplication::getInstance()->run();
		}

		if ($environment->isCli())
		{
			return CliApplication::getInstance()->run();
		}
	}
)
(Lightbit::getInstance());
