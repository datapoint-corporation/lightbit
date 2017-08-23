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
use \Lightbit\ClassNotFoundException;
use \Lightbit\ClassPathResolutionException;
use \Lightbit\Exception;
use \Lightbit\Helpers\TypeHelper;
use \Lightbit\Html\HtmlView;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\IO\FileSystem\FileNotFoundException;
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
	 * The debug flag.
	 *
	 * @type bool
	 */
	private static $debug = false;

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
	public static function getApplication() : IApplication
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
			try
			{
				return self::$classesPath[$className]
					= self::getNamespacePath(substr($className, 0, $i))
					. DIRECTORY_SEPARATOR
					. strtr(substr($className, $i + 1), '\\', DIRECTORY_SEPARATOR)
					. '.php';
			}
			catch (NamespacePathResolutionException $e)
			{
				throw new ClassPathResolutionException($className, sprintf('Class path resolution failure: "%s"', $className), $e);
			}
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
	public static function throwable(\Throwable $throwable) : void
	{
		static $throwing;

		if (!isset($throwing))
		{
			$throwing = true;

			if (isset(self::$application))
			{
				self::$application->throwable($throwable);
			}
			else
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
						$__K__ = lcfirst(strtr(ucwords(strtr($__K__, [ '-' => ' ' ])), [ ' ' => '' ]));

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
	 * Checks the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function isDebug() : bool
	{
		return self::$debug;
	}
	
	/**
	 * Loads a class.
	 *
	 * @param string $className
	 *	The class name.
	 */
	public static function loadClass(string $className) : void
	{
		try
		{
			$classPath = self::getClassPath($className);

			if (!file_exists($classPath))
			{
				throw new FileNotFoundException($classPath, sprintf('Class file not found: "%s"', $classPath));
			}

			self::inclusion()(self::getClassPath($className));
		}
		catch (ClassPathResolutionException $e)
		{
			throw new ClassNotFoundException($className, sprintf('Class not found, path resolution failure: "%s"', $className), $e);	
		}
		catch (FileNotFoundException $e)
		{
			throw new ClassNotFoundException($className, sprintf('Class not found, file does not exist: "%s"', $className), $e);
		}
	}

	/**
	 * Creates, registers and runs an application.
	 *
	 * @param string $private
	 *	The application private install path.
	 *
	 * @param string $public
	 *	The application public install path.
	 *
	 * @param string $className
	 *	The application class name.
	 *
	 * @param string $configuration
	 *	The application configuration script file system alias.
	 *
	 * @return int
	 *	The application exit status.
	 */
	public static function run(string $private, string $public, string $className, string $configuration = null) : int
	{
		self::setPrefixPath('private', $private);
		self::setPrefixPath('public', $public);

		$express = null;

		if ($configuration)
		{
			$configurationPath;
			$express = self::inclusion()($configurationPath = (new Alias($configuration))->resolve('php', $private));

			if (isset($express) && !is_array($express))
			{
				throw new Exception(sprintf('Configuration script file error: "%s", script path "%s", returns %s', $configuration, $configurationPath, TypeHelper::getNameOf($express)));
			}
		}

		$result = (self::$application = new $className($private, $express))->run();
		self::$application->dispose();
		self::$application = null;

		return $result;
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
	 * Sets the debug flag.
	 *
	 * @param bool $debug
	 *	The debug flag.
	 */
	public static function setDebug(bool $debug) : void
	{
		self::$debug = $debug;
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
