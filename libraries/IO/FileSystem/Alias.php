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

namespace Lightbit\IO\FileSystem;

use \Lightbit;
use \Lightbit\Base\Object;

/**
 * Alias.
 *
 * This class provides a basic implementation for a file system alias, which
 * is meant to be resolved to an absolute file or directory path.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Alias extends Object
{
	/**
	 * The file system alias.
	 *
	 * @type string
	 */
	private $alias;

	/**
	 * The file system prefix.
	 *
	 * @type string
	 */
	private $prefix;

	/**
	 * The file system prefix path.
	 *
	 * @type string
	 */
	private $prefixPath;

	/**
	 * The file system alias subject.
	 *
	 * @type string
	 */
	private $subject;

	/**
	 * Construct.
	 *
	 * @param string $alias
	 *	The file system alias.
	 */
	public function __construct(string $alias)
	{
		$this->alias = $alias;

		$parts = explode('://', $alias, 2);

		if (isset($parts[1]))
		{
			$this->prefix = $parts[0];
			$this->prefixPath = Lightbit::getPrefixPath($this->prefix);
			$this->subject = strtr($parts[1], '/', DIRECTORY_SEPARATOR);
		}
		else
		{
			$this->prefix = null;
			$this->prefixPath = null;
			$this->subject = strtr($parts[0], '/', DIRECTORY_SEPARATOR);
		}
	}

	/**
	 * Resolves to a path by looking through different context base paths,
	 * returning the first match or, if not found, the last resolution.
	 *
	 * @param string $extension
	 *	The file system path extension.
	 *
	 * @param array $contexts
	 *	The file system contexts base path.
	 *
	 * @return string
	 *	The result.
	 */
	public function lookup(string $extension = null, array $contexts = null) : string
	{
		$result = $this->subject;

		if ($extension)
		{
			$result .= '.' . $extension;
		}

		if ($this->prefix)
		{
			return $this->prefixPath . DIRECTORY_SEPARATOR . $result;
		}

		if ($contexts)
		{
			$candidate;

			foreach ($contexts as $i => $context)
			{
				$candidate = $context . DIRECTORY_SEPARATOR . $result;

				if (file_exists($candidate))
				{
					break;
				}
			}

			return $candidate;
		}

		return LIGHTBIT_PATH_PRIVATE . DIRECTORY_SEPARATOR . $result;
	}

	/**
	 * Resolves to a path.
	 *
	 * @param string $extension
	 *	The file system path extension.
	 *
	 * @param string $context
	 *	The file system context base path.
	 *
	 * @return string
	 *	The result.
	 */
	public function resolve(string $extension = null, string $context = null) : string
	{
		$result = $this->subject;

		if ($extension)
		{
			$result .= '.' . $extension;
		}

		if ($this->prefix)
		{
			return $this->prefixPath . DIRECTORY_SEPARATOR . $result;
		}

		if ($context)
		{
			return $context . DIRECTORY_SEPARATOR . $result;
		}

		return LIGHTBIT_PATH_PRIVATE . DIRECTORY_SEPARATOR . $result;
	}
}
