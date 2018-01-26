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

use \Throwable;

use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Base\IModule;
use \Lightbit\Cli\ICliRouter;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Html\IHtmlComposer;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Routing\Action;

/**
 * IContext.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IContext
{
	/**
	 * Disposes the context.
	 *
	 * It disposes each module, followed by each component, in reverse order
	 * as they were first accessed, before disposing any of its own resources.
	 */
	public function dispose() : void;

	/**
	 * Gets the command line interface router.
	 *
	 * @return ICliRouter
	 *	The command line interface router.
	 */
	public function getCliRouter() : ICliRouter;

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
	 * Gets a controller class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public function getControllerClassName(string $id) : string;

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
	 * Gets the default action.
	 *
	 * @return Action
	 *	The default action.
	 */
	public function getDefaultAction() : Action;

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array;

	/**
	 * Gets the file cache.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public function getFileCache() : IFileCache;

	/**
	 * Gets the html composer.
	 *
	 * @return IHtmlComposer
	 *	The html composer.
	 */
	public function getHtmlComposer() : IHtmlComposer;

	/**
	 * Gets the html document.
	 *
	 * @return IHtmlDocument
	 *	The html document.
	 */
	public function getHtmlDocument() : IHtmlDocument;

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public function getHttpRequest() : IHttpRequest;

	/**
	 * Gets the http response.
	 *
	 * @return IHttpRequest
	 *	The http response.
	 */
	public function getHttpResponse() : IHttpResponse;

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the memory cache.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public function getMemoryCache() : IMemoryCache;

	/**
	 * Gets the messages path.
	 *
	 * @return string
	 *	The messages path.
	 */
	public function getMessagesPath() : string;

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
	 * Gets the modules.
	 *
	 * @return array
	 *	The modules.
	 */
	public function getModules() : array;

	/**
	 * Gets the modules path.
	 *
	 * @return string
	 *	The modules path.
	 */
	public function getModulesPath() : string;

	/**
	 * Gets the network cache.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public function getNetworkCache() : INetworkCache;

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string;

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection;

	/**
	 * Gets the theme.
	 *
	 * @return ITheme
	 *	The theme.
	 */
	public function getTheme() : ?ITheme;

	/**
	 * Gets the themes path.
	 *
	 * @return string
	 *	The themes path.
	 */
	public function getThemesPath() : string;

	/**
	 * Gets the views path.
	 *
	 * @return string
	 *	The views path.
	 */
	public function getViewsPath() : string;

	/**
	 * Checks if a controller is available.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasController(string $id) : bool;

	/**
	 * Checks if a component is available.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasComponent(string $id) : bool;

	/**
	 * Checks if a module is available.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasModule(string $id) : bool;

	/**
	 * Resolves a route.
	 *
	 * A route is represented through a hybrid array holding a zero indexed
	 * action identifier and the parameters matching the criteria imposed by
	 * the action method signature.
	 *
	 * If a route is not provided, or the action identifier is missing, the
	 * default route will be used as applicable.
	 *
	 * @param array $route
	 *	The route to resolve.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve(?array $route) : Action;

	/**
	 * Sets the theme.
	 *
	 * @param string $id
	 *	The theme identifier.
	 */
	public function setTheme(?string $id) : void;

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
	public function throwable(Throwable $throwable) : bool;
}