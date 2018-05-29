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

namespace Lightbit\Http\Routing;

use \ReflectionClass;
use \ReflectionMethod;

use \Lightbit\Http\Routing\IHttpRoute;

/**
 * IHttpRoute.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HttpRoute implements IHttpRoute
{
	/**
	 * The controller class.
	 *
	 * @var ReflectionClass
	 */
	private $controllerClass;

	/**
	 * The controller class name.
	 *
	 * @var string
	 */
	private $controllerClassName;

	/**
	 * The controller method.
	 *
	 * @var ReflectionMethod
	 */
	private $controllerMethod;

	/**
	 * The controller method name.
	 *
	 * @var string
	 */
	private $controllerMethodName;

	/**
	 * The pattern regular expression.
	 *
	 * @var string
	 */
	private $expression;

	/**
	 * The method list.
	 *
	 * @var array
	 */
	private $methodsMap;

	/**
	 * The token map.
	 *
	 * @var array
	 */
	private $pathTokenMap;

	/**
	 * The pattern.
	 *
	 * @var string
	 */
	private $pattern;

	/**
	 * Constructor.
	 *
	 * @param string $method
	 *	The route method.
	 *
	 * @param string $pattern
	 *	The route pattern.
	 *
	 * @param string $controllerClassName
	 *	The route controller class name.
	 *
	 * @param string $controllerMethodName
	 *	The route controller method name.
	 */
	public function __construct(string $method, string $pattern, string $controllerClassName, string $controllerMethodName)
	{
		$this->controllerClassName = $controllerClassName;
		$this->controllerMethodName = $controllerMethodName;
		$this->generic = ($method === '*' && $method = 'DELETE,GET,POST,PUT');

		if ($this->pattern = trim($pattern, '/'))
		{
			$this->pattern = '/' . $this->pattern . '/';
		}
		else
		{
			$this->pattern = '/';
		}

		foreach (preg_split('%\\s*\\,\\s*%', $method, -1, PREG_SPLIT_NO_EMPTY) as $i => $method)
		{
			$this->methodMap[$method] = true;
		}

		if (preg_match_all('%\\{((bool|int|float|string)\\:)?([^\\}]+)\\}%', $this->pattern, $tokens, PREG_SET_ORDER))
		{
			$this->pathTokenMap = [];

			$pathTokenExpressionOffset = 1;
			$pathTokenExpressionMap = [];

			foreach ($tokens as $i => $token)
			{
				$pathTokenExpression = preg_quote($token[0], '%');

				$this->pathTokenMap[$token[3]] = [
					'token_expression' => $pathTokenExpression,
					'token_expression_offset' => $pathTokenExpressionOffset,
					'token_name' => $token[3],
					'token_tag' => $token[0]
				];

				switch ($token[2])
				{
					case 'bool':
						$pathTokenExpressionOffset += 1;
						$pathTokenExpressionMap[$pathTokenExpression] = '(true|false)';
						break;

					case 'int':
						$pathTokenExpressionOffset += 3;
						$pathTokenExpressionMap[$pathTokenExpression] = '((\\+|\\-)?(\\d+))';
						break;

					case 'float':
						$pathTokenExpressionOffset += 6;
						$pathTokenExpressionMap[$pathTokenExpression] = '((\\+|\\-)?((\\d+(\\.\\d+)?)|(\\.\\d+)))';
						break;

					case 'slug':
						$pathTokenExpressionOffset += 2;
						$pathTokenExpressionMap[$pathTokenExpression] = '([a-z][a-z0-9]*(\\-[a-z][a-z0-9]*)*)';
						break;

					default:
						$pathTokenExpressionOffset += 1;
						$pathTokenExpressionMap[$pathTokenExpression] = '([^\\/]+)';
						break;
				}
			}

			$this->expression = '%^' . strtr(preg_quote($this->pattern, '%'), $pathTokenExpressionMap) . '$%';
		}
	}

	public final function formatPath(array $pathTokenMap = null) : string
	{
		if (isset($this->expression))
		{
			$pathTokenValueMap = ($pathTokenMap ?? []);

			foreach ($this->getPathTokenList() as $i => $pathToken)
			{
				if (!isset($pathTokenValueMap[$pathToken]))
				{
					throw new HttpRoutePathFormatException(sprintf(
						'Can not format route path, token map mismatch: "%s"',
						$pathToken
					));
				}
			}

			foreach ($pathTokenValueMap as $pathToken => $pathTokenValue)
			{
				if (!isset($this->pathTokenMap[$pathToken]))
				{
					throw new HttpRoutePathFormatException(sprintf(
						'Can not format route path, token map mismatch: "%s"',
						$pathToken
					));
				}
			}

			$pathTokenTagReplacementMap = [];

			foreach ($this->pathTokenMap as $i => $pathToken)
			{
				$pathTokenTagReplacementMap[$pathToken['token_tag']] = $pathTokenValueMap[$pathToken['token_name']];
			}

			return strtr($this->pattern, $pathTokenTagReplacementMap);
		}

		if ($pathTokenMap)
		{
			throw new HttpRoutePathFormatException($this, sprintf(
				'Can not format route path, token map mismatch: "%s"',
				$this->pattern
			));
		}

		return $this->pattern;
	}

	/**
	 * Gets the controller class.
	 *
	 * @return ReflectionClass
	 *	The controller class.
	 */
	public final function getControllerClass() : ReflectionClass
	{
		return ($this->controllerClass ?? ($this->controllerClass = new ReflectionClass($this->controllerClassName)));
	}

	/**
	 * Gets the controller class name.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public final function getControllerClassName() : string
	{
		return $this->controllerClassName;
	}

	/**
	 * Gets the controller method.
	 *
	 * @return ReflectionMethod
	 *	The controller method.
	 */
	public final function getControllerMethod() : ReflectionMethod
	{
		return ($this->controllerMethod ?? ($this->controllerMethod = $this->getControllerClass()->getMethod($this->controllerMethodName)));
	}

	/**
	 * Gets the controller method name.
	 *
	 * @return string
	 *	The controller method name.
	 */
	public final function getControllerMethodName() : string
	{
		return $this->controllerMethodName;
	}

	/**
	 * Gets the path token list.
	 *
	 * @return array
	 *	The path token list.
	 */
	public final function getPathTokenList() : array
	{
		if (isset($this->pathTokenMap))
		{
			return array_keys($this->pathTokenMap);
		}

		return [];
	}

	/**
	 * Checks if a method is set.
	 *
	 * @param string $method
	 *	The method.
	 *
	 * @return bool
	 *	The status.
	 */
	public final function hasMethod(string $method) : bool
	{
		return isset($this->methodMap[$method]);
	}

	/**
	 * Matches the method an path against this routes method list and pattern,
	 * extracting any path tokens in the process.
	 *
	 * @param string $method
	 *	The method to match against.
	 *
	 * @param string $path
	 *	The path to match against.
	 *
	 * @param array $tokenMap
	 *	The path token map output variable.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function match(string $method, string $path, array &$tokenMap = null) : bool
	{
		$tokenMap = [];
		$path = '/' . trim($path, '/');

		if (isset($path[1]))
		{
			$path .= '/';
		}

		if (isset($this->methodMap[$method]))
		{
			if (isset($this->expression))
			{
				if (preg_match($this->expression, $path, $match))
				{
					foreach ($this->pathTokenMap as $i => $pathToken)
					{
						$tokenMap[$pathToken['token_name']] = $match[$pathToken['token_expression_offset']];
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
