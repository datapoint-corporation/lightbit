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

namespace Lightbit\Base;

use \ReflectionClass;
use \Throwable;

use \Lightbit;
use \Lightbit\Base\Element;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Data\Conversing\StringCamelCaseConversion;
use \Lightbit\IO\FileSystem\Asset;
use \Lightbit\Scope;

/**
 * Controller.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
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
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The controller context.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @param array $configuration
	 *	The controller configuration map.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct();

		$this->context = $context;
		$this->id = $id;

		if ($configuration)
		{
			(new Scope($this))->configure($configuration);
		}
	}

	/**
	 * Displays a view.
	 *
	 * If the view identifier is not absolute, a theme is set and a matching
	 * view override exists, the override is rendered instead of the default
	 * controller view.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 *
	 * @param bool $capture
	 *	The view output capture flag.
	 *
	 * @return string
	 *	The view output, if captured.
	 */
	public final function display(string $id, array $parameters = null) : void
	{
		if ($theme = $this->getTheme())
		{
			$theme->run($this->getView($id), $parameters);
		}
		else
		{
			$this->getView($id)->run($parameters, false);
		}
	}

	/**
	 * Gets a action method name.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @return string
	 *	The action method name.
	 */
	public function getActionMethodName(string $id) : string
	{
		return (new StringCamelCaseConversion($id, false))->toLowerCamelCase();
	}

	/**
	 * Gets the application.
	 *
	 * @return IApplication
	 *	The application.
	 */
	public final function getApplication() : IApplication
	{
		$context = $this->getContext();

		while (! ($context instanceof IApplication))
		{
			$context = $context->getContext();
		}

		return $context;
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : IContext
	{
		return $this->context;
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
	 * @return ITheme
	 *	The theme.
	 */
	public function getTheme() : ?ITheme
	{
		return $this->context->getTheme();
	}

	/**
	 * Gets a view.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public final function getView(string $id) : IView
	{
		$asset = new Asset($this->getViewsPath(), $id);

		if (!$asset->isAbsolute())
		{
			if ($theme = $this->getTheme())
			{
				if ($view = $theme->getView($this, $id))
				{
					return $view;
				}
			}
		}

		return new View($this, $id, (new Asset($this->getViewsPath(), $id))->getPath());
	}

	/**
	 * Gets the views path.
	 *
	 * @return string
	 *	The views path.
	 */
	public function getViewsPath() : string
	{
		return $this->context->getViewsPath() 
			. DIRECTORY_SEPARATOR
			. strtr($this->id, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Renders a view.
	 *
	 * If the view identifier is not absolute, a theme is set and a matching
	 * view override exists, the override is rendered instead of the default
	 * controller view.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 *
	 * @param bool $capture
	 *	The view output capture flag.
	 *
	 * @return string
	 *	The view output, if captured.
	 */
	public function render(string $id, array $parameters = null, bool $capture = false) : ?string
	{
		return $this->getView($id)->run($parameters, $capture);
	}

	/**
	 * Generates a response based on an action result.
	 *
	 * It is invoked automatically during the action run procedure, after 
	 * and only if a value is returned by its implementation.
	 *
	 * @param mixed $result
	 *	The result.
	 */
	public final function result($result) : void
	{
		$this->onResult($result);
	}

	/**
	 * Runs an action.
	 *
	 * @param string $method
	 *	The action method.
	 *
	 * @param array $arguments
	 *	The action method arguments.
	 *
	 * @return mixed
	 *	The result.
	 */
	public final function run(string $method, array $arguments)
	{
		return $this->{$method}(...$arguments);
	}

	/**
	 * Throwable handling.
	 *
	 * It is invoked automatically once a throwable is caught by the global
	 * handler, giving the controller the opportunity to generate the
	 * applicable error response.
	 *
	 * If the result is positivie, the throwable handling will not propagate
	 * to the parent contexts.
	 *
	 * @param Throwable $throwable
	 *	The throwable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function throwable(Throwable $throwable) : bool
	{
		return false;
	}

	/**
	 * On Result.
	 *
	 * It is invoked automatically during the action run procedure, after 
	 * and only if a value is returned by its implementation.
	 *
	 * @param mixed $result
	 *	The result.
	 */
	protected function onResult($result) : void
	{
		
	}
}