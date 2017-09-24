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

namespace Lightbit\Base;

use \Lightbit;
use \Lightbit\Exception;

/**
 * Object.
 *
 * This is the base class for all Lightbit derived objects, implementing the
 * most common procedures and doubling as a type hint filter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Object
{
	/**
	 * Configures the object.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public final function configure(array $configuration) : void
	{
		__object_apply($this, $configuration);
	}

	/**
	 * Sets an event subscription.
	 *
	 * @param string $id
	 *	The event identifier.
	 *
	 * @param array $callable
	 *	The event subscription callable.
	 */
	public final function on(string $id, array $callable) : void
	{
		__event_subscribe($id, $callable);
	}

	/**
	 * Creates a string representation of this object.
	 *
	 * @return string
	 *	The string representation of this object.
	 */
	public function toString() : string
	{
		return static::class;
	}

	/**
	 * Raises an event.
	 *
	 * @param string $id
	 *	The event identifier.
	 *
	 * @param mixed $arguments
	 *	The event arguments.
	 *
	 * @return array
	 *	The event results.
	 */
	public final function raise(string $id, ...$arguments) : array
	{
		return __event_raise($id, ...$arguments);
	}

	/**
	 * Calls a method.
	 *
	 * @param string $method
	 *	The method name.
	 *
	 * @param array $arguments
	 *	The method arguments.
	 *
	 * @return mixed
	 *	The method result.
	 */
	public function __call(string $method, array $arguments) // : mixed
	{
		throw new Exception(sprintf('Method does not exist: "%s", class "%s"', $method, static::class));
	}

	/**
	 * Gets a property value.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @return mixed
	 *	The property value.
	 */
	public function __get(string $property) // : mixed
	{
		throw new Exception(sprintf('Property does not exist: "%s", class "%s"', $property, static::class));
	}

	/**
	 * Checks if a property value is set.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function __isset(string $property) : bool
	{
		throw new Exception(sprintf('Property does not exist: "%s", class "%s"', $property, static::class));
	}

	/**
	 * Sets a property value.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param mixed $value
	 *	The property value.
	 */
	public function __set(string $property, $mixed) : void
	{
		throw new Exception(sprintf('Property does not exist: "%s", class "%s"', $property, static::class));
	}

	/**
	 * Unsets a property value.
	 *
	 * @param string $property
	 *	The property name.
	 */
	public function __unset(string $property) : void
	{
		throw new Exception(sprintf('Property does not exist: "%s", class "%s"', $property, static::class));
	}

	/**
	 * Creates a string representation of this object.
	 *
	 * @return string
	 *	The string representation of this object.
	 */
	public final function __toString() : string
	{
		return $this->toString();
	}
}
