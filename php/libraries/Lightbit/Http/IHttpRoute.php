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

namespace Lightbit\Http;

use \ReflectionClass;
use \ReflectionMethod;

/**
 * IHttpRoute.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpRoute
{
	/**
	 * Extracts tokens from a unform resource location relative to the
	 * host document root.
	 *
	 * @param string $subject
	 *	The uniform resource location.
	 *
	 * @param array $tokens
	 *	The tokens output variable.
	 *
	 * @return bool
	 *	The success status.
	 */
	public function extract(string $subject, array &$tokens = null) : bool;

	/**
	 * Gets the controller class.
	 *
	 * @return ReflectionClass
	 *	The controller class.
	 */
	public function getControllerClass() : ReflectionClass;

	/**
	 * Gets the controller class name.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public function getControllerClassName() : string;

	/**
	 * Gets the controller method.
	 *
	 * @return ReflectionMethod
	 *	The controller method.
	 */
	public function getControllerMethod() : ReflectionMethod;

	/**
	 * Gets the controller method name.
	 *
	 * @return string
	 *	The controller method name.
	 */
	public function getControllerMethodName() : string;

	/**
	 * Gets the methods.
	 *
	 * @return array
	 *	The methods.
	 */
	public function getMethods() : array;

	/**
	 * Gets the pattern.
	 *
	 * @return string
	 *	The pattern.
	 */
	public function getPattern() : string;

	/**
	 * Checks if the route is compatible with the four standard methods,
	 * namely the "GET", "POST", "PUT" and "DELETE".
	 *
	 * @return bool
	 *	The result.
	 */
	public function isGeneric() : bool;
}
