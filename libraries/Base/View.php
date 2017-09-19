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
use \Lightbit\Exception;
use \Lightbit\Base\Element;
use \Lightbit\Base\IWidget;
use \Lightbit\Base\IView;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\IO\FileSystem\Alias;

/**
 * View.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class View extends Element implements IView
{
	/**
	 * The context.
	 *
	 * @type Context
	 */
	private $context;

	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The view context.
	 *
	 * @param string $path
	 *	The view path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(?Context $context, string $path, array $configuration = null)
	{
		$this->context = $context;
		$this->path = $path;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Gets the base path.
	 *
	 * @return string
	 *	The base path.
	 */
	public final function getBasePath() : string
	{
		static $basePath;

		if (!$basePath)
		{
			$basePath = dirname($this->path);
		}

		return $basePath;
	}

	/**
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public final function getContext() : Context
	{
		if ($this->context)
		{
			return $this->context;
		}

		return parent::getContext();
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Imports a variable.
	 *
	 * @param mixed $variable
	 *	The variable to import.
	 *
	 * @param mixed $default
	 *	The variable default value.
	 *
	 * @param Closure $closure
	 *	The variable validation closure.
	 */
	public final function import(&$variable, \Closure $closure = null) : void
	{
		if ($closure)
		{
			$result = false;

			try
			{
				$result = ($closure($variable) === true);
			}
			catch (\Throwable $e)
			{
				throw new Exception(sprintf('View variable validation failure, %s: "%s", at context "%s"', lcfirst($e->getMessage()), $this->path, $this->getContext()->getPrefix()));
			}

			if (!$result)
			{
				throw new Exception(sprintf('View variable validation failure: "%s", at context "%s"', $this->path, $this->getContext()->getPrefix()));
			}
		}
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
		return $this->view((new Alias($view))->resolve('php', $this->getBasePath()))
			->run($parameters, $capture);
	}

	/**
	 * Runs the view.
	 *
	 * @param array $parameters
	 *	The parameters.
	 *
	 * @param bool $capture
	 *	The capture flag which, when set, will use an additional output 
	 *	buffer to capture any generated contents.
	 *
	 * @return string
	 *	The captured content.
	 */
	public final function run(array $parameters = null, bool $capture = false) : ?string
	{
		$ob;

		if ($capture)
		{
			$ob = ob_get_level();

			if (!ob_start())
			{
				throw new Exception('View output buffer can not start: unknown error');
			}
		}
		
		Lightbit::inclusion()->bindTo($this, null)($this->path, $parameters);

		if ($capture)
		{
			$result = '';

			while ($ob < ob_get_level())
			{
				$result .= ob_get_clean();
			}

			return $result;
		}

		return null;
	}

	/**
	 * Creates a widget.
	 *
	 * @param string $className
	 *	The widget class name.
	 *
	 * @param array $configuration
	 *	The widget configuration.
	 *
	 * @return IWidget
	 *	The widget.
	 */
	public function widget(string $className, array $configuration = null) : IWidget
	{
		return new $className($this->getContext(), $configuration);
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
}
