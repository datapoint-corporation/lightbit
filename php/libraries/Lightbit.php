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

final class Lightbit
{
	private static $instance;

	public static function getInstance() : Lightbit
	{
		return (self::$instance ?? (self::$instance = new Lightbit()));
	}

	private $inclusion;

	private $libraryLookupPathList;

	private $resourceBundleLookupPathListMap;

	private $resourcePathListMap;

	public function __construct()
	{
		$this->libraryLookupPathList = [];
		$this->resourceBundleLookupPathListMap = [];
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

	public function addLibraryLookupPath(string $path) : void
	{
		$this->libraryLookupPathList[] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	public function addLibraryLookupPathList(array $pathList) : void
	{
		foreach ($pathList as $i => $path)
		{
			$this->addLibraryLookupPath($path);
		}
	}

	public function addModulePath(string $path) : void
	{
		$this->addLibraryLookupPath($path . '/libraries');

		$this->addResourceBundleLookupPathMap([
			'hooks' => ($path . '/hooks'),
			'messages' => ($path . '/messages'),
			'settings' => ($path . '/settings'),
			'tests' => ($path . '/tests'),
			'theme' => ($path . '/theme'),
			'views' => ($path . '/views')
		]);
	}

	public function addModulePathList(array $pathList) : void
	{
		foreach ($pathList as $i => $path)
		{
			$this->addModulePath($path);
		}
	}

	public function addResourceBundleLookupPath(string $bundle, string $path) : void
	{
		$this->resourceBundleLookupPathListMap[$bundle][] = $path;
	}

	public function addResourceBundleLookupPathMap(array $resourceBundleLookupPathMap) : void
	{
		foreach ($resourceBundleLookupPathMap as $bundle => $path)
		{
			$this->addResourceBundleLookupPath($bundle, $path);
		}
	}

	public function getClassPath(string $className) : ?string
	{
		$filePathSuffix = (strtr(('\\' . $className), [ '\\' => DIRECTORY_SEPARATOR ]) . '.php');

		foreach ($this->libraryLookupPathList as $i => $libraryLookupPath)
		{
			$filePath = $libraryLookupPath . $filePathSuffix;

			if (file_exists($filePath) && is_file($filePath))
			{
				return $filePath;
			}
		}

		return null;
	}

	public function getResourcePath(?string $extension, string $resource) : ?string
	{
		return ($this->getResourcePathList($extension, $resource)[0] ?? null);
	}

	public function getResourcePathList(?string $extension, string $resource) : array
	{
		$id = ($resource . ($extension ? ('.' . $extension) : ''));

		if (!isset($this->resourcePathListMap[$id]))
		{
			$this->resourcePathListMap[$id] = [];

			if (strpos($id, '://'))
			{
				list($prefix, $suffix) = explode(':/', $id);

				if (isset($this->resourceBundleLookupPathListMap[$prefix]))
				{
					$suffix = strtr($suffix, [ '/' => DIRECTORY_SEPARATOR ]);

					foreach ($this->resourceBundleLookupPathListMap[$prefix] as $i => $path)
					{
						$path .= $suffix;

						if (file_exists($path))
						{
							$this->resourcePathListMap[$id][] = $path;
						}
					}
				}
			}
		}

		return $this->resourcePathListMap[$id];
	}

	public function include(string $filePath, array $variables = null) // : mixed
	{
		return ($this->inclusion->bindTo(null, 'static'))($filePath, $variables);
	}

	public function includeAs(object $scope, string $filePath, array $variables = null) // : mixed
	{
		return ($this->inclusion->bindTo($scope, 'static'))($filePath, $variables);
	}
}
