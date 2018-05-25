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

use \Lightbit\AssetManagement\Php\PhpAsset;
use \Lightbit\Testing\ITestSuite;

/**
 * TestSuite.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class TestSuite implements ITestSuite
{
	/**
	 * The asset.
	 *
	 * @var PhpAsset
	 */
	private $asset;

	/**
	 * The success flag.
	 *
	 * @var bool
	 */
	private $success;

	/**
	 * Constructor.
	 *
	 * @param PhpAsset $asset
	 *	The test suite asset.
	 */
	public function __construct(PhpAsset $asset)
	{
		$this->asset = $asset;
	}

	/**
	 * Sets an additional case.
	 *
	 * @param ITestCase $case
	 *	The case.
	 */
	public function addCase(ITestCase $case) : void
	{
		$this->cases[] = $case;
	}

	/**
	 * Sets an additional set of cases.
	 *
	 * @param array $cases
	 *	The cases.
	 */
	public function addCases(array $cases) : void
	{
		foreach ($cases as $i => $case)
		{
			$this->addCase($case);
		}
	}

	/**
	 * Gets the cases.
	 *
	 * @return array
	 *	The cases.
	 */
	public function getCases() : array
	{
		if (!isset($this->cases))
		{
			$this->cases = [];
			$this->asset->includeAs(new TestSuiteScope($this));
		}

		return $this->cases;
	}

	/**
	 * Validate.
	 *
	 * When invoked, it iterates through the test suite cases performing
	 * validation on each one, failing if any of them fails.
	 *
	 * @return bool
	 *	The success status.
	 */
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

	/**
	 * Creates a string representation of this test suite.
	 *
	 * @return string
	 *	The string representation of this test suite.
	 */
	public function toString() : string
	{
		if (!isset($this->success))
		{
			$this->validate();
		}

		$id = 1;
		$content = '';
		$count = 0;
		$countSuccess = 0;

		foreach ($this->getCases() as $i => $case)
		{
			$content .= 'TS.' . (++$i) . PHP_EOL;
			$content .= $case->getDescription() . PHP_EOL;
			$content .= '--------------------------------' . PHP_EOL;

			++$count;

			if ($case->validate())
			{
				$content .= 'SUCCESS' . PHP_EOL . PHP_EOL;
				++$countSuccess;
			}
			else
			{
				$content .= 'FAILURE' . PHP_EOL . PHP_EOL;
			}

			foreach ($case->getMessages() as $i => $message)
			{
				$content .= 'VERBOSE (' . $i . ')' . PHP_EOL . $message . PHP_EOL . PHP_EOL;
			}

			foreach ($case->getWarningMessages() as $i => $message)
			{
				$content .= 'WARNING (' . $i . ')' . PHP_EOL . $message . PHP_EOL . PHP_EOL;
			}

			foreach ($case->getErrorMessages() as $i => $message)
			{
				$content .= 'ERROR (' . $i . ')' . PHP_EOL . $message . PHP_EOL . PHP_EOL;
			}

			foreach ($case->getThrowables() as $i => $throwable)
			{
				$content .= 'THROWABLE (' . $i . ')' . PHP_EOL . $throwable->getMessage() . PHP_EOL;
				$content .= $throwable->getFile() . ' (: ' . $throwable->getLine() . ')' . PHP_EOL;
				$content .= $throwable->getTraceAsString() . PHP_EOL;
				$content .= PHP_EOL;
			}
		}

		$content .= '================================' . PHP_EOL;
		$content .= $countSuccess . ' out of ' . $count;

		return $content;
	}
}
