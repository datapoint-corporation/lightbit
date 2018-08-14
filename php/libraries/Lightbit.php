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

use \Lightbit\Data\Caching\CacheProvider;

/**
 * Lightbit.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
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
	 * Gets the singleton instance.
	 *
	 * @return Lightbit
	 *	The singleton instance.
	 */
	public static function getInstance() : Lightbit
	{
		return (self::$instance ?? (self::$instance = new Lightbit()));
	}

	/**
	 * The resource bundle lookup path list map.
	 *
	 * @var array
	 */
	private $bundlePathListMap;

	/**
	 * The class path map.
	 *
	 * @var array
	 */
	private $classPathMap;

	/**
	 * The class path map update flag.
	 *
	 * @var array
	 */
	private $classPathMapUpdate;

	/**
	 * The inclusion.
	 *
	 * @var Closure
	 */
	private $inclusion;

	/**
	 * The library lookup path list.
	 *
	 * @var array
	 */
	private $libraryLookupPathList;

	/**
	 * The key value file path map.
	 *
	 * @var array
	 */
	private $keyValueFilePathMap;

	/**
	 * The resource path list map.
	 *
	 * @var array
	 */
	private $resourcePathListMap;

	/**
	 * The resource path list map update flag.
	 *
	 * @var bool
	 */
	private $resourcePathListMapUpdate;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->bundlePathListMap = [];
		$this->classPathMap = [];
		$this->keyValueFilePathMap = [];
		$this->libraryLookupPathList = [];
		$this->resourcePathListMap = [];

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

				unset($__DATA__);

				return require ($__FILE__);
			}
		);
	}

	/**
	 * Sets an additional resource bundle path.
	 *
	 * @param string $bundle
	 *	The resource bundle identifier.
	 *
	 * @param string $bundlePath
	 *	The resource bundle path.
	 */
	public final function addBundlePath(string $bundle, string $bundlePath) : void
	{
		$this->bundlePathListMap[$bundle][] = $bundlePath;
	}

	/**
	 * Sets an additional resource bundle path map.
	 *
	 * @param array $bundlePathMap
	 *	The resource bundle path map.
	 */
	public final function addBundlePathMap(array $bundlePathMap) : void
	{
		foreach ($bundlePathMap as $bundle => $bundlePath)
		{
			$this->addBundlePath($bundle, $bundlePath);
		}
	}

	/**
	 * Sets an additional library lookup path.
	 *
	 * @param string $lookupPath
	 *	The lookup path.
	 */
	public final function addLibraryLookupPath(string $lookupPath) : void
	{
		$this->libraryLookupPathList[] = strtr($lookupPath, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets an additional library lookup path list.
	 *
	 * @param array $lookupPathList
	 *	The additional library lookup path list.
	 */
	public final function addLibraryLookupPathList(array $lookupPathList) : void
	{
		foreach ($lookupPathList as $i => $lookupPath)
		{
			$this->addLibraryLookupPath($lookupPath);
		}
	}

	/**
	 * Sets an additional module path.
	 *
	 * @param string $modulePath
	 *	The module path.
	 */
	public final function addModulePath(string $modulePath) : void
	{
		$this->addLibraryLookupPath($modulePath . '/libraries');

		$this->addBundlePathMap([
			'hooks' => ($modulePath . '/hooks'),
			'layouts' => ($modulePath . '/layouts'),
			'messages' => ($modulePath . '/messages'),
			'settings' => ($modulePath . '/settings'),
			'tests' => ($modulePath . '/tests'),
			'themes' => ($modulePath . '/themes'),
			'views' => ($modulePath . '/views')
		]);
	}

	/**
	 * Sets an additional module path list.
	 *
	 * @param string $modulePathList
	 *	The module path list.
	 */
	public final function addModulePathList(array $modulePathList) : void
	{
		foreach ($modulePathList as $i => $modulePath)
		{
			$this->addModulePath($modulePath);
		}
	}

	/**
	 * Commits from internal cache.
	 */
	public final function commit() : void
	{
		if (LB_CACHE)
		{
			$opcache = CacheProvider::getInstance()->getOpCache();

			if ($this->classPathMapUpdate && LB_CACHE_CLASS_PATH)
			{
				$opcache->write('lightbit.class.path', $this->classPathMap);
			}

			if ($this->resourcePathListMapUpdate && LB_CACHE_RESOURCE_PATH)
			{
				$opcache->write('lightbit.resource.path', $this->resourcePathListMap);
			}
		}
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
	public final function getClassPath(string $className) : ?string
	{
		if (!isset($this->classPathMap[$className]) && !array_key_exists($className, $this->classPathMap))
		{
			$this->classPathMap[$className] = null;

			$filePathSuffix = (strtr(('\\' . $className), [ '\\' => DIRECTORY_SEPARATOR ]) . '.php');

			foreach ($this->libraryLookupPathList as $i => $libraryLookupPath)
			{
				$filePath = $libraryLookupPath . $filePathSuffix;

				if (file_exists($filePath) && is_file($filePath))
				{
					$this->classPathMap[$className] = $filePath;
					$this->classPathMapUpdate = true;
					break;
				}
			}
		}

		return $this->classPathMap[$className];
	}

	/**
	 * Gets a resource path.
	 *
	 * @param string $extension
	 *	The resource extension.
	 *
	 * @param string $resource
	 *	The resource identifier.
	 *
	 * @return string
	 *	The resource path.
	 */
	public final function getResourcePath(?string $extension, string $resource) : ?string
	{
		return ($this->getResourcePathList($extension, $resource)[0] ?? null);
	}

	/**
	 * Gets a resource path list.
	 *
	 * @param string $extension
	 *	The resource extension.
	 *
	 * @param string $resource
	 *	The resource identifier.
	 *
	 * @return string
	 *	The resource path list.
	 */
	public final function getResourcePathList(?string $extension, string $resource) : array
	{
		$id = ($resource . ($extension ? ('.' . $extension) : ''));

		if (!isset($this->resourcePathListMap[$id]))
		{
			$this->resourcePathListMap[$id] = [];

			if (strpos($id, '://'))
			{
				list($prefix, $suffix) = explode(':/', $id);

				if (isset($this->bundlePathListMap[$prefix]))
				{
					$suffix = strtr($suffix, [ '/' => DIRECTORY_SEPARATOR ]);

					foreach ($this->bundlePathListMap[$prefix] as $i => $path)
					{
						$path .= $suffix;

						if (file_exists($path))
						{
							$this->resourcePathListMap[$id][] = $path;
							$this->resourcePathListMapUpdate = true;
						}
					}
				}
			}
		}

		return $this->resourcePathListMap[$id];
	}

	/**
	 * Includes a file.
	 *
	 * @param string $filePath
	 *	The inclusion file path.
	 *
	 * @param array $variableMap
	 *	The inclusion variable map.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public final function include(string $filePath, array $variableMap = null) // : mixed
	{
		return ($this->inclusion->bindTo(null, 'static'))($filePath, $variableMap);
	}

	/**
	 * Includes a file.
	 *
	 * @param object $scope
	 *	The inclusion scope.
	 *
	 * @param string $filePath
	 *	The inclusion file path.
	 *
	 * @param array $variableMap
	 *	The inclusion variable map.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public final function includeAs(object $scope, string $filePath, array $variableMap = null) // : mixed
	{
		return ($this->inclusion->bindTo($scope, 'static'))($filePath, $variableMap);
	}

	/**
	 * Restores from internal cache.
	 */
	public final function restore() : void
	{
		if (LB_CACHE)
		{
			$opcache = CacheProvider::getInstance()->getOpCache();

			if (LB_CACHE_CLASS_PATH)
			{
				if ($opcache->read('lightbit.class.path', $classPathMap))
				{
					$this->classPathMap += $classPathMap;
				}
			}

			if (LB_CACHE_RESOURCE_PATH)
			{
				if ($opcache->read('lightbit.resource.path', $resourcePathListMap))
				{
					$this->resourcePathListMap += $resourcePathListMap;
				}
			}
		}
	}
}
