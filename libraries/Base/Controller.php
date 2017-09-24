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
use \Lightbit\Base\Action;
use \Lightbit\Base\Element;
use \Lightbit\Base\Context;
use \Lightbit\Base\IController;
use \Lightbit\Base\IllegalParameterRouteException;
use \Lightbit\Base\IView;
use \Lightbit\Base\MethodNotFoundRouteException;
use \Lightbit\Base\MissingParameterRouteException;
use \Lightbit\Base\SlugParseParameterRouteException;
use \Lightbit\Base\View;
use \Lightbit\Data\Filtering\Filter;
use \Lightbit\Data\Filtering\FilterException;
use \Lightbit\Exception;

/**
 * Controller.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Controller extends Element implements IController
{
	/**
	 * The context.
	 *
	 * @type Context
	 */
	private $context;

	/**
	 * The global identifier.
	 *
	 * @type string
	 */
	private $globalID;

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
	 * @param Context $context
	 *	The context.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(Context $context, string $id, array $configuration = null)
	{
		$this->context = $context;
		$this->id = $id;

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}

	/**
	 * Creates a default action method name.
	 *
	 * @param string $action
	 *	The action name.
	 *
	 * @return string
	 *	The default action method name.
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
	public final function display(string $view, array $parameters = null) : void
	{
		$this->onDisplay();

		$layoutPath = $this->getLayoutPath();

		if ($layoutPath)
		{
			$this->view($layoutPath)->run([ 'content' => $this->render($view, $parameters, true) ]);
		}
		else
		{
			$this->render($view, $parameters, false);
		}

		$this->onAfterDisplay();
	}

	/**
	 * Gets an action method name.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @return string
	 *	The action method name.
	 */
	public final function getActionMethodName(string $id) : string
	{
		static $actionMethodNames = [];

		if (!isset($actionMethodNames[$id]))
		{
			$actionMethodNames[$id] = $this->actionMethodName($id);
		}

		return $actionMethodNames[$id];
	}

	/**
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public final function getContext() : Context
	{
		return $this->context;
	}

	/**
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string
	{
		if (!$this->globalID)
		{
			$tokens = [];
			$context = $this->context;

			$this->globalID = '';

			while (true)
			{
				$parent = $context->getContext();

				if (!$parent)
				{
					break;
				}

				$this->globalID = $context->getID() . '/' . $this->globalID;
				$context = $parent;
			}

			$this->globalID .= $this->id;
		}

		return $this->globalID;
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
			return $this->getContext()->getLayout();
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
				return $this->getContext()->getLayoutPath();
			}

			$this->layoutPath = __asset_path_resolve
			(
				$this->getContext()->getPath(),
				'php', $this->layout
			);
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
		static $viewsBasePaths;

		if (!isset($viewsBasePaths))
		{
			$viewsBasePaths = [];

			foreach ($this->getContext()->getViewsBasePaths() as $i => $path)
			{
				$viewsBasePaths[] = $path . DIRECTORY_SEPARATOR . strtr($this->id, [ '/' => DIRECTORY_SEPARATOR ]);
			}
		}

		return $viewsBasePaths;
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
		$this->onRender();

		$result = $this->view(__asset_path_resolve_array($this->getViewsBasePaths(), 'php', $view))
			->run($parameters, $capture);

		$this->onAfterRender();

		return $result;
	}

	/**
	 * Resolves an action.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 *
	 * @return Action
	 *	The result.
	 */
	public final function resolve(string $id, array $parameters) : Action
	{
		$methodName = $this->getActionMethodName($id);
		$method;

		try
		{
			$method = (new \ReflectionClass($this))->getMethod($methodName);
		}
		catch (\ReflectionException $e)
		{
			throw new MethodNotFoundRouteException
			(
				$this->context,
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				sprintf
				(
					'Action not found, method is not defined: "%s", at controller "%s", at context "%s"',
					$id,
					$this->id,
					$this->getContext()->getPrefix()
				)
			);
		}

		if (!$method->isPublic() || $method->isAbstract() || $method->isStatic())
		{
			throw new MethodNotFoundRouteException
			(
				$this->context,
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				sprintf
				(
					'Action not found, method signature mismatch: "%s", at controller "%s", at context "%s"',
					$id,
					$this->id,
					$this->getContext()->getPrefix()
				)
			);
		}

		$arguments = [];

		foreach ($method->getParameters() as $i => $parameter)
		{
			$parameterName = $parameter->getName();

			if (isset($parameters[$parameterName]))
			{
				$arguments[] = $this->argument($id, $parameters, $methodName, $parameter->getType(), $parameterName, $parameters[$parameterName]);
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

			throw new MissingParameterRouteException
			(
				$this->context,
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				$parameterName,
				sprintf
				(
					'Action binding failure, missing parameter: "%s", at action, "%s", at controller "%s", at context "%s"',
					$parameterName,
					$id,
					$this->id,
					$this->getContext()->getPrefix()
				)
			);
		}

		return new Action($this, $id, $arguments);
	}

	/**
	 * Runs an action.
	 *
	 * @param Action $action
	 *	The action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function run(Action $action) // : mixed
	{
		$this->onRun();

		$controller = $action->getController();

		$result = __object_call_array
		(
			$controller,
			$controller->getActionMethodName($action->getName()),
			$action->getArguments()
		);

		$this->onAfterRun();

		return $result;
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
		return new View($this->context, $path, $configuration);
	}

	/**
	 * Creates an action argument.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $parameters
	 *	The parameters.
	 *
	 * @param string $methodName
	 *	The method name.
	 *
	 * @param string $typeName
	 *	The type name.
	 *
	 * @param string $parameterName
	 *	The parameter name.
	 *
	 * @param mixed $value
	 *	The value.
	 *
	 * @return mixed
	 *	The result.
	 */
	private function argument(string $id, array $parameters, string $methodName, ?string $typeName, string $parameterName, $value)
	{
		// When a type name is not defined, only scalar values will be accepted
		// as action parameters.
		if (!$typeName)
		{
			if (!__type_is_scalar($typeName))
			{
				throw new IllegalParameterRouteException
				(
					($route = ([ $this->id . '/' . $id ] + $parameters)),
					$parameterName,
					sprintf
					(
						'Action binding failure, not a scalar: "%s", at action, "%s", at controller "%s", at context "%s"',
						$parameterName,
						$id,
						$this->id,
						$this->getContext()->getPrefix()
					)
				);
			}
		}

		$valueTypeName = __type_of($value);

		if ($typeName == $valueTypeName)
		{
			return $value;
		}

		if (__type_is_scalar($typeName))
		{
			try
			{
				return Filter::create($typeName)->run($value);
			}
			catch (FilterException $e)
			{
				throw new IllegalParameterRouteException
				(
					$this->context,
					($route = ([ $this->id . '/' . $id ] + $parameters)),
					sprintf
					(
						'Action binding failure, filter failure: "%s", at action, "%s", at controller "%s", at context "%s"',
						$parameterName,
						$id,
						$this->id,
						$this->getContext()->getPrefix()
					),
					$e
				);
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
					throw new IllegalParameterRouteException
					(
						$this->context,
						($route = ([ $this->id . '/' . $id ] + $parameters)),
						$parameterName,
						sprintf
						(
							'Action parameter bind failure, slug is invalid: "%s", at action, "%s", at controller "%s", at context "%s"',
							$parameterName,
							$id,
							$this->id,
							$this->getContext()->getPrefix()
						)
					);
				}

				return $result;
			}
			catch (\Throwable $e)
			{
				throw new SlugParseParameterRouteException
				(
					$this->context,
					($route = ([ $this->id . '/' . $id ] + $parameters)),
					$parameterName,
					$typeName,
					$value,
					sprintf
					(
						'Action parameter bind failure, slug parse failure: "%s", at action, "%s", at controller "%s", at context "%s"',
						$parameterName,
						$id,
						$this->id,
						$this->getContext()->getPrefix()
					),
					$e
				);
			}
		}

		throw new IllegalParameterRouteException
		(
			$this->context,
			($route = ([ $this->id . '/' . $id ] + $parameters)),
			$parameterName,
			sprintf
			(
				'Action parameter bind failure, parameter is invalid: "%s", at action, "%s", at controller "%s", at context "%s"',
				$parameterName,
				$id,
				$this->id,
				$this->getContext()->getPrefix()
			)
		);
	}

	/**
	 * Called during the controller display procedure, after the view is
	 * resolved, constructed and executed.
	 */
	protected function onAfterDisplay() : void
	{
		$this->raise('base.controller.display.after');
	}

	/**
	 * Called during the controller render procedure, after the view is
	 * resolved, constructed and executed.
	 */
	protected function onAfterRender() : void
	{
		$this->raise('base.controller.render.after');
	}

	/**
	 * Called during the controller run procedure, after the applicable
	 * action method is invoked.
	 */
	protected function onAfterRun() : void
	{
		$this->raise('base.controller.run.after');
	}

	/**
	 * Called during the controller display procedure, before the view is
	 * resolved, constructed and executed.
	 */
	protected function onDisplay() : void
	{
		$this->raise('base.controller.display');
	}

	/**
	 * Called during the controller render procedure, before the view is
	 * resolved, constructed and executed.
	 */
	protected function onRender() : void
	{
		$this->raise('base.controller.render');
	}

	/**
	 * Called during the controller run procedure, before the applicable
	 * action method is invoked.
	 */
	protected function onRun() : void
	{
		$this->raise('base.controller.run');
	}
}
