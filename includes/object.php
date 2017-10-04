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

use \Lightbit\Base\Object;

/**
 * Applies properties to an object.
 *
 * The properties array is expected to be an associative array indexed by
 * property name, where each key matches a public "set" method in the object.
 *
 * The properties "set" method must accept a minimum of one argument and
 * require no more than one argument.
 *
 * The properties indexed by an integer as well as magic properties (starting
 * with "@") will be ignored.
 *
 * @param object $object
 *	The object.
 *
 * @param array $properties
 *	The properties.
 */
function __object_apply($object, array $properties) : void
{
	foreach ($properties as $property => $value)
	{
		if (!$property || !is_string($property) || $property[0] == '@')
		{
			continue;
		}

		$method = 'set' . ucfirst($property);
		$object->{$method}($value);
	}
}

function __object_call($object, string $method, ...$arguments) // : mixed
{
	if ($arguments)
	{
		return $object->{$method}(...$arguments);
	}

	return $object->{$method}();
}

function __object_call_array($object, string $method, array $arguments = null) // : mixed
{
	if ($arguments)
	{
		return $object->{$method}(...$arguments);
	}

	return $object->{$method}();
}

/**
 * Constructs an object.
 *
 * @param string $class
 *	The object class name.
 *
 * @param mixed $arguments
 *	The object constructor arguments.
 *
 * @return Object
 *	The object.
 */
function __object_construct(string $class, ...$arguments) : Object
{
	return new $class(...$arguments);
}

function __object_construct_a(string $base, string $class, ...$arguments) : Object
{
	if (!__class_is_a($base, $class))
	{
		__throw('Can not object from incorrect class: class %s, must be subclass of %s', $class, $base);
	}

	return new $class(...$arguments);
}

function __object_create(array $attributes) : Object
{
	$subject = __map_get($attributes, 'string', '@class');

	$arguments = [];
	foreach ((new ReflectionClass($subject))->getConstructor()->getParameters() as $i => $parameter)
	{
		$attribute = $parameter->getName();

		if (isset($attributes[$attribute]) || array_key_exists($attributes[$attribute]))
		{
			$arguments[] = $attributes[$attribute];
			unset($attributes[$attribute]);
			continue;
		}

		if ($parameter->allowsNull())
		{
			$arguments[] = null;
			continue;
		}

		__throw('Can not create object, missing constructor attribute: attribute %s, class %s', $attribute, $subject);
	}

	return new $subject(...$arguments);
}

function __object_create_ex(?string $default, ?array $names, array $attributes) : Object
{
	$subject = $default
		? (__map_get($attributes, '?string', '@class') ?? $default)
		: (__map_get($attributes, 'string', '@class'));

	if (isset($names[$subject]))
	{
		$subject = $names[$subject];
	}

	$arguments = [];
	foreach ((new ReflectionClass($subject))->getConstructor()->getParameters() as $i => $parameter)
	{
		$attribute = $parameter->getName();

		if (isset($attributes[$attribute]) || array_key_exists($attribute, $attributes))
		{
			$arguments[] = $attributes[$attribute];
			unset($attributes[$attribute]);
			continue;
		}

		if ($parameter->allowsNull())
		{
			$arguments[] = null;
			continue;
		}

		__throw('Can not create object, missing constructor attribute: attribute %s, class %s', $attribute, $subject);
	}

	return new $subject(...$arguments);
}

function __object_create_a(string $class, array $attributes) : Object
{
	$subject = __map_get($attributes, 'string', '@class');

	if (!__class_is_a($class, $subject))
	{
		__throw('Can not object from incorrect class: class %s, must be subclass of %s', $subject, $class);
	}

	$arguments = [];
	foreach ((new ReflectionClass($subject))->getConstructor()->getParameters() as $i => $parameter)
	{
		$attribute = $parameter->getName();

		if (isset($attributes[$attribute]) || array_key_exists($attributes[$attribute]))
		{
			$arguments[] = $attributes[$attribute];
			unset($attributes[$attribute]);
			continue;
		}

		if ($parameter->allowsNull())
		{
			$arguments[] = null;
			continue;
		}

		__throw('Can not create object, missing constructor attribute: attribute %s, class %s', $attribute, $subject);
	}

	return new $subject(...$arguments);
}

function __object_create_a_ex(string $class, ?string $default, ?array $names, array $attributes) : Object
{
	$subject = $default
		? (__map_get($attributes, '?string', '@class') ?? $default)
		: (__map_get($attributes, 'string', '@class'));

	if (isset($names[$subject]))
	{
		$subject = $names[$subject];
	}

	if (!__class_is_a($class, $subject))
	{
		__throw('Can not object from incorrect class: class %s, must be subclass of %s', $subject, $class);
	}

	$arguments = [];
	foreach ((new ReflectionClass($subject))->getConstructor()->getParameters() as $i => $parameter)
	{
		$attribute = $parameter->getName();

		if (isset($attributes[$attribute]) || array_key_exists($attributes[$attribute]))
		{
			$arguments[] = $attributes[$attribute];
			unset($attributes[$attribute]);
			continue;
		}

		if ($parameter->allowsNull())
		{
			$arguments[] = null;
			continue;
		}

		__throw('Can not create object, missing constructor attribute: attribute %s, class %s', $attribute, $subject);
	}

	return new $subject(...$arguments);
}

function __object_attribute_get($object, string $property) // : mixed
{
	return $object->{$property};
}

function __object_attribute_get_array($object, array $properties) : array
{
	$result = [];

	foreach ($properties as $i => $property)
	{
		$result[$property] = $object->{$property};
	}

	return $result;
}

function __object_attribute_is_set($object, string $property) : bool
{
	return isset($object->{$property});
}

function __object_attribute_set($object, string $property, $attribute) : void
{
	$object->{$property} = $attribute;
}

function __object_attribute_set_array($object, array $attributes) : void
{
	foreach ($attributes as $property => $attribute)
	{
		$object->{$property} = $attribute;
	}
}
