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

use \Lightbit\AssetManagement\Php\PhpAsset;
use \Lightbit\Testing\ITestSuite;
use \Lightbit\Testing\TestCases\EqualTestCase;
use \Lightbit\Testing\TestCases\ExactlyTestCase;

/**
 * TestSuiteScope.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class TestSuiteScope
{
	/**
	 * The suite.
	 *
	 * @var ITestSuite
	 */
	private $suite;

	/**
	 * Constructor.
	 *
	 * @param ITestSuite $suite
	 *	The test suite.
	 */
	public function __construct(ITestSuite $suite)
	{
		$this->suite = $suite;
	}

	/**
	 * Creates an equality test case.
	 *
	 * @param string $description
	 *	The test case description.
	 *
	 * @param mixed $constraint
	 *	The test case constraint.
	 *
	 * @param Closure $closure
	 *	The test case closure.
	 */
	public function equals(string $description, $constraint, Closure $closure) : void
	{
		$this->suite->addCase(new EqualTestCase($description, $constraint, $closure));
	}

	/**
	 * Creates an exact equality test case.
	 *
	 * @param string $description
	 *	The test case description.
	 *
	 * @param mixed $constraint
	 *	The test case constraint.
	 *
	 * @param Closure $closure
	 *	The test case closure.
	 */
	public function exactly(string $description, $constraint, Closure $closure) : void
	{
		$this->suite->addCase(new ExactlyTestCase($description, $constraint, $closure));
	}
}
