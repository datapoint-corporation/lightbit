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

namespace Lightbit\Helpers;

/**
 * TypeHelper.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class TypeHelper
{
	/**
	 * Gets the type name of a given value.
	 *
	 * @param mixed $value
	 *	The value to get the type name of.
	 *
	 * @return string
	 *	The type name.
	 */
	public static function getNameOf($value) : string
	{
		static $aliases =
		[
			'boolean' => 'bool',
			'integer' => 'int',
			'double' => 'float'
		];

		$result = gettype($value);

		if (isset($aliases[$result]))
		{
			return $aliases[$result];
		}

		if ($result === 'object')
		{
			return get_class($value);
		}

		return $result;
	}

	/**
	 * Checks if a type name matches a basic type name.
	 *
	 * @param string $typeName
	 *	The type name to compare against.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function isBasicTypeName(string $typeName) : bool
	{
		static $baseTypeNames =
		[
			'array' => true,
			'double' => true,
			'bool' => true,
			'float' => true,
			'int' => true,
			'string' => true,
			'resource' => true
		];

		return isset($baseTypeNames[$typeName]);
	}

	/**
	 * Checks if a type name matches a scalar type name.
	 *
	 * @param string $typeName
	 *	The type name to compare against.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function isScalarTypeName(string $typeName) : bool
	{
		static $scalarTypeNames =
		[
			'bool' => true,
			'double' => true,
			'float' => true,
			'int' => true,
			'string' => true
		];

		return isset($scalarTypeNames[$typeName]);
	}

	/**
	 * Creates a string representation of the given value.
	 *
	 * @param mixed $value
	 *	The value.
	 *
	 * @return string
	 *	The result.
	 */
	public static function toString($value) : string
	{
		switch (gettype($value))
		{
			case 'string':
				return $value;

			case 'boolean':
				return $value ? 'true' : 'false';

			case 'double':
			case 'float':
			case 'integer':
				return strval($value);
		}

		return self::getNameOf($value);
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		trigger_error(sprintf('Class does not support construction: "%s"', __CLASS__), E_USER_ERROR);
		exit(1);
	}
}
