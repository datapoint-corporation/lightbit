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

use \Lightbit\Base\Component;

use \Lightbit\Base\IContext;
use \Lightbit\Data\Caching\ICache;

/**
 * CacheBase.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class CacheBase extends Component implements ICache
{
	/**
	 * Deletes a attribute.
	 *
	 * @param string $property
	 *	The property.
	 */
	abstract public function delete(string $property) : void;

	/**
	 * Extracts a attribute.
	 *
	 * @param string $type
	 *	The property data type (e.g.: '?string').
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	abstract public function extract(?string $type, string $property); // : mixed

	/**
	 * Gets a attribute.
	 *
	 * @param string $type
	 *	The property data type (e.g.: '?string').
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	abstract public function get(?string $type, string $property); // : mixed

	/**
	 * Checks if a attribute is set.
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @return bool
	 *	The result.
	 */
	abstract public function has(string $property) : bool;

	/**
	 * Sets a attribute.
	 *
	 * @param string $property
	 *	The property.
	 *
	 * @param mixed $attribute
	 *	The attribute.
	 */
	abstract public function set(string $property, $attribute) : void;
}