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

abstract class TestCase implements ITestCase
{
	private $closure;

	private $errorMessages;

	private $messages;

	private $warningMessages;

	private $throwables;

	private $title;

	public function __construct(string $title, Closure $closure)
	{
		$this->closure = $closure;
		$this->errorMessages = [];
		$this->messages = [];
		$this->warningMessages = [];
		$this->throwables = [];
		$this->title = $title;
	}

	public final function addErrorMessage(string $errorMessage) : void
	{
		$this->errorMessages[] = $errorMessage;
	}

	public final function addMessage(string $message) : void
	{
		$this->messages[] = $message;
	}

	public final function addWarningMessage(string $warningMessage) : void
	{
		$this->warningMessages[] = $warningMessage;
	}

	public final function addThrowable(Throwable $throwable) : void
	{
		$this->throwables[] = $throwable;
	}

	public final function getErrorMessages() : array
	{
		return $this->errorMessages;
	}

	public final function getMessages() : array
	{
		return $this->messages;
	}

	public final function getWarningMessages() : array
	{
		return $this->warningMessages;
	}

	public final function getThrowables() : array
	{
		return $this->throwables;
	}

	public final function getTitle() : string
	{
		return $this->title;
	}

	public final function getClosure() : Closure
	{
		return $this->closure;
	}

	public final function getDescription() : string
	{
		return $this->description;
	}

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
