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

use \Lightbit\Base\Element;
use \Lightbit\IO\FileSystem\FileNotFoundException;

use \Lightbit\Base\IContext;
use \Lightbit\Base\IView;

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
	 * @type IContext
	 */
	private $context;
	
	/**
	 * The base path.
	 *
	 * @type string
	 */
	private $basePath;

	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The view context.
	 *
	 * @param string $path
	 *	The view path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(?IContext $context, string $path, array $configuration = null)
	{
		parent::__construct($context);

		$this->context = $context;
		$this->path = $path;

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
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
	 * Gets the base path.
	 *
	 * @return string
	 *	The base path.
	 */
	public final function getBasePath() : string
	{
		if (!$this->basePath)
		{
			$this->basePath = dirname($this->path);
		}

		return $this->basePath;
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
				throw new Exception(sprintf('View variable validation failure, %s: %s, at context %s', lcfirst($e->getMessage()), $this->path, $this->getContext()->getPrefix()));
			}

			if (!$result)
			{
				throw new Exception(sprintf('View variable validation failure: %s, at context %s', $this->path, $this->getContext()->getPrefix()));
			}
		}
	}

	/**
	 * Checks if the view is available.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isAvailable() : bool
	{
		return is_file($this->path);
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
		return $this->view(__asset_path_resolve($this->getBasePath(), 'php', $view))
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
				throw new Exception('Can not run view, output buffer start failure');
			}
		}

		if (!is_file($this->path))
		{
			throw new FileNotFoundException($this->path, sprintf('Can not run view, file not found: file %s', $this->path));
		}

		// During the view script inclusion, the context is meant to be the
		// one the view was originally created with.
		$context = __context_replace($this->context);
		__include_file_as_ex($this, $this->path, $parameters);
		__context_set($context);

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
	 * @param array $arguments
	 *	The widget constructor arguments.
	 *
	 * @return IWidget
	 *	The widget.
	 */
	public function widget(string $className, ...$arguments) : IWidget
	{
		return new $className(...$arguments);
	}

	/**
	 * Creates and instantly inflates an inline widget.
	 *
	 * @param string $className
	 *	The widget class name.
	 *
	 * @param array $arguments
	 *	The widget constructor arguments.
	 *
	 * @return string
	 *	The content.
	 */
	public function inflate(string $className, ...$arguments) : string
	{
		$widget = $this->widget($className, ...$arguments);

		if (! ($widget instanceof IWidgetInline))
		{
			throw new Exception(sprintf('Can not inflate inline widget: %s', $className));
		}

		return $widget->inflate();
	}
}
