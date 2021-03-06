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

namespace Lightbit\Reflection;

use \ReflectionClass;

use \Lightbit\Reflection\IType;

/**
 * InterfaceType.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class InterfaceType implements IType
{
	/**
	 * The reflection.
	 *
	 * @var ReflectionClass
	 */
	private $reflection;

	/**
	 * Constructor.
	 *
	 * @param ReflectionClass $reflection
	 *	The interface reflection.
	 */
	public function __construct(ReflectionClass $reflection)
	{
		$this->reflection = $reflection;
	}

	/**
	 * Gets the base name.
	 *
	 * @return string
	 *	The base name.
	 */
	public final function getBaseName() : string
	{
		return $this->reflection->getShortName();
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public final function getName() : string
	{
		return $this->reflection->getName();
	}

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 *	The namespace.
	 */
	public final function getNamespace() : string
	{
		return $this->reflection->getNamespaceName();
	}

	/**
	 * Checks if it equals another type.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function equals(IType $type) : bool
	{
		return ($this->getName() === $type->getName());
	}

	/**
	 * Checks if it is a class.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isClass() : bool
	{
		return true;
	}

	/**
	 * Checks if it is an interface.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isInterface() : bool
	{
		return false;
	}

	/**
	 * Checks if it is native.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isNative() : bool
	{
		return !$this->reflection->isUserDefined();
	}

	/**
	 * Checks if it is scalar.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isScalar() : bool
	{
		return false;
	}
}
