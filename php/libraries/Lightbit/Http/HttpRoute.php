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
use \ReflectionException;
use \ReflectionMethod;

use \Lightbit\Data\Parsing\ParserProvider;
use \Lightbit\Http\IHttpRoute;

/**
 * HttpRoute.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRoute implements IHttpRoute
{
	private $controllerClass;

	private $controllerClassName;

	private $controllerMethod;

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
		$tokens = [];
		$subject = '/' . trim($subject, '/');

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
	 * @throws HttpRouteControllerClassException
	 *	Thrown when the controller class reflection fails to be fetched
	 *	for usage in a route resolution context.
	 *
	 * @return ReflectionClass
	 *	The controller class.
	 */
	public function getControllerClass() : ReflectionClass
	{
		if (!isset($this->controllerClass))
		{
			$subject;

			try
			{
				$subject = new ReflectionClass($this->controllerClassName);
			}
			catch (ReflectionException $e)
			{
				throw new HttpRouteControllerClassException($this, sprintf('Can not get controller class, reflection failure: "%s"', $this->controllerClassName), $e);
			}

			$this->controllerClass = $subject;
		}

		return $this->controllerClass;
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
	 * @throws HttpRouteControllerMethodException
	 *	Thrown when the controller method reflection fails to be fetched
	 *	for usage in a route resolution context.
	 *
	 * @return ReflectionMethod
	 *	The controller action method.
	 */
	public function getControllerMethod() : ReflectionMethod
	{
		if (!isset($this->controllerMethod))
		{
			$subject;

			try
			{
				$subject = $this->getControllerClass()->getMethod($this->controllerMethodName);
			}
			catch (ReflectionException $e)
			{
				throw new HttpRouteControllerMethodException($this, sprintf('Can not get controller method, reflection failure: "%s", on method "%s"', $this->controllerClassName, $this->controllerMethodName), $e);
			}

			if (!$subject->isPublic())
			{
				throw new HttpRouteControllerMethodException($this, sprintf('Can not get controller method, it is not public: "%s", on method "%s"', $this->controllerClassName, $this->controllerMethodName));
			}

			$this->controllerMethod = $subject;
		}

		return $this->controllerMethod;
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

	/**
	 * Resolve.
	 *
	 * @throws HttpRouteParameterNotSetException
	 *	Thrown when a parameter can not be parsed according to the
	 *	constraint imposed by the action method parameter type hint.
	 *
	 * @throws HttpRouteParameterCompositionException
	 *	Thrown when a parameter can not be composed according to the
	 *	constraint imposed by the action method parameter type hint.
	 *
	 * @throws HttpRouteParameterParseException
	 *	Thrown when a parameter can not be parsed according to the
	 *	constraint imposed by the action method parameter type hint.
	 *
	 * @param string $path
	 *	The resolution path.
	 *
	 * @param array $parameters
	 *	The resolution parameters.
	 *
	 * @return IHttpAction
	 *	The action, on success.
	 */
	public function resolve(string $path, array $parameters = null) : ?IHttpAction
	{
		$tokens;
		$path = '/' . trim($path, '/');

		if ($this->extract($path, $tokens))
		{
			// Set the tokens to the parameters array, where tokens have
			// precedence over the given parameters.
			$parameters = ($tokens + ($parameters ?? []));

			// Get the method and go through each parameter to assemble the
			// action arguments.
			$method = $this->getControllerMethod();
			$parserProvider = ParserProvider::getInstance();
			$arguments = [];

			foreach ($method->getParameters() as $i => $parameter)
			{
				$name = $parameter->getName();

				// When a parameter exists...
				if (isset($parameters[$name]))
				{
					// We must first check for an existing constraint and
					// match it against the candidate type.
					if ($type = $parameter->getType())
					{
						$constraint = type($type->__toString());
						$candidate = typeof($parameter[$name]);

						// If the constraint is equals to or assignable from
						// the candidate, just assign it.
						if ($constraint->equals($candidate) || $constraint->isAssignableFrom($candidate))
						{
							// $parameters[$name] = $parameters[$name];
							continue;
						}

						// If the candidate is a string, we can make an attempt
						// at parsing it before assignment.
						if ('string' === $candidate->getName())
						{
							try
							{
								$parameters[$name] = $parserProvider->getParser($constraint->getName())->parse($parameters[$name]);
							}
							catch (ParserException $e)
							{
								if (isset($tokens[$name]))
								{
									throw new HttpRouteTokenParseException($this, $name, sprintf('Can not bind controller action argument, token parse failure: "%s", at token "%s", of type "%s"', $path, $name, $constraint->getName()), $e);
								}

								throw new HttpRouteParameterParseException($this, $name, sprintf('Can not bind controller action argument, parameter parse failure: "%s", at parameter "%s", of type "%s"', $path, $name, $constraint->getName()), $e);
							}
						}

						// If the constraint is a string, we can make an attempt
						// at composing it before assignment.
						if ('string' === $constraint->getName())
						{
							try
							{
								$parameters[$name] = $parserProvider->getParser($candidate->getName())->compose($parameters[$name]);
							}
							catch (ParserException $e)
							{
								if (isset($tokens[$name]))
								{
									throw new HttpRouteTokenCompositionException($this, $name, sprintf('Can not bind controller action argument, token composition failure: "%s", at token "%s", of type "%s"', $path, $name, $constraint->getName()), $e);
								}

								throw new HttpRouteParameterCompositionException($this, $name, sprintf('Can not bind controller action argument, parameter composition failure: "%s", at parameter "%s", of type "%s"', $path, $name, $constraint->getName()), $e);
							}
						}

						// Give up, the constraint is probably not supported
						// and we can't do this safely.
						throw new HttpRouteParameterParseException($this, $name, sprintf('Can not bind controller action argument, constraint is not supported: "%s", at parameter "%s", of type "%s"', $path, $name, $constraint->getName()), $e);
					}

					// If the constraint is not set, we simply let everything
					// go and hope the developer is aware of the security
					// implications.
					$parameters[$name] = $parameters[$name];
					continue;
				}

				// If it does not exist, we make an attempt at using the
				// default parameter.
				if ($parameter->isOptional())
				{
					$parameters[$name] = $parameter->getDefaultValue();
				}

				// If it's nullable, guess what...
				if ($parameter->allowsNull())
				{
					$parameters[$name] = null;
				}
			}

			return new HttpAction($this, $parameters);
		}

		return null;
	}
}
