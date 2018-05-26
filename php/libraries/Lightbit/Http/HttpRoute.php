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

class HttpRoute implements IHttpRoute
{
	private $controllerClass;

	private $controllerClassName;

	private $controllerMethod;

	private $controllerMethodName;

	private $expression;

	private $methods;

	private $pattern;

	private $tokens;

	public function __construct(string $method, string $pattern, string $controllerClassName, string $controllerMethodName)
	{
		$this->controllerClassName = $controllerClassName;
		$this->controllerMethodName = $controllerMethodName;
		$this->generic = ($method === '*' && $method = 'DELETE,GET,POST,PUT');
		$this->pattern = $pattern;

		foreach (preg_split('%\\s*\\,\\s*%', $method, -1, PREG_SPLIT_NO_EMPTY) as $i => $method)
		{
			$this->methods[$method] = true;
		}

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

	public function getControllerClass() : ReflectionClass
	{
		return ($this->controllerClass ?? ($this->controllerClass = new ReflectionClass($this->controllerClassName)));
	}

	public function getControllerMethod() : ReflectionMethod
	{
		return ($this->controllerMethod ?? ($this->controllerMethod = $this->getControllerClass()->getMethod($this->controllerMethod)));
	}

	public final function match(string $method, string $path, array &$tokens = null) : bool
	{
		$tokens = [];

		if (isset($this->methods[$method]))
		{
			if (isset($this->expression))
			{
				if (preg_match($this->expression, $path, $match))
				{
					foreach ($this->tokens as $token => $offset)
					{
						$tokens[$token] = $match[$offset];
					}

					return true;
				}

				return false;
			}

			return ($path === $this->pattern);
		}

		return false;
	}
}
