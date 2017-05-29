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
use \Lightbit\Base\ICluster;
use \Lightbit\Base\IController;
use \Lightbit\ControllerNotFoundException;

/**
 * Cluster.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Cluster extends Object implements ICluster
{
	/**
	 * The context.
	 *
	 * @type IContext
	 */
	private $context;

	/**
	 * The controllers.
	 *
	 * @type array
	 */
	private $controllers;

	/**
	 * The controllers configuration.
	 *
	 * @type array
	 */
	private $controllersConfiguration;

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
	 *	The context.
	 */
	protected function __construct(IContext $context, string $path)
	{
		$this->controllers = [];
		$this->controllersConfiguration = [];

		$this->context = $context;

		$this->path = $path;
	}

	/**
	 * Creates a controller default class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	protected function controllerClassName(string $id) : string
	{
		return $this->getNamespaceName()
			. '\\Controllers\\' 
			. strtr(ucwords(strtr($id, [ '/' => ' \\ ', '-' => ' ' ])), [ ' ' => '' ])
			. 'Controller';
	}

	/**
	 * Gets a controller.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return IController
	 *	The controller.
	 */
	public final function getController(string $id) : IController
	{
		if (!isset($this->controllers[$id]))
		{
			if (!$this->hasController($id))
			{
				throw new ControllerNotFoundException($this, $id, sprintf('Controller not found: "%s", at context "%s"', $id, $this->context->getPrefix()));
			}

			$className = $this->getControllerClassName($id);

			return $this->controllers[$id] = new $className
			(
				$this, 
				$id, 
				(isset($this->controllersConfiguration[$id]) ? $configuration : null)
			);
		}

		return $this->controllers[$id];
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
		static $results = [];

		if (!isset($results[$id]))
		{
			$results[$id] = $this->controllerClassName($id);
		}

		return $results[$id];
	}

	/**
	 * Gets the namespace name.
	 *
	 * @return string
	 *	The namespace name.
	 */
	public final function getNamespaceName() : string
	{
		static $result;

		if (!$result)
		{
			$result = Lightbit::getClassNamespaceName(static::class);
		}

		return $result;
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
	 * Checks a controller availability.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasController(string $id) : bool
	{
		static $results = [];

		if (!isset($results[$id]))
		{
			return $results[$id] = Lightbit::hasClass($this->getControllerClassName($id));
		}

		return $results[$id];
	}

	/**
	 * Sets a controller configuration.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @param array $configuration
	 *	The controller configuration.
	 *
	 * @param bool $merge
	 *	The controllers configuration merge flag.
	 */
	public final function setControllerConfiguration(string $id, array $configuration, bool $merge = true) : void
	{
		$this->controllersConfiguration[$id] 
			= ($merge && isset($this->controllersConfiguration[$id])) 
			? array_replace_recursive($this->controllersConfiguration[$id], $configuration)
			: $configuration;
	}

	/**
	 * Sets the controllers configuration.
	 *
	 * @param array $controllersConfiguration
	 *	The controllers configuration.
	 *
	 * @param bool $merge
	 *	The controllers configuration merge flag.
	 */
	public final function setControllersConfiguration(array $controllersConfiguration, bool $merge = true) : void
	{
		foreach ($controllersConfiguration as $id => $configuration)
		{
			$this->setControllerConfiguration($id, $configuration, $merge);
		}
	}
}