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

// Core constants
// -----------------------------------------------------------------------------
/**
 * The framework path.
 *
 * @var string
 */
const LB_PATH_LIGHTBIT = (__DIR__);

// Core inclusions
// -----------------------------------------------------------------------------
(
	/**
	 * Core inclusions.
	 *
	 * Requires the files defining the classes required to bootstrap and
	 * sustain the framework, from a safe context, without exposing
	 * members of the current inclusion scope.
	 */
	function()
	{
		require (LB_PATH_LIGHTBIT . '/libraries/Lightbit.php');
		require (LB_PATH_LIGHTBIT . '/libraries/Lightbit/Exception.php');
		require (LB_PATH_LIGHTBIT . '/libraries/Lightbit/BootstrapException.php');
		require (LB_PATH_LIGHTBIT . '/libraries/Lightbit/AssetManagement/AssetProvider.php');
	}
)();

// Core bootstrap
// -----------------------------------------------------------------------------
(
	/**
	 * Bootstrap.
	 *
	 * Checks for the necessary constants, defining them to default, as
	 * possible; adds the relevant directories to the library paths; registers
	 * the class autoloaders; registers the error and exception handlers;
	 */
	function (Lightbit $lightbit)
	{
		//
		// Constants
		//
		if (!defined('LB_PATH_APPLICATION'))
		{
			throw new BootstrapException(sprintf('Can not bootstrap Lightbit, constant is not defined: "%s"', 'LB_PATH_APPLICATION'));
		}

		/**
		 * The environment name.
		 *
		 * @var string
		 */
		defined('LB_ENVIRONMENT') || define('LB_ENVIRONMENT', 'production');

		//
		// Modules
		//
		$lightbit->addModulePaths([
			LB_PATH_LIGHTBIT,
			LB_PATH_APPLICATION
		]);

		//
		// Class autoloaders
		//
		spl_autoload_register
		(
			/**
			 * Lightbit autoloader.
			 *
			 * Tries to locate the class file through the framework library
			 * lookup path and, if found, includes it.
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

		//
		// Error and exception handlers
		//
		set_error_handler
		(
			function(int $level, string $message, string $filePath = null, int $line = null)
			{
				throw new Lightbit\Exception(sprintf('%s at %s (: %d)', $message, $filePath, $line));
			}
		);
	}
)
(Lightbit::getInstance());

// Core functions
// -----------------------------------------------------------------------------
function lbcompose($subject) // : mixed
{
	static $provider;

	if (!isset($provider))
	{
		$provider = Lightbit\Data\Parsing\ParserProvider::getInstance();
	}

	return $provider->getParser(lbstypeof($subject))->compose($subject);
}

function lbparse(string $type, string $subject) // : mixed
{
	static $provider;

	if (!isset($provider))
	{
		$provider = Lightbit\Data\Parsing\ParserProvider::getInstance();
	}

	return $provider->getParser($type)->parse($subject);
}

/**
 * Gets a type.
 *
 * @param string $type
 *	The type name.
 *
 * @return Type
 *	The type.
 */
function lbtype(string $type) : Lightbit\Reflection\Type
{
	return new Lightbit\Reflection\Type($type);
}

/**
 * Gets the type of a variable.
 *
 * @param array $variable
 *	The variable to get the type of.
 *
 * @return Type
 *	The variable type.
 */
function lbtypeof($variable) : Lightbit\Reflection\Type
{
	return Lightbit\Reflection\Type::getInstanceOf($variable);
}

/**
 * Gets the type of a variable.
 *
 * @param array $variable
 *	The variable to get the type of.
 *
 * @return string
 *	The variable type name.
 */
function lbstypeof($variable) : string
{
	return Lightbit\Reflection\Type::getInstanceOf($variable)->getName();
}
