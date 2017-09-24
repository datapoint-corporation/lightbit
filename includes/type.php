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

/**
 * Checks if a type matches a class or interface.
 *
 * @param string $type
 *	The type name.
 *
 * @return bool
 *	The type.
 */
function __type_is_object(string $type) : bool
{
	return class_exists($type) || interface_exists($type);
}

/**
 * Checks a type against another.
 *
 * @param string $subject
 *	The type to compare.
 *
 * @param string $candidate
 *	The type to compare against.
 *
 * @return bool
 *	The result.
 */
function __type_is(string $subject, string $candidate) : bool
{
	$nullable = ($subject[0] === '?');

	if ($candidate === 'NULL')
	{
		return $nullable;
	}

	if ($candidate[0] === '?')
	{
		$candidate = substr($candidate, 1);
	}

	if ($subject === $candidate)
	{
		return true;
	}

	if ($nullable)
	{
		$subject = substr($subject, 1);
	}

	if (__type_is_scalar($subject))
	{
		return ($subject === $candidate);
	}

	return is_subclass_of($candidate, $subject);
}

function __type_is_basic(string $type) : bool
{
	if ($type[0] === '?')
	{
		$type = substr($type, 1);
	}

	switch ($type)
	{
		case 'array':
		case 'bool':
		case 'boolean':
		case 'double':
		case 'float':
		case 'int':
		case 'integer':
		case 'resource':
		case 'string':
			return true;
	}

	return false;
}

/**
 * Checks if a type is scalar.
 *
 * @param string $type
 *	The type name.
 *
 * @return bool
 *	The result.
 */
function __type_is_scalar(string $type) : bool
{
	if ($type[0] === '?')
	{
		$type = substr($type, 1);
	}

	switch ($type)
	{
		case 'bool':
		case 'boolean':
		case 'double':
		case 'float':
		case 'int':
		case 'integer':
		case 'string':
			return true;
	}

	return false;
}

/**
 * Checks if a type is nullable.
 *
 * @param string $type
 *	The type name.
 *
 * @return bool
 *	The result.
 */
function __type_is_nullable(string $type) : bool
{
	return $type[0] === '?';
}

/**
 * Checks a type against a variable.
 *
 * @param string $type
 *	The type name.
 *
 * @param mixed $variable
 *	The variable value.
 *
 * @return bool
 *	The result.
 */
function __type_match(string $type, $variable) : bool
{
	if (isset($variable))
	{
		if ($type[0] === '?')
		{
			$type = substr($type, 1);
		}

		if ($type === __type_of($variable))
		{
			return true;
		}

		if (is_object($variable))
		{
			return is_subclass_of($variable, $type);
		}

		return false;
	}

	return ($type[0] === '?');
}

/**
 * Checks the type of a variable.
 *
 * @param mixed $variable
 *	The variable value.
 *
 *
 * @return string
 *	The variable value type.
 */
function __type_of($variable) : string
{
	switch ($t = gettype($variable))
	{
		case 'array':
		case 'string':
		case 'resource':
			return $t;

		case 'boolean':
			return 'bool';

		case 'double':
			return 'float';

		case 'integer':
			return 'int';

		case 'NULL':
			return 'null';

		case 'object':
			return get_class($variable);
	}

	return 'undefined';
}

/**
 * Stringifies a variable.
 *
 * @param mixed $variable
 *	The variable.
 *
 * @return string
 *	The string.
 */
function __type_to_string($variable) : string
{
	switch (gettype($variable))
	{
		case 'array':
			return __json_encode($variable);

		case 'string':
			return $variable;

		case 'resource':
			return 'resource';

		case 'boolean':
			return ($variable ? 'true' : 'false');

		case 'double':
			return __number_format($variable, null, '.', '');

		case 'integer':
			return __number_format($variable, 0);

		case 'NULL':
			return '';

		case 'object':
			return get_class($variable);
	}

	return __type_of($variable);
}
