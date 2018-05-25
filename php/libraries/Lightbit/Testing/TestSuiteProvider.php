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

use \Lightbit\Testing\ITestSuite;
use \Lightbit\Testing\ITestSuiteFactory;
use \Lightbit\Testing\TestSuiteFactory;

/**
 * TestSuiteProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class TestSuiteProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var TestSuiteProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return TestSuiteProvider
	 *	The singleton instance.
	 */
	public static function getInstance() : TestSuiteProvider
	{
		return (self::$instance ?? (self::$instance = new TestSuiteProvider()));
	}

	/**
	 * The suite factory.
	 *
	 * @var ITestSuiteFactory
	 */
	private $suiteFactory;

	/**
	 * The suites.
	 */
	private $suites;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->suites = [];
	}

	/**
	 * Gets a suite.
	 *
	 * @param string $suite
	 *	The suite asset identifier.
	 *
	 * @return ITestSuite
	 *	The test suite.
	 */
	public final function getSuite(string $suite) : ITestSuite
	{
		return ($this->suites[$suite] ?? ($this->suites[$suite] = $this->getSuiteFactory()->createSuite($suite)));
	}

	/**
	 * Gets the suite factory.
	 *
	 * @return ITestSuiteFactory
	 *	The test suite factory.
	 */
	public final function getSuiteFactory() : ITestSuiteFactory
	{
		return ($this->suiteFactory ?? ($this->suiteFactory = new TestSuiteFactory()));
	}
}
