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
use \Lightbit\Base\IApplication;
use \Lightbit\Base\IContainer;
use \Lightbit\Base\Object;
use \Lightbit\Base\Plugin;
use \Lightbit\Helpers\ObjectHelper;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Plugin extends Element implements IPlugin
{
	/**
	 * The container.
	 *
	 * @type IContainer
	 */
	private $container;

	/**
	 * The controllers.
	 *
	 * @type array
	 */
	private $controllers;

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
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param IContainer $container
	 *	The container.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param string $path
	 *	The path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(IContainer $container, string $id, string $path, array $configuration = null)
	{
		$this->container = $container;
		$this->id = $id;
		$this->path = $path;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Creates a controller class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	protected function controllerClassName(string $id) : string
	{
		return $controllersClassName[$id]
			= $this->getNamespaceName()
			. '\\Controllers\\'
			. strtr(ucwords(strtr($id, [ '-' =>  ' ', '/' => ' \\ '])), [ ' ' => '' ])
			. 'Controller';
	}

	/**
	 * Gets the container.
	 *
	 * @return IContainer
	 *	The container.
	 */
	public function getContainer() : IContainer
	{
		return $this->container;
	}

	/**
	 * Gets a controller class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public final function getControllerClassName(string $id) : string
	{
		static $controllersClassName = [];

		if (!isset($controllersClassName[$id]))
		{
			$controllersClassName[$id] = $this->controllerClassName($id);
		}

		return $controllersClassName[$id];
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
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
		if (!$this->layoutPath && $this->layout)
		{
			$this->layoutPath = (new Alias($this->layout))->resolve('php', $this->getPath());
		}

		return $this->layoutPath;
	}

	/**
	 * Gets the namespace name.
	 *
	 * @return string
	 *	The namespace name.
	 */
	public final function getNamespaceName() : string
	{
		static $namespaceName;

		if (!$namespaceName)
		{
			if (static::class === Plugin::class)
			{
				$namespaceName
					= $this->getApplication()->getNamespaceName()
					. '\\'
					. strtr(ucwords(strtr($this->id, [ '-' =>  ' ', '/' => ' / '])), [ ' ' => '', '/', '\\' ]);
			}
			else
			{
				$namespaceName = Lightbit::getClassNamespaceName(static::class);
			}
		}

		return $namespaceName;
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
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public function getViewsBasePaths() : array
	{
		echo 'aaa';
		$viewPaths = $this->container->getViewsBasePaths();
		$viewPaths[] = $this->getPath() . DIRECTORY_SEPARATOR . 'views';

		return $viewPaths;
	}

	/**
	 * Checks a controller availability.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The check result.
	 */
	public final function hasController(string $id) : bool
	{
		return Lightbit::hasClass($this->getControllerClassName($id));
	}

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public final function setLayout(string $layout) : void
	{
		$this->layout = $layout;
		$this->layoutPath = null;
	}
}
