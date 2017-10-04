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

use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;

/**
 * IAction.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IAction
{
	/**
	 * Compares to a route.
	 *
	 * Comparison is based on the resolution of the given route through the
	 * context this action is attached to.
	 *
	 * The following list of integers can be returned:
	 *
	 *	0)	The route resolves to a different controller, either within the
	 *		same context or at one of the parent modules.
	 *
	 *	1)	The route resolves to the same context and controller with a
	 *		a matching action name - parameters may differ.
	 *
	 *	2)	The route resolves to an action within the same context and
	 *		controller.
	 *
	 *	3)	The route resolves to an action in a different controller, that
	 *		may be part of either the same context or a child of it.
	 *
	 * @param array $route
	 *	The route to compare against.
	 *
	 * @return int
	 *	The result.
	 */
	public function compare(array $route) : int;

	/**
	 * Gets the arguments.
	 *
	 * @return array
	 *	The arguments.
	 */
	public function getArguments() : array;

	/**
	 * Gets the parameters.
	 *
	 * @return array
	 *	The parameters.
	 */
	public function getParameters() : array;

	/**
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public function getContext() : IContext;

	/**
	 * Gets the controller.
	 *
	 * @return IController
	 *	The controller.
	 */
	public function getController() : IController;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

	/**
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string;

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public function getName() : string;

	/**
	 * Runs the action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function run();
}
