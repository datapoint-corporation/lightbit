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

namespace Lightbit;

use \Throwable;

use \Lightbit\Exception;

/**
 * ErrorException.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class ErrorException extends Exception
{
	/**
	 * The level string map.
	 *
	 * @var array
	 */
	private static $levelStringMap = [
		E_ERROR => 'E_ERROR',
		E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE',
		E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR => 'E_USER_ERROR',
		E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE',
		E_STRICT => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED => 'E_DEPRECATED',
		E_USER_DEPRECATED => 'E_USER_DEPRECATED',
		E_ALL => 'E_ALL'
	];

	/**
	 * The file path.
	 *
	 * @var string
	 */
	private $filePath;

	/**
	 * The level.
	 *
	 * @var int
	 */
	private $level;

	/**
	 * The line number.
	 *
	 * @var int
	 */
	private $lineNumber;

	/**
	 * Constructor.
	 *
	 * @param int $level
	 *	The exception level.
	 *
	 * @param string $filePath
	 *	The exception file path.
	 *
	 * @param int $lineNumber
	 *	The exception line number.
	 *
	 * @param string $message
	 *	The exception message.
	 *
	 * @param Throwable $previous
	 *	The exception previous throwable.
	 */
	public function __construct(int $level, ?string $filePath, ?int $lineNumber, string $message, Throwable $previous = null)
	{
		parent::__construct($message, $previous);

		$this->filePath = $filePath;
		$this->level = $level;
		$this->lineNumber = $lineNumber;
	}

	/**
	 * Gets the file path.
	 *
	 * @return string
	 *	The file path.
	 */
	public final function getFilePath() : ?string
	{
		return $this->filePath;
	}

	/**
	 * Gets the level.
	 *
	 * @return int
	 *	The level.
	 */
	public final function getLevel() : int
	{
		return $this->level;
	}

	/**
	 * Gets the level as a string.
	 *
	 * @return string
	 *	The level as a string.
	 */
	public final function getLevelAsString() : string
	{
		return (self::$levelStringMap[$this->level] ?? 'E_UNKNOWN');
	}

	/**
	 * Gets the line number.
	 *
	 * @return int
	 *	The line number.
	 */
	public final function getLineNumber() : ?int
	{
		return $this->lineNumber;
	}
}
