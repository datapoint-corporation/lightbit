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

use \Lightbit\Base\IApplication;
use \Lightbit\ClassPathResolutionException;
use \Lightbit\Html\HtmlView;
use \Lightbit\Http\HttpException;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\NamespacePathResolutionException;

/**
 * Lightbit.
 *
 * This class defines the static methods that are used to implement the
 * framework core behaviours like path resolution, autoloading and context
 * safe script inclusion.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Lightbit
{
	/**
	 * The version.
	 *
	 * @type string
	 */
	public const VERSION = '1.0.0';

	/**
	 * The version build.
	 *
	 * @type string
	 */
	public const VERSION_BUILD = '201705301323';

	/**
	 * The version signature.
	 *
	 * @type string
	 */
	public const VERSION_SIGNATURE = 'Lightbit/1.0.0';

	/**
	 * The application.
	 *
	 * @type IApplication
	 */
	private static $application;

	/**
	 * The classes path.
	 *
	 * @type array
	 */
	private static $classesPath =
	[
		__CLASS__ => __FILE__
	];

	/**
	 * The namespaces path.
	 *
	 * @type array
	 */
	private static $namespacesPath = [];

	/**
	 * The file system alias prefixes path.
	 *
	 * @type array
	 */
	private static $prefixesPath = [];

	/**
	 * Gets the application.
	 *
	 * @return IApplication
	 *	The the application.
	 */
	public static function getApplication() : ?IApplication
	{
		return self::$application;
	}

	/**
	 * Gets a class namespace name.
	 *
	 * @param string $className
	 *	The class namespace name.
	 *
	 * @return string
	 *	The class namespace.
	 */
	public static function getClassNamespaceName(string $className) : string
	{
		if ($i = strrpos($className, '\\'))
		{
			return substr($className, 0, $i);
		}

		return '';
	}

	/**
	 * Gets a class path.
	 *
	 * @param string $className
	 *	The class name.
	 *
	 * @return string
	 *	The class path.
	 */
	public static function getClassPath(string $className) : string
	{
		if (isset(self::$classesPath[$className]))
		{
			return self::$classesPath[$className];
		}

		if ($i = strrpos($className, '\\'))
		{
			return self::$classesPath[$className]
				= self::getNamespacePath(substr($className, 0, $i))
				. DIRECTORY_SEPARATOR
				. strtr(substr($className, $i + 1), '\\', DIRECTORY_SEPARATOR)
				. '.php';
		}

		return $className . '.php';
	}

	/**
	 * Gets a namespace path.
	 *
	 * @param string $namespaceName
	 *	The namespace name.
	 *
	 * @return string
	 *	The namespace path.
	 */
	public static function getNamespacePath(string $namespaceName) : string
	{
		if (isset(self::$namespacesPath[$namespaceName]))
		{
			return self::$namespacesPath[$namespaceName];
		}

		$i = -1;
		while ($i = strpos($namespaceName, '\\', ++$i))
		{
			$parentNamespaceName = substr($namespaceName, 0, $i);

			if (isset(self::$namespacesPath[$parentNamespaceName]))
			{
				return self::$namespacesPath[$namespaceName]
					= self::$namespacesPath[$parentNamespaceName]
					. DIRECTORY_SEPARATOR
					. strtr(substr($namespaceName, $i + 1), '\\', DIRECTORY_SEPARATOR);
			}
		}

		throw new NamespacePathResolutionException($namespaceName, sprintf('Namespace path is not available: "%s"', $namespaceName));
	}

	/**
	 * Gets a file system resource alias prefix path.
	 *
	 * @param string $prefix
	 *	The file system resource alias prefix.
	 *
	 * @return string
	 *	The file system resource alias prefix path.
	 */
	public static function getPrefixPath(string $prefix) : string
	{
		if (isset(self::$prefixesPath[$prefix]))
		{
			return self::$prefixesPath[$prefix];
		}

		throw new Exception(sprintf('File system resource alias prefix is not defined: "%s"', $prefix));
	}

	/**
	 * Handles a throwable.
	 *
	 * @param Throwable $throwable
	 *	The throwable.
	 */
	public static function handleThrowable(\Throwable $throwable) : void
	{
		static $throwing;

		throw $throwable;

		if (!isset($throwing))
		{
			$throwing = true;

			while (ob_get_level() > 0)
			{
				ob_end_clean();
			}

			if(self::isCli())
			{
				do
				{
					echo get_class($throwable), ': ', $throwable->getMessage(), PHP_EOL;
					echo $throwable->getTraceAsString(), PHP_EOL;
					echo PHP_EOL;

					$throwable = $throwable->getPrevious();
				}
				while ($throwable);
			}

			else
			{
				if (!($throwable instanceof HttpException))
				{
					$throwable = new HttpException(500, $throwable->getMessage(), $throwable);
				}

				(new HtmlView((new Alias('lightbit://views/http/throwable'))->resolve('php')))
					->run([ 'throwable' => $throwable ]);
			}

			if (self::$application)
			{
				self::$application->terminate(1);
			}
		}
		
		exit (1);
	}

	/**
	 * Checks for a class availability.
	 *
	 * @param string $className
	 *	The class name.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function hasClass(string $className) : bool
	{
		if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false))
		{
			$classPath = self::getClassPath($className);

			if (!file_exists($classPath))
			{
				return false;
			}
		}

		return true;
	}


	/**
	 * Includes a script file.
	 *
	 * @param string $alias
	 *	The script file system alias.
	 *
	 * @return mixed
	 *	The script result.
	 */
	public static function include(string $alias) // : mixed
	{
		return self::inclusion()((new Alias($alias))->resolve('php'));
	}

	/**
	 * Creates an inclusion closure.
	 *
	 * @return Closure
	 *	The inclusion closure.
	 */
	public static function inclusion() : \Closure
	{
		static $inclusion;

		if (!isset($inclusion))
		{
			$inclusion = function(string $__FILE__, array $__DATA__ = null)
			{
				if (isset($__DATA__))
				{
					foreach ($__DATA__ as $__K__ => $__V__)
					{
						${$__K__} = $__V__;
						unset($__K__);
						unset($__V__);
					}
				}

				unset($__DATA__);

				return require $__FILE__;
			};

			$inclusion = $inclusion->bindTo(null, null);
		}

		return $inclusion;
	}

	/**
	 * Checks if this is a command line interface.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function isCli() : bool
	{
		static $result;

		if (!isset($result))
		{
			$result = (strpos('cli', php_sapi_name()) !== false);
		}

		return $result;
	}

	/**
	 *
	 */
	public static function isDebug() : bool
	{
		return true;
	}
	
	/**
	 * Loads a class.
	 *
	 * @param string $className
	 *	The class name.
	 */
	public static function loadClass(string $className) : void
	{
		self::inclusion()(self::getClassPath($className));
	}

	/**
	 * Creates, registers and runs a new application of the given class
	 * according to the current runtime environment.
	 *
	 * At the end of the application execution, it will be unregistered
	 * and all Lightbit resources will be properly disposed of in preparation
	 * for the next request, as applicable.
	 *
	 * @param string $className
	 *	The application class name.
	 *
	 * @param string $path
	 *	The application path.
	 *
	 * @param string $configuration
	 *	The application configuration file system alias.
	 *
	 * @return int
	 *	The result.
	 */
	public static function run(string $className, string $path, string $configuration = null) : int
	{
		// The public and private prefixes may be implicitly defined based
		// on the application path and the current script file name.
		if (!isset(self::$prefixesPath['public']))
		{
			self::$prefixesPath['public']
				= (isset($_SERVER['SCRIPT_FILENAME']))
				? dirname($_SERVER['SCRIPT_FILENAME'])
				: ($path . DIRECTORY_SEPARATOR . 'public');
		}

		if (!isset(self::$prefixesPath['private']))
		{
			self::$prefixesPath['private'] = $path;
		}

		if ($configuration)
		{
			$configuration = self::include($configuration);
		}

		self::$application = new $className(strtr($path, [ '/' => DIRECTORY_SEPARATOR ]), $configuration);
		$result = self::$application->run();
		self::$application->dispose();
		self::$application = null;

		return $result;
	}

	/**
	 * Sets the application.
	 *
	 * @param IApplication $application
	 *	The application.
	 */
	public static function setApplication(?IApplication $application) : void
	{
		self::$application = $application;
	}

	/**
	 * Sets a class path.
	 *
	 * @param string $className
	 *	The class name.
	 *
	 * @param string $path
	 *	The class path.
	 */
	public static function setClassPath(string $className, string $path) : void
	{
		self::$classesPath[$className] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets a namespace path.
	 *
	 * @param string $namespaceName
	 *	The namespace name.
	 *
	 * @param string $path
	 *	The namespace path.
	 */
	public static function setNamespacePath(string $namespaceName, string $path) : void
	{
		self::$namespacesPath[$namespaceName] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets a file system resource alias prefix path.
	 *
	 * @param string $prefix
	 *	The file system resource alias prefix.
	 *
	 * @param string $path
	 *	The file system resource alias prefix path.
	 */
	public static function setPrefixPath(string $prefix, string $path) : void
	{
		self::$prefixesPath[$prefix] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		trigger_error(sprintf('Class does not support construction: "%s"', __CLASS__), E_USER_ERROR);
		exit(1);
	}
}
