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

use \Lightbit\Base\Action;
use \Lightbit\Base\Element;
use \Lightbit\Base\IllegalParameterRouteException;
use \Lightbit\Base\MethodNotFoundRouteException;
use \Lightbit\Base\MissingParameterRouteException;

use \Lightbit\Base\IAction;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Base\ITheme;

/**
 * Controller.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Controller extends Element implements IController
{
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
	 * The views.
	 *
	 * @type array
	 */
	private $views;

	/**
	 * The views base paths.
	 *
	 * @type string
	 */
	private $viewsBasePaths;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context);

		$this->id = $id;
		$this->views = [];

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

		$view = $this->getView($view);

		if ($theme = $this->getTheme())
		{
			$theme->run
			(
				$view->run($parameters, true)
			);
		}
		else
		{
			$view->run($parameters);
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
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string
	{
		if (!$this->globalID)
		{
			$this->globalID = '';
			$context = $this->getContext();

			while ($parent = $context->getContext())
			{
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
	 * Gets the theme.
	 *
	 * @return string
	 *	The theme.
	 */
	public final function getTheme() : ?ITheme
	{
		return $this->getContext()->getTheme();
	}

	/**
	 * Gets a view.
	 *
	 * @param string $view
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public final function getView(string $view) : IView
	{
		if (!isset($this->views[$view]))
		{
			$this->views[$view] = new View
			(
				$this->getContext(),
				__asset_path_resolve_array
				(
					$this->getViewsBasePaths(),
					'php',
					$view
				)
			);
		}
		
		return $this->views[$view];
	}

	/**
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public final function getViewsBasePaths() : array
	{
		if (!$this->viewsBasePaths)
		{
			$this->viewsBasePaths = [];

			$context = $this->getContext();
			$suffix = DIRECTORY_SEPARATOR . strtr($this->id, [ '/' => DIRECTORY_SEPARATOR ]);

			if ($theme = $context->getTheme())
			{
				$this->viewsBasePaths[] = $theme->getViewsBasePath() . $suffix;
			}

			$this->viewsBasePaths[] = $this->getContext()->getViewsBasePath() . $suffix;
		}

		return $this->viewsBasePaths;
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

		$result = ($this->getView($view))->run($parameters, $capture);

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
	 * @return IAction
	 *	The result.
	 */
	public final function resolve(string $id, array $parameters) : IAction
	{
		$method;

		try
		{
			$method = (new \ReflectionClass($this))
				->getMethod($this->getActionMethodName($id));
		}
		catch (\ReflectionException $e)
		{
			throw new MethodNotFoundRouteException
			(
				$this->getContext(),
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				sprintf
				(
					'Can not resolve to action, method is undefined: action %s, at controller %s, at context %s',
					$id,
					$this->id,
					$this->getContext()->getGlobalID()
				)
			);
		}

		if (!$method->isPublic() || $method->isAbstract() || $method->isStatic())
		{
			throw new MethodNotFoundRouteException
			(
				$this->getContext(),
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				sprintf
				(
					'Can not resolve to action, method signature mismatch: action %s, at controller %s, at context %s',
					$id,
					$this->id,
					$this->getContext()->getGlobalID()
				)
			);
		}

		$params = [];
		foreach ($method->getParameters() as $i => $parameter)
		{
			$parameterName = $parameter->getName();

			if (isset($parameters[$parameterName]))
			{
				try
				{
					$params[$parameterName] = __type_filter
					(
						__type_signature($parameter->getType()),
						$parameters[$parameterName]
					);

					continue;
				}
				catch (\Throwable $e)
				{
					throw new IllegalParameterRouteException
					(
						$this->getContext(),
						($route = ([ $this->id . '/' . $id ] + $parameters)),
						$parameterName,
						sprintf
						(
							'Can not resolve to action, filter failure: %s, at action, %s, at controller %s, at context %s',
							$parameterName,
							$id,
							$this->id,
							$this->getContext()->getPrefix()
						),
						$e
					);
				}
			}

			if ($parameter->isDefaultValueAvailable())
			{
				$params[$parameterName] = $parameter->getDefaultValue();
				continue;
			}

			if ($parameter->allowsNull())
			{
				$params[$parameterName] = null;
				continue;
			}

			throw new MissingParameterRouteException
			(
				$this->getContext(),
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				$parameterName,
				sprintf
				(
					'Can not resolve to action, parameter missing: %s, at action, %s, at controller %s, at context %s',
					$parameterName,
					$id,
					$this->id,
					$this->getContext()->getPrefix()
				)
			);
		}

		return new Action($this, $id, $params);
	}

	/**
	 * Runs an action.
	 *
	 * @param IAction $action
	 *	The action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function run(IAction $action) // : mixed
	{
		global $_LIGHTBIT_ACTION;
		global $_LIGHTBIT_CONTEXT;

		// Save the previous action and context.
		$pAction = __action_replace($action);
		$pContext = __context_replace($this->getContext());

		// Run
		$this->onRun();

		$controller = $action->getController();

		$result = __object_call_array
		(
			$controller,
			$controller->getActionMethodName($action->getName()),
			array_values($action->getParameters())
		);

		$this->onAfterRun();

		// Restore action and context.
		__action_set($pAction);
		__context_set($pContext);

		return $result;
	}

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public function setLayout(?string $layout) : void
	{
		if ($theme = $this->getTheme())
		{
			$theme->setLayout($layout);
		}
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
