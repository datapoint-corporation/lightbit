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

use \Lightbit\Action;
use \Lightbit\Base\IContainer;
use \Lightbit\Base\IPlugin;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpRouter;

/**
 * IApplication.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IApplication extends IContainer
{
	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $path, array $configuration = null);

	/**
	 * Disposes the application by closing any resources started through it
	 * in proper order, due to their dependencies.
	 */
	public function dispose() : void;

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public function getHttpRequest() : IHttpRequest;

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter;

	/**
	 * Gets the layout.
	 *
	 * @return string
	 *	The layout.
	 */
	public function getLayout() : ?string;

	/**
	 * Gets the layout path.
	 *
	 * @return string
	 *	The layout path.
	 */
	public function getLayoutPath() : ?string;

	/**
	 * Gets a plugin.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	public function getPlugin(string $id) : IPlugin;

	/**
	 * Gets the plugins.
	 *
	 * @return array
	 *	The plugins.
	 */
	public function getPlugins() : array;

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public function getSlugManager() : ISlugManager;

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
	 * Checks a plugin availability.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPlugin(string $id) : bool;

	/**
	 * Resolves a route to a controller action.
	 *
	 * @param array $route
	 *	The route to resolve.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve(array $route = null) : Action;

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The status code.
	 */
	public function run() : int;

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public function setLayout(?string $layout) : void;

	/**
	 * Disposes the application by closing any resources started through it
	 * in proper order, due to their dependencies, before terminating the
	 * script execution.
	 *
	 * @param int $status
	 *	The exit status code.
	 */
	public function terminate(int $status) : void;
}
