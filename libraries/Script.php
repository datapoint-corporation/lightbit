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

namespace Lightbit;

use \Lightbit\IO\FileSystem\FileNotFoundException;

/**
 * Script.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Script
{
	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The script path.
	 */
	public function __construct(string $path)
	{
		$this->path = $path;
	}

	/**
	 * Includes the script.
	 *
	 * @param object $scope
	 *	The script scope object.
	 *
	 * @param array $variables
	 *	The script variables.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function include(object $context = null, array $variables = null)
	{
		if (!is_file($this->path))
		{
			throw new FileNotFoundException($this->path, sprintf('Can not include script, file not found: "%s"', $this->path));
		}

		return 
		(
			function($__FILE__, $__DATA__)
			{
				if ($__DATA__)
				{
					foreach ($__DATA__ as $__K__ => $__V__)
					{
						${$__K__} = $__V__;
					}

					unset($__K__, $__V__);
				}

				return require ($__FILE__);
			}
		)

		->bindTo($context, null)

		($this->path, $variables);
	}
}