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

/**
 * IContainer.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IController
{
	/**
	 * Constructor.
	 *
	 * @param IContainer $container
	 *	The container.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(IContainer $container, string $id, array $configuration = null);

	/**
	 * Displays a view.
	 *
	 * @param string $view
	 *	The view file system alias.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 */
	public function display(string $view, array $parameters = null) : void;

	/**
	 * Gets an action method name.
	 *
	 * @param string $action
	 *	The action name.
	 *
	 * @return string
	 *	The action method name.
	 */
	public function getActionMethodName(string $action) : string;

	/**
	 * Gets the container.
	 *
	 * @return IContainer
	 *	The container.
	 */
	public function getContainer() : IContainer;

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
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public function getViewsBasePaths() : array;

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
	public function render(string $view, array $parameters = null, bool $capture = false) : ?string;

	/**
	 * Resolves an action.
	 *
	 * @param string $name
	 *	The action name.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 *
	 * @return Action
	 *	The result.
	 */
	public function resolve(string $action, array $parameters) : Action;

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public function setLayout(?string $layout) : void;
}
