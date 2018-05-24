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

/**
 * ParserProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class ParserProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var ParserProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return ParserProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : ParserProvider
	{
		return (self::$instance ?? (self::$instance = new ParserProvider()));
	}

	/**
	 * The parser factory.
	 *
	 * @var IParserFactory
	 */
	private $parserFactory;

	/**
	 * The parsers.
	 *
	 * @var array
	 */
	private $parsers;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->parsers = [];
	}

	/**
	 * Gets a parser.
	 *
	 * @param string $type
	 *	The parser type.
	 *
	 * @return IParser
	 *	The parser.
	 */
	public final function getParser(string $type) : IParser
	{
		return ($this->parsers[$type] ?? ($this->parsers[$type] = $this->getParserFactory()->createParser($type)));
	}

	/**
	 * Gets the parser factory.
	 *
	 * @return IParserFactory
	 *	The parser factory.
	 */
	public final function getParserFactory() : IParserFactory
	{
		return ($this->parserFactory ?? ($this->parserFactory = new ParserFactory()));
	}
}
