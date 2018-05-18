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

use \Lightbit\Hooking\HookManager;

/**
 * Lightbit.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Lightbit
{
	/**
	 * The singleton instance.
	 *
	 * @var Lightbit
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return Lightbit
	 * 	The instance.
	 */
	public static final function getInstance() : Lightbit
	{
		return (self::$instance ?? (self::$instance = new Lightbit()));
	}

	/**
	 * The apcu support flag.
	 *
	 * @var bool
	 */
	private $apcu;

	/**
	 * The class path map.
	 *
	 * @var array
	 */
	private $classPathMap;

	/**
	 * The hook manager.
	 *
	 * @var HookManager
	 */
	private $hookManager;

	/**
	 * The inclusion closure.
	 *
	 * @var Closure
	 */
	private $inclusion;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->apcu = function_exists('apcu_store');
		$this->classPathMap = [];

		// Setup the inclusion closure, which is used to safely include
		// script files without exposing members.
		$this->inclusion =
		(
			function(string $__FILE__, array $__DATA__ = null)
			{
				if ($__DATA__)
				{
					foreach ($__DATA__ as $__K__ => $__V__)
					{
						${$__K__} = $__V__;
					}

					unset($__K__, $__V__);
				}

				return include ($__FILE__);
			}
		)

		->bindTo(null, 'static');
	}

	/**
	 * Sets an additional include path.
	 *
	 * @param string $includePath
	 * 	The additional include path.
	 */
	public final function addIncludePath(string $includePath) : void
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . strtr($includePath, [ '/' => DIRECTORY_SEPARATOR ]));
	}

	/**
	 * Commits relevant information to persistent memory storage engines,
	 * as available, to allow for optimization in future requests.
	 */
	public final function commit() : void
	{
		$this->hookManager->trigger('commit');

		if ($this->apcu)
		{
			apcu_store('lightbit.class.path.map', $this->classPathMap);
		}
	}

	/**
	 * Gets a class path.
	 *
	 * @param string $className
	 * 	The class name.
	 *
	 * @return string
	 * 	The class path.
	 */
	public function getClassPath(string $className) : ?string
	{
		if (!isset($this->classPathMap[$className]) && !array_key_exists($className, $this->classPathMap))
		{
			if ($classPath = stream_resolve_include_path(strtr($className, [ '\\' => DIRECTORY_SEPARATOR ]) . '.php'))
			{
				$this->classPathMap[$className] = $classPath;
			}
			else
			{
				$this->classPathMap[$className] = null;
			}
		}

		return $this->classPathMap[$className];
	}

	/**
	 * Gets the include path.
	 *
	 * @return string
	 * 	The include path.
	 */
	public final function getIncludePath() : string
	{
		return get_include_path();
	}

	/**
	 * Gets the include path.
	 *
	 * @return array
	 * 	The include path.
	 */
	public final function getIncludePathAsArray() : array
	{
		return explode(PATH_SEPARATOR, $this->getIncludePath());
	}

	/**
	 * Includes a file.
	 *
	 * @param string $filePath
	 *	The inclusion file path.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function include(string $filePath, array $variables = null)
	{
		return ($this->inclusion)($filePath, $variables);
	}

	/**
	 * Includes a file as an object.
	 *
	 * @param object $object
	 *	The inclusion object.
	 *
	 * @param string $filePath
	 *	The inclusion file path.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function includeAs(object $object, string $filePath, array $variables = null)
	{
		return (($this->inclusion)->bindTo($object, 'static'))($filePath, $variables);
	}

	/**
	 * Restores relevant information from the persistent storage engines,
	 * as available, allowing for optimization of this request.
	 */
	public final function restore() : void
	{
		if ($this->apcu)
		{
			if ($classPathMap = apcu_fetch('lightbit.class.path.map'))
			{
				$this->classPathMap = $classPathMap;
			}
		}

		$this->hookManager = HookManager::getInstance();
		$this->hookManager->import('application://hooks');
		$this->hookManager->import('lightbit://hooks');
		$this->hookManager->trigger('restore');
	}
}
