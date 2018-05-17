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

/**
 * HttpRoute.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRoute implements IHttpRoute
{
	private $controllerClassName;

	private $controllerMethodName;

	private $expression;

	private $generic;

	private $methods;

	private $parameters;

	private $pattern;

	private $tokens;

	public function __construct(string $method, string $pattern, string $controllerClassName, string $controllerMethodName)
	{
		$this->controllerClassName = $controllerClassName;
		$this->controllerMethodName = $controllerMethodName;
		$this->generic = ($method === '*' && ($method = 'DELETE,GET,POST,PUT'));
		$this->pattern = '/' . trim($pattern, '/');

		// Get the method details.
		foreach (preg_split('%(\\s*\\,\\s*)%', $method, -1, PREG_SPLIT_NO_EMPTY) as $i => $token)
		{
			$this->methods[$token] = HttpMethod::getInstance($token);
		}

		// Get all tokens, if any, from the current pattern.
		if (preg_match_all('%\\{((bool|int|float|string)\\:)?([^\\}]+)\\}%', $this->pattern, $tokens, PREG_SET_ORDER))
		{
			$offset = 1;
			$keywords = [];
			$this->tokens = [];

			foreach ($tokens as $i => $token)
			{
				$keyword = preg_quote($token[0], '%');
				$this->tokens[$token[3]] = $offset;

				switch ($token[2])
				{
					case 'bool':
						$offset += 1;
						$keywords[$keyword] = '(true|false)';
						break;

					case 'int':
						$offset += 3;
						$keywords[$keyword] = '((\\+|\\-)?(\\d+))';
						break;

					case 'float':
						$offset += 6;
						$keywords[$keyword] = '((\\+|\\-)?((\\d+(\\.\\d+)?)|(\\.\\d+)))';
						break;

					case 'slug':
						$offset += 2;
						$keywords[$keyword] = '([a-z][a-z0-9]*(\\-[a-z][a-z0-9]*)*)';
						break;

					default:
						$offset += 1;
						$keywords[$keyword] = '([^\\/]+)';
						break;
				}
			}

			$this->expression = '%^' . strtr(preg_quote($this->pattern, '%'), $keywords) . '$%';
		}
	}

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
	public function extract(string $subject, array &$tokens = null) : bool
	{
		$subject = '/' . trim($subject, '/');
		$tokens = [];

		if (isset($this->expression))
		{
			if (preg_match($this->expression, $subject, $match))
			{
				foreach ($this->tokens as $token => $offset)
				{
					$tokens[$token] = $match[$offset];
				}

				return true;
			}

			return false;
		}

		return $subject === $this->pattern;
	}

	/**
	 * Gets the controller class.
	 *
	 * @return ReflectionClass
	 *	The controller class.
	 */
	public function getControllerClass() : ReflectionClass
	{
		return new ReflectionClass($this->controllerClassName);
	}

	/**
	 * Gets the controller class name.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public function getControllerClassName() : string
	{
		return $this->controllerClassName;
	}

	/**
	 * Gets the controller action method.
	 *
	 * @return ReflectionMethod
	 *	The controller action method.
	 */
	public function getControllerMethod() : ReflectionMethod
	{
		return $this->getControllerClass()->getMethod($this->controllerMethodName);
	}

	/**
	 * Gets the controller method name.
	 *
	 * @return string
	 *	The controller method name.
	 */
	public function getControllerMethodName() : string
	{
		return $this->controllerMethodName;
	}

	/**
	 * Gets the methods.
	 *
	 * @return array
	 *	The methods.
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}

	/**
	 * Gets the pattern.
	 *
	 * @return string
	 *	The pattern.
	 */
	public function getPattern() : string
	{
		return $this->pattern;
	}

	/**
	 * Checks if the route is compatible with the four standard methods,
	 * namely the "GET", "POST", "PUT" and "DELETE".
	 *
	 * @return bool
	 *	The result.
	 */
	public function isGeneric() : bool
	{
		return $this->generic;
	}
}
