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
use \Lightbit\Base\IElement;
use \Lightbit\Base\ITheme;

/**
 * IController.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IController
{
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
	public function __construct(IContext $context, string $id, array $configuration = null);

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
	public function display(string $id, array $parameters = null) : void;

	/**
	 * Gets a action method name.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @return string
	 *	The action method name.
	 */
	public function getActionMethodName(string $id) : string;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the theme.
	 *
	 * @return ITheme
	 *	The theme.
	 */
	public function getTheme() : ?ITheme;

	/**
	 * Gets a view.
	 *
	 * If the view identifier is not absolute, a theme is set and a matching
	 * view override exists, the override is returned instead of the default
	 * controller view.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public function getView(string $id) : IView;

	/**
	 * Gets the views path.
	 *
	 * @return string
	 *	The views path.
	 */
	public function getViewsPath() : string;

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
	public function render(string $id, array $parameters = null, bool $capture = false) : ?string;

	/**
	 * Generates a response based on an action result.
	 *
	 * It is invoked automatically during the action run procedure, after 
	 * and only if a value is returned by its implementation.
	 *
	 * @param mixed $result
	 *	The result.
	 */
	public function result($result) : void;

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