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

namespace Lightbit\Data\Traversing;

use \Lightbit\Data\Traversing\IIterator;

/**
 * ArrayIterator.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class ArrayIterator implements IIterator
{
	/**
	 * The values.
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Constructor.
	 *
	 * @param array $values
	 *	The iterator values.
	 */
	public function __construct(array $values)
	{
		$this->values = $values;
	}

	/**
	 * Gets the value set for the current internal index.
	 *
	 * @return mixed
	 *	The key value for the current internal index.
	 */
	public final function current()
	{
		return current($this->values);
	}

	/**
	 * Gets a iterator based on matching elements.
	 *
	 * @param array $criteria
	 *	The match criteria.
	 *
	 * @return IIterator
	 *	The result.
	 */
	public final function with(array $criteria) : IIterator
	{
		$result = [];

		foreach ($this->values as $i => $candidate)
		{
			if (is_array($candidate))
			{
				$success = true;

				foreach ($criteria as $key => $subject)
				{
					if (!(isset($candidate[$key]) && $candidate[$key] === $subject))
					{
						$success = false;
						break;
					}
				}

				if ($success)
				{
					$result[$i] = $candidate;
				}
			}
		}

		return new ArrayIterator($result);
	}

	/**
	 * Gets the key set for the current internal index.
	 *
	 * @return mixed
	 *	The key set for the current internal index.
	 */
	public final function key()
	{
		return key($this->values);
	}

	/**
	 * Sets the internal index to the next position.
	 */
	public final function next() : void
	{
		next($this->values);
	}

	/**
	 * Sets the internal index to the first position.
	 */
	public final function rewind() : void
	{
		reset($this->values);
	}

	/**
	 * Checks if the internal index is valid.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function valid() : bool
	{
		return (key($this->values) !== null);
	}
}