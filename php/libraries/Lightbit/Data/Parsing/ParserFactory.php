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

use \Lightbit\Data\Parsing\IParser;
use \Lightbit\Data\Parsing\ParserFactoryException;

/**
 * ParserFactory.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class ParserFactory implements IParserFactory
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Creates a parser.
	 *
	 * @throws ParserFactoryException
	 *	Thrown if a parser does not exist for the given type and fails to be
	 *	dynamically created, if even possible.
	 *
	 * @param string $type
	 *	The parser type.
	 *
	 * @return IParser
	 *	The parser.
	 */
	public function createParser(string $type) : IParser
	{
		switch ($type)
		{
			case 'bool':
			case 'boolean':
				return new BooleanParser();

			case 'double':
			case 'float':
				return new FloatParser();

			case 'int':
			case 'integer':
				return new IntegerParser();

			case 'string':
				return new StringParser();
		}

		throw new ParserFactoryException($this, sprintf('Can not create type parser, out of options: "%s"', $type));
	}
}
