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
use \Lightbit\Http\HttpStatusException;

use \Lightbit\Base\IAction;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Base\ITheme;
use \Lightbit\Data\IModel;

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
	 * @var IContext
	 */
	private $context;

	/**
	 * The global identifier.
	 *
	 * @var string
	 */
	private $globalID;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The views.
	 *
	 * @var array
	 */
	private $views;

	/**
	 * The views base paths.
	 *
	 * @var string
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
	public final function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct();

		$this->context = $context;
		$this->id = $id;
		$this->views = [];

		$this->onConstruct();

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}

		$this->onAfterConstruct();
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext
	{
		return $this->context;
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
	 * Exports the current http request.
	 *
	 * @param string $method
	 *	The http request method.
	 *
	 * @param IModel $model
	 *	The http request model.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function export(string $method, IModel ...$model) : bool
	{
		$request = $this->getHttpRequest();

		if ($request->isOfMethod($method))
		{
			$result = true;

			foreach ($model as $i => $subject)
			{
				if (!$request->export($subject))
				{
					$result = false;
				}
			}

			return $result;
		}

		return false;
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
			$context = $this->context;

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
		return $this->context->getTheme();
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
		return $this->context->getView($this->id . '/' . $view);
	}

	/**
	 * Checks if a view exists.
	 *
	 * @param string $view
	 *	The view identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasView(string $view) : bool
	{
		return $this->context->hasView($this->id . '/' . $view);
	}

	/**
	 * Sets a response redirection.
	 *
	 * @param array $route
	 *	The response redirection route.
	 *
	 * @param int $statusCode
	 *	The response redirection status code.
	 */
	public final function redirect(array $route, int $statusCode = 303) : void
	{
		$response = $this->getHttpResponse();
		$response->reset();
		$response->setHeader('Location', $this->getHttpRouter()->url($route, true));
		$response->setStatusCode($statusCode);
		__exit(0);
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
				$this->context,
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				sprintf
				(
					'Can not resolve to action, method is undefined: action %s, at controller %s, at context %s',
					$id,
					$this->id,
					$this->context->getGlobalID()
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
					'Can not resolve to action, method signature mismatch: action %s, at controller %s, at context %s',
					$id,
					$this->id,
					$this->context->getGlobalID()
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
						$this->context,
						($route = ([ $this->id . '/' . $id ] + $parameters)),
						$parameterName,
						sprintf
						(
							'Can not resolve to action, filter failure: %s, at action, %s, at controller %s, at context %s',
							$parameterName,
							$id,
							$this->id,
							$this->context->getPrefix()
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
				$this->context,
				($route = ([ $this->id . '/' . $id ] + $parameters)),
				$parameterName,
				sprintf
				(
					'Can not resolve to action, parameter missing: %s, at action, %s, at controller %s, at context %s',
					$parameterName,
					$id,
					$this->id,
					$this->context->getPrefix()
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
	 * @return int
	 *	The result.
	 */
	public final function run(IAction $action) : int
	{
		// Save the previous action and context.
		$pAction = __action_replace($action);
		$pContext = __context_replace($this->context);

		// Run
		$this->onRun();

		$controller = $action->getController();

		$result = __object_call_array
		(
			$controller,
			$controller->getActionMethodName($action->getName()),
			array_values($action->getParameters())
		);

		if (is_int($result))
		{
			// If the result is an integer matching the range of an http status
			// code, handle it as such.
			if ($result > 99 && $result < 600)
			{
				// If it's an error, we'll simply throw a http status exception
				// and delegate the behaviour to the parent context.
				if ($result > 299)
				{
					throw new HttpStatusException($result, __http_status_message($result));
				}

				// If not, simply change the status code.
				$this->getHttpResponse()->setStatusCode($result);
			}
		}

		else if (is_array($result))
		{
			// If the result is an array, we'll generate a json response with
			// the contents of it encoded as json.
			$status = (__map_extract($result, '?int', '@status') ?? 200);
			$content = __json_encode($result);

			$response = $this->getHttpResponse();
			$response->reset();
			$response->setStatusCode($status);
			$response->setHeader('Content-Type', 'application/json; charset=utf-8');
			$response->setHeader('Content-Length', strlen($content));

			echo $content;
		}

		$this->onAfterRun();

		// Restore action and context.
		__action_set($pAction);
		__context_set($pContext);

		return (is_int($result) ? $result : 0);
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
	 * Creates and starts a new sql transaction to execute the procedure(s)
	 * implemented by the given closure.
	 *
	 * If the closure throws an uncaught exception, the transaction will
	 * automatically rollback before that exception is re-thrown.
	 *
	 * @param \Closure $closure
	 *	The transaction closure.
	 *
	 * @return mixed
	 *	The closure result.
	 */
	public function transaction(\Closure $closure) // : mixed;
	{
		$sql = $this->getSqlConnection();
		$transaction = $sql->transaction();

		try
		{
			$result = $closure($sql);
			$transaction->commit();
			return $result;
		}
		catch (\Throwable $e)
		{
			if (!$transaction->isClosed())
			{
				$transaction->rollback();
			}

			throw $e;
		}
	}

	/**
	 * Generates the proper response to a throwable caught by the global
	 * exception handler during an action implemented by this controller.
	 *
	 * If the controller can not generate the proper response, false should
	 * be returned in order to delegate control to the application.
	 *
	 * @param Throwable $throwable
	 *	The throwable object.
	 *
	 * @return bool
	 *	The result.
	 */
	public function throwable(\Throwable $throwable) : bool
	{
		return false;
	}

	/**
	 * On After Construct.
	 *
	 * This method is invoked during the application construction procedure,
	 * after the dynamic configuration is applied.
	 */
	protected function onAfterConstruct() : void
	{
		$this->raise('lightbit.base.controller.construct.after', $this);
	}

	/**
	 * On After Display.
	 *
	 * Called during the controller display procedure, after the view is
	 * resolved, constructed and executed.
	 */
	protected function onAfterDisplay() : void
	{
		$this->raise('base.controller.display.after');
	}

	/**
	 * On After Render.
	 *
	 * Called during the controller render procedure, after the view is
	 * resolved, constructed and executed.
	 */
	protected function onAfterRender() : void
	{
		$this->raise('base.controller.render.after');
	}

	/**
	 * On After Run.
	 *
	 * Called during the controller run procedure, after the applicable
	 * action method is invoked.
	 */
	protected function onAfterRun() : void
	{
		$this->raise('base.controller.run.after');
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the application construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		$this->raise('lightbit.base.controller.construct', $this);
	}

	/**
	 * On Display.
	 *
	 * Called during the controller display procedure, before the view is
	 * resolved, constructed and executed.
	 */
	protected function onDisplay() : void
	{
		$this->raise('base.controller.display');
	}

	/**
	 * On Render.
	 *
	 * Called during the controller render procedure, before the view is
	 * resolved, constructed and executed.
	 */
	protected function onRender() : void
	{
		$this->raise('base.controller.render');
	}

	/**
	 * On Run.
	 *
	 * Called during the controller run procedure, before the applicable
	 * action method is invoked.
	 */
	protected function onRun() : void
	{
		$this->raise('base.controller.run');
	}
}
