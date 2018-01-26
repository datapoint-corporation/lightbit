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

namespace Lightbit\Data\Caching;

use \Lightbit\Data\Caching\Cache;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;

/**
 * NoCache.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class NoCache extends Cache implements IFileCache, IMemoryCache, INetworkCache
{
	/**
	 * The memory map.
	 *
	 * @var array
	 */
	private $memory;

	/**
	 * Checks if content is available.
	 *
	 * @param string $id
	 *	The content identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function contains(string $id) : bool
	{
		return isset($this->memory[$id]);
	}

	/**
	 * Reads content into a variable.
	 *
	 * @param string $id
	 *	The content identifier.
	 *
	 * @param mixed $variable
	 *	The variable to read into.
	 *
	 * @return bool
	 *	The result.
	 */
	public function read(string $id, &$variable) : bool
	{
		if (isset($this->memory[$id]))
		{
			$variable = $this->memory[$id];
			return true;
		}

		$variable = null;
		return false;
	}

	/**
	 * Writes content.
	 *
	 * @param string $id
	 *	The content identifier.
	 *
	 * @param mixed $content
	 *	The content.
	 *
	 * @return bool
	 *	The result.
	 */
	public function write(string $id, $content) : bool
	{
		$this->memory[$id] = $content;
		return true;
	}

	/**
	 * On Construct.
	 *
	 * It is invoked automatically during the component construction
	 * procedure, before applying the custom configuration.
	 */
	protected function onConstruct() : void
	{
		parent::onConstruct();

		$this->memory = [];
	}
}