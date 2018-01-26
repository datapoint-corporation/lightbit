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

namespace Lightbit\IO\FileSystem;

use \Lightbit;
use \Lightbit\Base\IContext;
use \Lightbit\IllegalArgumentException;

/**
 * Asset.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Asset
{
	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The prefix.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructor.
	 *
	 * @param string $base
	 *	The asset base path.
	 *
	 * @param string $id
	 *	The asset identifier.
	 *
	 * @param string $extension
	 *	The asset extension.
	 */
	public function __construct(?string $base, string $id, ?string $extension = 'php')
	{
		$this->id = $id;

		$tokens;
		if (!preg_match('%^((([^:]+):\\/\\/)|((@|\\/)\\/))?([\\w\\-]+(\\/[\\w\\-]+)*)$%', $id, $tokens))
		{
			throw new IllegalArgumentException(sprintf('Can not get asset, illegal identifier format: "%s"', $id));
		}

		if ($tokens[3])
		{
			$this->prefix = $tokens[3];
		}

		else if ($tokens[5])
		{
			switch ($this->prefix = $tokens[5])
			{
				case '/':
					$this->path = Lightbit::getInstance()->getApplication()->getPath();
					break;

				case '@':
					$this->path = Lightbit::getInstance()->getContext()->getPath();
					break;
			}
		}

		else if ($base)
		{
			$this->path = $base;
		}

		else
		{
			throw new IllegalArgumentException(sprintf('Can not get asset, illegal identifier format: "%s"', $id));
		}

		$this->path .= DIRECTORY_SEPARATOR . strtr($tokens[6], [ '/' => DIRECTORY_SEPARATOR ]);

		if ($extension)
		{
			$this->path .= '.' . $extension;
		}
	}

	/**
	 * Checks if the asset exists.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function exists() : bool
	{
		return file_exists($this->path);
	}

	/**
	 * Checks if the asset has a prefix.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isAbsolute() : bool
	{
		return isset($this->prefix);
	}

	/**
	 * Checks if the asset is a directory.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDirectory() : bool
	{
		return is_dir($this->path);
	}

	/**
	 * Checks if the asset is a file.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isFile() : bool
	{
		return is_file($this->path);
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return $this->path;
	}
}