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

namespace Lightbit\Data;

use \Lightbit\Base\Component;
use \Lightbit\Base\Object;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Data\NoCache;

/**
 * SlugManager.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class SlugManager extends Component implements ISlugManager
{
	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $id, array $configuration = null)
	{
		parent::__construct($id, $configuration);
	}

	private function compile(Object $object) : array
	{
		$className = get_class($object);
		$content = base64_encode(serialize($object));
		$hash = hash('sha1', (static::class . '/' . $this->getApplication()->getPath() . '/'  . $className . '/' . $content));

		return [ $hash, $className, $content ];
	}
	
	/**
	 * Composes a slug.
	 *
	 * @param Object $object
	 *	The object to compose the slug of.
	 *
	 * @return string
	 *	The object slug.
	 */
	public function compose(Object $object) : string
	{
		$storage = $this->getMemoryCache();

		if ($storage instanceof NoCache)
		{
			$storage = $this->getHttpSession();
		}

		list($hash, $className, $content) = $this->compile($object);

		$key = 'lightbit://data/slug-manager/' . $hash;

		if (!$storage->contains($key))
		{
			$storage->write($key, [ $className, $content ]);
		}

		return $hash;
	}

	/**
	 * Parses a slug.
	 *
	 * @param string $className
	 *	The object class name.
	 *
	 * @param string $slug
	 *	The object slug.
	 *
	 * @return Object
	 *	The object.
	 */
	public function parse(string $className, string $slug) : ?Object
	{
		$storage = $this->getMemoryCache();

		if ($storage instanceof NoCache)
		{
			$storage = $this->getHttpSession();
		}

		$key = 'lightbit://data/slug-manager/' . $slug;

		if ($storage->contains($key))
		{
			list($className1, $content) = $storage->read($key);

			if ($className1 == $className)
			{
				return unserialize(base64_decode($content));
			}
		}

		return null;
	}
}