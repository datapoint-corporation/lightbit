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

use \Lightbit\Base\IComponent;
use \Lightbit\Base\IController;

/**
 * IContext.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IContext
{
	/**
	 * Disposes the context.
	 */
	public function dispose() : void;

	/**
	 * Gets a component.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return IComponent
	 *	The component.
	 */
	public function getComponent(string $id) : IComponent;

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : ?IContext;

	/**
	 * Gets a controller.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return IController
	 *	The controller.
	 */
	public function getController(string $id) : IController;

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets a module.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return IModule
	 *	The module.
	 */
	public function getModule(string $id) : IModule;

	/**
	 * Gets the namespace name.
	 *
	 * @return string
	 *	The namespace name.
	 */
	public function getNamespaceName() : string;

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string;

	/**
	 * Gets a plugin.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	public function getPlugin(string $id) : IPlugin;

	/**
	 * Checks for a component availability.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasComponent(string $id) : bool;

	/**
	 * Checks a controller availability.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasController(string $id) : bool;

	/**
	 * Checks for a module availability.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasModule(string $id) : string;

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
	public function setControllerConfiguration(string $id, array $configuration, bool $merge = true) : void;

	/**
	 * Sets the controllers configuration.
	 *
	 * @param array $controllersConfiguration
	 *	The controllers configuration.
	 *
	 * @param bool $merge
	 *	The controllers configuration merge flag.
	 */
	public function setControllersConfiguration(array $controllersConfiguration, bool $merge = true) : void;

	/**
	 * Sets a module configuration.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @param array $configuration
	 *	The module configuration.
	 *
	 * @param bool $merge
	 *	The module configuration merge flag.
	 */
	public function setModuleConfiguration(string $id, array $configuration, bool $merge = true) : void;

	/**
	 * Sets the modules configuration.
	 *
	 * @param array $modulesConfiguration
	 *	The modules configuration.
	 *
	 * @param bool $merge
	 *	The module configuration merge flag.
	 */
	public function setModulesConfiguration(array $modulesConfiguration, bool $merge = true) : void;

	/**
	 * Sets a plugin configuration.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @param array $configuration
	 *	The plugin configuration.
	 *
	 * @param bool $merge
	 *	The plugin configuration merge flag.
	 */
	public function setPluginConfiguration(string $id, array $configuration, bool $merge = true) : void;

	/**
	 * Sets the plugins configuration.
	 *
	 * @param array $modulesConfiguration
	 *	The plugins configuration.
	 *
	 * @param bool $merge
	 *	The plugins configuration merge flag.
	 */
	public function setPluginsConfiguration(array $modulesConfiguration, bool $merge = true) : void;
}