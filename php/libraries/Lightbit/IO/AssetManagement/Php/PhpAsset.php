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

namespace Lightbit\IO\AssetManagement\Php;

use \Lightbit;
use \Lightbit\IO\AssetManagement\Asset;
use \Lightbit\IO\AssetManagement\Php\IPhpAsset;
use \Lightbit\IO\FileNotFoundException;

/**
 * PhpAsset.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class PhpAsset extends Asset implements IPhpAsset
{
	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The asset identifier.
	 *
	 * @param string $path
	 *	The asset path.
	 */
	public function __construct(string $id, string $path)
	{
		parent::__construct($id, $path);
	}

	/**
	 * Checks if the file exists.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function exists() : bool
	{
		return file_exists($this->getPath()) && is_file($this->getPath());
	}

	/**
	 * Includes the script.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public final function include(array $variables = null)
	{
		$filePath = $this->getPath();

		if (file_exists($filePath) && is_file($filePath))
		{
			return Lightbit::getInstance()->include($filePath, $variables);
		}

		throw new FileNotFoundException($filePath);
	}

	/**
	 * Includes the script as an object.
	 *
	 * @param object $object
	 *	The inclusion object.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public final function includeAs(object $object, array $variables = null)
	{
		$filePath = $this->getPath();

		if (file_exists($filePath) && is_file($filePath))
		{
			return Lightbit::getInstance()->includeAs($object, $filePath, $variables);
		}

		throw new FileNotFoundException($filePath);
	}
}
