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

namespace Lightbit\Data\Parsing;

use \Lightbit\Exception;
use \Lightbit\Data\Parsing\IParser;

/**
 * Parser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Parser implements IParser
{
	/**
	 * Gets an instance by type name.
	 *
	 * @param string $type
	 *	The type name.
	 *
	 * @return IParser
	 *	The parser.
	 */
	public static function getInstance(string $type) : IParser
	{
		static $instances = [];

		if (!isset($instances[$type]))
		{
			$instance = null;

			switch ($type)
			{
				case 'array':
					return ($instances[$type] = new ArrayParser());

				case 'bool':
				case 'boolean':
					return ($instances[$type] = new BooleanParser());

				case 'int':
				case 'integer':
					return ($instances[$type] = new IntegerParser());

				case 'double':
				case 'float':
					return ($instances[$type] = new FloatParser());

				case 'string':
					return ($instances[$type] = new StringParser());
			}

			if (!isset($instance))
			{
				if (!class_exists($type))
				{
					throw new Exception(sprintf('Can not get parser, unsupported data type: "%s"', $type));
				}

				return ($instances[$type] = new ObjectParser($type));
			}
		}

		return $instances[$type];
	}

	/**
	 * Composes a subject.
	 *
	 * @param mixed $subject.
	 *	The subject.
	 *
	 * @return string
	 *	The result.
	 */
	abstract public function compose($subject) : string;

	/**
	 * Parses a subject.
	 *
	 * @param string $subject.
	 *	The subject.
	 *
	 * @return mixed
	 *	The result.
	 */
	abstract public function parse($subject);

	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}
}