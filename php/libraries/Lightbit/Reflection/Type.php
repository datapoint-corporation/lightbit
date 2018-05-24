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

use \Lightbit\Reflection\IType;

/**
 * Type.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class Type implements IType
{
	/**
	 * The types.
	 *
	 * @var array
	 */
	private static $types = [];

	/**
	 * Gets a type.
	 *
	 * @param mixed $variable
	 *	The variable to get the type from.
	 *
	 * @return IType
	 *	The variable type.
	 */
	public static final function getInstanceOf($variable) : IType
	{
		if (isset($variable))
		{
			$name = gettype($variable);

			if ($name === 'object')
			{
				return new Type(get_class($variable));
			}

			return new Type((($name === 'object') ? (get_class($variable)) : ($name)));
		}

		return new Type('null');
	}

	/**
	 * Constructor.
	 *
	 * @param string $name
	 *	The type name.
	 */
	public function __construct(string $name)
	{
		if (!isset(self::$types[$name]))
		{
			switch (true)
			{
				case ($name === 'bool'):
				case ($name === 'boolean'):
					self::$types[$name] = new BooleanType();
					break;

				case ($name === 'int'):
				case ($name === 'integer'):
					self::$types[$name] = new IntegerType();
					break;

				case ($name === 'double'):
				case ($name === 'float'):
					self::$types[$name] = new FloatType();
					break;

				case ($name === 'array'):
					self::$types[$name] = new ArrayType();
					break;

				case (class_exists($name)):
					self::$types[$name] = new ClassType($name);
					break;

				case (interface_exists($name)):
					self::$types[$name] = new InterfaceType($name);
					break;

				default:
					self::$types[$name] = new UnknownType($name);
					break;
			}
		}

		$this->type = self::$types[$name];
	}

	/**
	 * Gets the base name.
	 *
	 * @return string
	 *	The base name.
	 */
	public function getBaseName() : string
	{
		return $this->type->getBaseName();
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public function getName() : string
	{
		return $this->type->getName();
	}

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 *	The namespace.
	 */
	public function getNamespace() : string
	{
		return $this->type->getNamespace();
	}

	/**
	 * Checks if it equals another type.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function equals(IType $type) : bool
	{
		return $this->type->equals($type);
	}

	/**
	 * Checks if it is a class.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClass() : bool
	{
		return $this->type->isClass();
	}

	/**
	 * Checks if it is an interface.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isInterface() : bool
	{
		return $this->type->isInterface();
	}

	/**
	 * Checks if it is native.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isNative() : bool
	{
		return $this->type->isNative();
	}

	/**
	 * Checks if it is scalar.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isScalar() : bool
	{
		return $this->type->isScalar();
	}

	public function __toString() : string
	{
		return $this->type->getName();
	}
}
