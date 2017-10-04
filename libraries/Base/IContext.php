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
use \Lightbit\Base\ControllerNotFoundException;
use \Lightbit\Base\IBase;
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IElement;
use \Lightbit\Base\IEnvironment;
use \Lightbit\Base\ModuleNotFoundException;
use \Lightbit\Base\View;
use \Lightbit\Data\Caching\ICache;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Exception;
use \Lightbit\Globalization\ILocale;
use \Lightbit\Globalization\IMessageSource;
use \Lightbit\Html\IHtmlAdapter;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpAssetManager;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;
use \Lightbit\IllegalStateException;
use \Lightbit\Security\Cryptography\IPasswordDigest;

/**
 * IContext.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IContext extends IBase
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
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the layout.
	 *
	 * @return View
	 *	The layout.
	 */
	public function getLayout() : ?View;

	/**
	 * Gets the locale.
	 *
	 * @return Locale
	 *	The locale.
	 */
	public function getLocale() : ILocale;

	/**
	 * Gets the namespace name.
	 *
	 * @return string
	 *	The namespace name.
	 */
	public function getNamespaceName() : string;

	/**
	 * Gets a module.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return Module
	 *	The module.
	 */
	public function getModule(string $id) : IModule;

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string;

	/**
	 * Gets the prefix.
	 *
	 * @return string
	 *	The prefix.
	 */
	public function getPrefix() : string;

	/**
	 * Gets the views base paths.
	 *
	 * @return string
	 *	The views base path.
	 */
	public function getViewsBasePath() : string;

	/**
	 * Checks a component availability.
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
	 * Resolves a route.
	 *
	 * @param array $route
	 *	The route.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve(?array $route) : IAction;

	/**
	 * Sets a component configuration.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function setComponentConfiguration(string $id, array $configuration) : void;

	/**
	 * Sets the components configuration.
	 *
	 * @param array $componentsConfiguration
	 *	The components configuration.
	 */
	public function setComponentsConfiguration(array $componentsConfiguration) : void;

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public function setLayout(?string $layout) : void;

	/**
	 * Sets the locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 */
	public function setLocale(string $id) : void;

	/**
	 * Sets a module configuration.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @param array $configuration
	 *	The module configuration.
	 */
	public function setModuleConfiguration(string $id, array $configuration) : void;

	/**
	 * Sets the modules configuration.
	 *
	 * @param array $modulesConfiguration
	 *	The modules configuration.
	 */
	public function setModulesConfiguration(array $modulesConfiguration) : void;
}