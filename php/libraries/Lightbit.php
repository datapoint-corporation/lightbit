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

use \Lightbit\AssetManagement\AssetProvider;

/**
 * Lightbit.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class Lightbit
{
	/**
	 * The instance.
	 *
	 * @var Lightbit
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return Lightbit
	 *	The instance.
	 */
	public static function getInstance() : Lightbit
	{
		return (self::$instance ?? (self::$instance = new Lightbit()));
	}

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
	 * Constructor.
	 */
	public function __construct()
	{
		$this->libraryLookupPathList = [];

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
	 * Sets an additional library lookup path.
	 *
	 * @param string $path
	 *	The library lookup path.
	 */
	public function addLibraryLookupPath(string $path) : void
	{
		$this->libraryLookupPathList[] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets an additional library lookup path list.
	 *
	 * @param string[] $pathList
	 *	The library lookup path list.
	 */
	public function addLibraryLookupPathList(array $pathList) : void
	{
		foreach ($pathList as $i => $path)
		{
			$this->addLibraryLookupPath($path);
		}
	}

	/**
	 * Sets an additional module path.
	 *
	 * @param string $path
	 *	The module path.
	 */
	public function addModulePath(string $path) : void
	{
		$this->addLibraryLookupPath($path . '/libraries');

		AssetProvider::getInstance()->addAssetPrefixLookupPathMap([
			'hooks' => ($path . '/hooks'),
			'messages' => ($path . '/messages'),
			'settings' => ($path . '/settings'),
			'theme' => ($path . '/theme'),
			'views' => ($path . '/views')
		]);
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

	/**
	 * Include.
	 *
	 * It includes a script file, safely, without exposing the callers
	 * protected members while giving an option of declaring an explicit
	 * set of variables.
	 *
	 * @param string $filePath
	 *	The inclusion file path.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public function include(string $filePath, array $variables = null) // : mixed
	{
		return ($this->inclusion->bindTo(null, 'static'))($filePath, $variables);
	}
}
