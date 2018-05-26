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

namespace Lightbit\Testing;

use \Closure;
use \Throwable;

use \Lightbit\Testing\ITestSuite;

/**
 * TestCase.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
abstract class TestCase implements ITestCase
{
	/**
	 * The closure.
	 *
	 * @var Closure
	 */
	private $closure;

	/**
	 * The error messages.
	 *
	 * @var array
	 */
	private $errorMessages;

	/**
	 * The messages.
	 *
	 * @var array
	 */
	private $messages;

	/**
	 * The warning messages.
	 *
	 * @var array
	 */
	private $warningMessages;

	/**
	 * The throwables.
	 *
	 * @var array
	 */
	private $throwables;

	/**
	 * The title.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Constructor.
	 *
	 * @param string $title
	 *	The test case title.
	 *
	 * @param Closure $closure
	 *	The test case closure.
	 */
	public function __construct(string $title, Closure $closure)
	{
		$this->closure = $closure;
		$this->errorMessages = [];
		$this->messages = [];
		$this->warningMessages = [];
		$this->throwables = [];
		$this->title = $title;
	}

	/**
	 * Sets an additional error message.
	 *
	 * @param string $errorMessage
	 *	The error message.
	 */
	public final function addErrorMessage(string $errorMessage) : void
	{
		$this->errorMessages[] = $errorMessage;
	}

	/**
	 * Sets an additional message.
	 *
	 * @param string $message
	 *	The message.
	 */
	public final function addMessage(string $message) : void
	{
		$this->messages[] = $message;
	}

	/**
	 * Sets an additional warning message.
	 *
	 * @param string $warningMessage
	 *	The warning message.
	 */
	public final function addWarningMessage(string $warningMessage) : void
	{
		$this->warningMessages[] = $warningMessage;
	}

	/**
	 * Sets an additional throwable.
	 *
	 * @param Throwable $throwable
	 *	The throwable.
	 */
	public final function addThrowable(Throwable $throwable) : void
	{
		$this->throwables[] = $throwable;
	}

	/**
	 * Gets the error messages.
	 *
	 * @return array
	 *	The error messages.
	 */
	public final function getErrorMessages() : array
	{
		return $this->errorMessages;
	}

	/**
	 * Gets the messages.
	 *
	 * @return array
	 *	The messages.
	 */
	public final function getMessages() : array
	{
		return $this->messages;
	}

	/**
	 * Gets the warning messages.
	 *
	 * @return array
	 *	The warning messages.
	 */
	public final function getWarningMessages() : array
	{
		return $this->warningMessages;
	}

	/**
	 * Gets the throwables.
	 *
	 * @return array
	 *	The throwables.
	 */
	public final function getThrowables() : array
	{
		return $this->throwables;
	}

	/**
	 * Gets the title.
	 *
	 * @return string
	 *	The title.
	 */
	public final function getTitle() : string
	{
		return $this->title;
	}

	/**
	 * Gets the closure.
	 *
	 * @return Closure
	 *	The closure.
	 */
	public final function getClosure() : Closure
	{
		return $this->closure;
	}

	/**
	 * Gets the description.
	 *
	 * @return string
	 *	The description.
	 */
	public final function getDescription() : string
	{
		return $this->description;
	}

	/**
	 * Export.
	 *
	 * When called, it invokes the closure and exports its return value,
	 * failing if an uncaught throwable is detected.
	 *
	 * @param mixed $variable
	 *	The export variable.
	 *
	 * @return bool
	 *	The success status.
	 */
	protected final function export(&$variable) : bool
	{
		$variable = null;

		try
		{
			$variable = ($this->closure)();
		}
		catch (Throwable $e)
		{
			// $this->addErrorMessage($e->getMessage());
			$this->addThrowable($e);
			return false;
		}

		return true;
	}
}
