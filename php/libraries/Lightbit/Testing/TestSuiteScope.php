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

use \Lightbit\Testing\ITestSuite;
use \Lightbit\Testing\TestSuiteProvider;
use \Lightbit\Testing\TestCases\EqualityTestCase;
use \Lightbit\Testing\TestCases\StrictEqualityTestCase;
use \Lightbit\Testing\TestCases\ThrowTestCase;

class TestSuiteScope
{
	private $suite;

	public function __construct(ITestSuite $suite)
	{
		$this->suite = $suite;
	}

	public function equals($constraint, string $description, Closure $closure) : void
	{
		$this->suite->addCase(new EqualityTestCase($description, $constraint, $closure));
	}

	public function exactly($constraint, string $description, Closure $closure) : void
	{
		$this->suite->addCase(new StrictEqualityTestCase($description, $closure, $constraint));
	}

	public function import(string ...$testSuites) : void
	{
		$provider = TestSuiteProvider::getInstance();

		foreach ($testSuites as $i => $testSuite)
		{
			$this->suite->addSuite($provider->getSuite($testSuite));
		}
	}

	public function throws(string $className, string $description, Closure $closure) : void
	{
		$this->suite->addCase(new ThrowTestCase($description, $closure, $className));
	}

	public function setTitle(string $title) : void
	{
		$this->suite->setTitle($title);
	}
}