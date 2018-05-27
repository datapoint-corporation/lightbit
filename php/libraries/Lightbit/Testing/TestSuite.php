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

use \Lightbit;
use \Lightbit\Testing\ITestSuite;

class TestSuite implements ITestSuite
{
	private $filePath;

	private $fileScope;

	private $success;

	private $suites;

	private $title;

	public function __construct(string $filePath)
	{
		$this->filePath = $filePath;
		$this->fileScope = new TestSuiteScope($this);
		$this->suites = [];
	}

	public function addCase(ITestCase $case) : void
	{
		$this->cases[] = $case;
	}

	public function addCases(array $cases) : void
	{
		foreach ($cases as $i => $case)
		{
			$this->addCase($case);
		}
	}

	public function addSuite(ITestSuite $suite) : void
	{
		$this->suites[] = $suite;
	}

	public function getCases() : array
	{
		if (!isset($this->cases))
		{
			$this->cases = [];
			Lightbit::getInstance()->includeAs($this->fileScope, $this->filePath);
		}

		return $this->cases;
	}

	public function getSuites() : array
	{
		return $this->suites;
	}

	public function getTitle() : string
	{
		return ($this->title ?? ($this->title = $this->filePath));
	}

	public function setTitle(string $title) : void
	{
		$this->title = $title;
	}

	public function validate() : bool
	{
		if (!isset($this->success))
		{
			$this->success = true;

			foreach ($this->getCases() as $i => $case)
			{
				if (!$case->validate())
				{
					$this->success = false;
				}
			}
		}

		return $this->success;
	}

	private function toStringEx(ITestSuite $suite, int $index = 0) : string
	{
		$suite->validate();

		++$index;

		$content = ($index) . '.' . PHP_EOL;
		$content .= $suite->getTitle() . PHP_EOL;
		$content .= '================================' . PHP_EOL;
		$content .= PHP_EOL;

		$tcCount = 0;
		$tcCountSuccess = 0;
		foreach ($suite->getCases() as $i => $case)
		{
			$success = $case->validate() && (++$tcCountSuccess);

			$content .= ($index) . '.' . (++$tcCount) . '.' . PHP_EOL;
			$content .= $case->getTitle() . PHP_EOL;
			$content .= '--------------------------------' . PHP_EOL;
			$content .= ($success ? 'SUCCESS' : 'FAILURE') . PHP_EOL;
			$content .= PHP_EOL;

			foreach ($case->getErrorMessages() as $i => $errorMessage)
			{
				$content .= 'WARNING' . PHP_EOL;
				$content .= $errorMessage . PHP_EOL;
				$content .= PHP_EOL;
			}

			foreach ($case->getWarningMessages() as $i => $warningMessage)
			{
				$content .= 'WARNING' . PHP_EOL;
				$content .= $warningMessage . PHP_EOL;
				$content .= PHP_EOL;
			}

			foreach ($case->getMessages() as $i => $message)
			{
				$content .= 'VERBOSE' . PHP_EOL;
				$content .= $message . PHP_EOL;
				$content .= PHP_EOL;
			}

			foreach ($case->getThrowables() as $i => $throwable)
			{
				do
				{
					$content .= 'THROWABLE' . PHP_EOL;
					$content .= $throwable->getMessage() . PHP_EOL;
					$content .= PHP_EOL;
					$content .= get_class($throwable) . PHP_EOL;
					$content .= $throwable->getFile() . '(' . $throwable->getLine() . ')' . PHP_EOL;
					$content .= $throwable->getTraceAsString() . PHP_EOL;
					$content .= PHP_EOL;
				}
				while ($throwable = $throwable->getPrevious());
			}
		}

		$content .= '================================' . PHP_EOL;
		$content .= $tcCountSuccess . ' out of ' . $tcCount . PHP_EOL;

		foreach ($suite->getSuites() as $i => $suite)
		{
			$content .= PHP_EOL;
			$content .= $this->toStringEx($suite, $index++);
		}

		return $content;
	}

	public function toString() : string
	{
		return $this->toStringEx($this, 0);
	}
}
