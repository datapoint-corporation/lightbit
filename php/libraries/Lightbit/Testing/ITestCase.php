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

/**
 * ITestCase.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
interface ITestCase
{
	/**
	 * Gets the closure.
	 *
	 * @return Closure
	 *	The closure.
	 */
	public function getClosure() : Closure;

	/**
	 * Gets the description.
	 *
	 * @return string
	 *	The description.
	 */
	public function getDescription() : string;

	/**
	 * Gets the error messages.
	 *
	 * @return array
	 *	The error messages.
	 */
	public function getErrorMessages() : array;

	/**
	 * Gets the messages.
	 *
	 * @return array
	 *	The messages.
	 */
	public function getMessages() : array;

	/**
	 * Gets the warning messages.
	 *
	 * @return array
	 *	The warning messages.
	 */
	public function getWarningMessages() : array;

	/**
	 * Gets the throwables.
	 *
	 * @return array
	 *	The throwables.
	 */
	public function getThrowables() : array;

	/**
	 * Validate.
	 *
	 * When called, it invokes the test case closure and performs the
	 * applicable validation on its result, failing if it does not meet
	 * expectation or if an uncaught throwable is detected.
	 *
	 * @return bool
	 *	The success status.
	 */
	public function validate() : bool;
}
