<?php

// -----------------------------------------------------------------------------
// Lightbit
//
// Copyright (c) 2017 Datapoint — Sistemas de Informação, Unipessoal, Lda.
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

namespace Lightbit\Base;

use \Lightbit;
use \Lightbit\Action;
use \Lightbit\Base\Element;
use \Lightbit\Base\IContainer;
use \Lightbit\Base\IController;
use \Lightbit\Base\IView;
use \Lightbit\Base\View;
use \Lightbit\Data\Validation\Filter;
use \Lightbit\Data\Validation\FilterException;
use \Lightbit\Exception;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Helpers\TypeHelper;
use \Lightbit\IllegalParameterRouteException;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\MethodNotFoundRouteException;
use \Lightbit\MissingParameterRouteException;
use \Lightbit\SlugParseParameterRouteException;

/**
 * Controller.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Controller extends Element implements IController
{
	/**
	 * The container.
	 *
	 * @type IContainer
	 */
	private $container;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * The layout.
	 *
	 * @type string
	 */
	private $layout;

	/**
	 * The layout path.
	 *
	 * @type string
	 */
	private $layoutPath;

	/**
	 * Constructor.
	 *
	 * @param IContainer $container
	 *	The container.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(IContainer $container, string $id, array $configuration = null)
	{
		$this->container = $container;
		$this->id = $id;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Creates an action method name.
	 *
	 * @param string $action
	 *	The action name.
	 *
	 * @return string
	 *	The action method name.
	 */
	protected function actionMethodName(string $action) : string
	{
		return lcfirst(strtr(ucwords(strtr($action, [ '-' => ' ' ])), [ ' ' => '' ]));
	}

	/**
	 * Displays a view.
	 *
	 * @param string $view
	 *	The view file system alias.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 */
	public function display(string $view, array $parameters = null) : void
	{
		$layoutPath = $this->getLayoutPath();

		if ($layoutPath)
		{
			(new View($layoutPath))
				->run([ 'content' => $this->render($view, $parameters, true) ]);
		}
		else
		{
			$this->render($view, $parameters, false);
		}
	}

	/**
	 * Gets an action method name.
	 *
	 * @param string $action
	 *	The action name.
	 *
	 * @return string
	 *	The action method name.
	 */
	public final function getActionMethodName(string $action) : string
	{
		static $actionMethodNames = [];

		if (!isset($actionMethodNames[$action]))
		{
			$actionMethodNames[$action] = $this->actionMethodName($action);
		}

		return $actionMethodNames[$action];
	}

	/**
	 * Gets the container.
	 *
	 * @return IContainer
	 *	The container.
	 */
	public final function getContainer() : IContainer
	{
		return $this->container;
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public final function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the layout.
	 *
	 * @return string
	 *	The layout.
	 */
	public final function getLayout() : ?string
	{
		if (!$this->layout)
		{
			return $this->getApplication()->getLayout();
		}

		return $this->layout;
	}

	/**
	 * Gets the layout path.
	 *
	 * @return string
	 *	The layout path.
	 */
	public final function getLayoutPath() : ?string
	{
		if (!$this->layoutPath)
		{
			if (!$this->layout)
			{
				return $this->getApplication()->getLayoutPath();
			}

			$this->layoutPath = (new Alias($this->layout))
				->resolve('php', $this->getApplication()->getPath());
		}

		return $this->layoutPath;
	}

	/**
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public function getViewsBasePaths() : array
	{
		static $viewPaths;

		if (!isset($viewPaths))
		{
			$viewPaths = [];

			foreach ($this->getContainer()->getViewsBasePaths() as $i => $path)
			{
				$viewPaths[] = $path . DIRECTORY_SEPARATOR . strtr($this->id, '/', DIRECTORY_SEPARATOR);
			}
		}

		return $viewPaths;
	}

	/**
	 * Renders a view.
	 *
	 * @param string $view
	 *	The view file system alias.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 *
	 * @param bool $capture
	 *	The capture flag which, when set, will use an additional output 
	 *	buffer to capture any generated contents.
	 *
	 * @return string
	 *	The captured content.
	 */
	public final function render(string $view, array $parameters = null, bool $capture = false) : ?string
	{
		return $this->view((new Alias($view))->lookup('php', $this->getViewsBasePaths()))
			->run($parameters, $capture);
	}

	/**
	 * Resolves an action.
	 *
	 * @param string $name
	 *	The action name.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 *
	 * @return Action
	 *	The result.
	 */
	public final function resolve(string $action, array $parameters) : Action
	{
		$methodName = $this->getActionMethodName($action);
		$method;

		try
		{
			$method = (new \ReflectionClass($this))->getMethod($methodName);
		}
		catch (\ReflectionException $e)
		{
			$route = [ $this->id . '/' . $action ] + $parameters;
			throw new MethodNotFoundRouteException($route, sprintf('Bad route path, method not found: "%s", method "%s::%s"', $route[0], static::class, $methodName));
		}

		if (!$method->isPublic() || $method->isStatic())
		{
			$route = [ $this->id . '/' . $action ] + $parameters;
			throw new MethodNotFoundRouteException($route, sprintf('Bad route path, method signature mismatch: "%s", method "%s::%s"', $route[0], static::class, $methodName));
		}

		$arguments = [];

		foreach ($method->getParameters() as $i => $parameter)
		{
			$parameterName = $parameter->getName();

			if (isset($parameters[$parameterName]))
			{
				$arguments[] = $this->argument($action, $parameters, $methodName, $parameter->getType(), $parameterName, $parameters[$parameterName]);
				continue;
			}

			if ($parameter->isDefaultValueAvailable())
			{
				$arguments[] = $parameter->getDefaultValue();
				continue;
			}

			if ($parameter->allowsNull())
			{
				$arguments[] = null;
				continue;
			}

			$route = [ $this->id . '/' . $action ] + $parameters;
			throw new MissingParameterRouteException($route, sprintf('Bad route parameter, missing: "%s", parameter "%s", method "%s::%s"', $route[0], $parameterName, static::class, $methodName));
		}

		return new Action($this, $action, $arguments);
	}

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public final function setLayout(?string $layout) : void
	{
		$this->layout = $layout;
		$this->layoutPath = null;
	}

	/**
	 * Creates a view.
	 *
	 * @param string $path
	 *	The view path.
	 *
	 * @param array $configuration
	 *	The view configuration.
	 *
	 * @return IView
	 *	The view.
	 */
	protected function view(string $path, array $configuration = null) : IView
	{
		return new View($path, $configuration);
	}

	private function argument(string $action, array $parameters, string $methodName, ?string $typeName, string $parameterName, $value)
	{
		// When a type name is not defined, only scalar values will be accepted
		// as action parameters.
		if (!$typeName)
		{
			if (!TypeHelper::isScalarTypeName($typeName))
			{
				$route = [ '/' . $this->id . '/' . $action ] + $parameters;
				throw new IllegalParameterRouteException($route, sprintf('Bad route parameter, not a scalar type: "%s", parameter "%s", method "%s::%s"', $route[0], $parameterName, static::class, $methodName));
			}
		}

		$valueTypeName = TypeHelper::getNameOf($value);

		if ($typeName == $valueTypeName)
		{
			return $value;
		}

		if (TypeHelper::isScalarTypeName($typeName))
		{
			try
			{
				return Filter::create($typeName)->run($value);
			}
			catch (FilterException $e)
			{
				$route = [ '/' . $this->id . '/' . $action ] + $parameters;
				throw new IllegalParameterRouteException($route, sprintf('Bad route parameter, filter error: "%s", parameter "%s", method "%s::%s"', $route[0], $parameterName, static::class, $methodName));
			}
		}

		if (is_string($value))
		{
			$result;

			try
			{
				$result = $this->getSlugManager()->parse($typeName, $value);

				if (!isset($result))
				{
					throw new Exception(sprintf('Slug parse error: class "%s", slug "%s"', $typeName, $value));
				}

				return $result;
			}
			catch (\Throwable $e)
			{
				$route = [ '/' . $this->id . '/' . $action ] + $parameters;
				throw new SlugParseParameterRouteException($route, sprintf('Bad route parameter, slug parse: "%s", parameter "%s", method "%s::%s"', $route[0], $parameterName, static::class, $methodName));
			}
		}

		$path = '/' . $this->id . '/' . $action;
		throw new Exception(sprintf('Controller action defined unsupported parameter type hint: "%s", type hint "%s", parameter "%s"', $path, $typeName, $parameterName));
	}
}
