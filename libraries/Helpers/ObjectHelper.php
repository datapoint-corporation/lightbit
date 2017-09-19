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

use \Lightbit\Base\Object;
use \Lightbit\Exception;

/**
 * ObjectHelper.
 *
 * Provides static utility methods for the most common procedures based
 * on objects.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class ObjectHelper
{
	/**
	 * Calls a function.
	 *
	 * @param Object $object
	 *	The object to call the function on.
	 *
	 * @param string $function
	 *	The function name.
	 *
	 * @param array $arguments
	 *	The function arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public static function call(Object $object, string $function, array $arguments = null) // : mixed
	{
		if ($arguments)
		{
			return $object->{$function}(...$arguments);
		}

		return $object->{$function}();
	}

	/**
	 * Configures an object.
	 *
	 * @param Object $object
	 *	The object to configure.
	 *
	 * @param array $configuration
	 *	The object configuration.
	 */
	public static function configure(Object $object, array $configuration) : void
	{
		$class = new \ReflectionClass($object);

		foreach ($configuration as $property => $value)
		{
			if (!$property || !is_string($property) || $property[0] == '@')
			{
				continue;
			}

			// Basically, we're going to exploit the fact that PHP methods
			// can be accessed in a case insensitive manner and use it
			// to boost our performance, while keeping support for
			// dashes and underscores.
			$subject = strtr($property, [ '-' => '', '_' => '' ]);
			$setterName = 'set' . $subject;

			if ($class->hasMethod($setterName))
			{
				$setter = $class->getMethod($setterName);

				if ($setter->isPublic()
					&& !$setter->isStatic()
					&& $setter->getNumberOfParameters() > 0
					&& $setter->getNumberOfRequiredParameters() < 2)
				{
					$setter->invoke($object, $value);
					continue;
				}
			}

			throw new Exception(sprintf('Object property setter is not defined: "%s" ("%s")', $property, $class->getName()));
		}
	}

	/**
	 * Gets an object attribute.
	 *
	 * @param Object $object
	 *	The object.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @return mixed
	 *	The attribute.
	 */
	public static function getAttribute(Object $object, string $property) // : mixed
	{
		return $object->{$property};
	}

	/**
	 * Gets an object attributes.
	 *
	 * @param Object $object
	 *	The object.
	 *
	 * @param array $properties
	 *	The properties name.
	 *
	 * @return array
	 *	The attributes.
	 */
	public static function getAttributes(Object $object, array $properties = null) : array
	{
		$result = [];

		if (!$properties)
		{
			foreach ((new \ReflectionClass($object))->getProperties() as $i => $property)
			{
				if ($property->isPublic() && !$property->isStatic())
				{
					$result[$property->getName()] = $object->{$property->getName()};
				}
			}

			return $result;
		}

		foreach ($properties as $i => $property)
		{
			$result[$property] = $object->{$property};
		}

		return $result;
	}

	/**
	 * Checks if an object attribute is set.
	 *
	 * @param Object $object
	 *	The object.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @return bool
	 *	The result.
	 */
	public static function hasAttribute(Object $object, string $property) : bool
	{
		return isset($object->{$property});
	}

	/**
	 * Sets an object attribute.
	 *
	 * @param Object $object
	 *	The object.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param mixed $attribute
	 *	The value.
	 */
	public static function setAttribute(Object $object, string $property, $value) : void
	{
		$object->{$property} = $value;
	}

	/**
	 * Sets an object attributes.
	 *
	 * @param Object $object
	 *	The object.
	 *
	 * @param array $attributes
	 *	The attributes.
	 */
	public static function setAttributes(Object $object, array $attributes) : void
	{
		foreach ($attributes as $property => $value)
		{
			$object->{$property} = $value;
		}
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
