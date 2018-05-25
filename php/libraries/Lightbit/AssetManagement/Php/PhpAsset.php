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

namespace Lightbit\AssetManagement\Php;

use \Lightbit;
use \Lightbit\AssetManagement\Asset;

/**
 * PhpAsset.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class PhpAsset extends Asset
{
	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The path.
	 */
	public function __construct(string $path)
	{
		parent::__construct($path);
	}

	/**
	 * Include.
	 *
	 * It includes the script file, safely, without exposing the callers
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
	public final function include(array $variables = null) // : mixed
	{
		return Lightbit::getInstance()->include($this->getPath(), $variables);
	}

	/**
	 * Include.
	 *
	 * It includes the script file, safely, without exposing the callers
	 * protected members while giving an option of declaring an explicit
	 * public scope object and a set of variables.
	 *
	 * @param object $scope
	 *	The inclusion public scope object.
	 *
	 * @param array $variables
	 *	The inclusion variables.
	 *
	 * @return mixed
	 *	The inclusion result.
	 */
	public final function includeAs(object $object, array $variables = null) // : mixed
	{
		return Lightbit::getInstance()->includeAs($object, $this->getPath(), $variables);
	}
}
