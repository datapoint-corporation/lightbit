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

use \Lightbit\Base\IAction;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IElement;

/**
 * IController.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IController extends IElement
{
	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The controller context.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null);

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
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public function getContext() : IContext;

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
	 * Gets a view.
	 *
	 * @param string $view
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public function getView(string $view) : IView;

	/**
	 * Resolves to an action.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 *
	 * @return Action
	 *	The action.
	 */
	public function resolve(string $id, array $parameters) : IAction;

	/**
	 * Generates the applicable error response.
	 *
	 * This method is invoked automatically by the lightbit global exception
	 * and error handlers when an uncaught exception is thrown.
	 *
	 * If the error response is generated, this function should return false
	 * in order to prevent escalation and, at the end, the default behaviour.
	 *
	 * @param Throwable $throwable
	 *	The uncaught throwable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function throwable(\Throwable $throwable) : bool;

	/**
	 * Runs an action.
	 *
	 * @param IAction $action
	 *	The action.
	 *
	 * @return int
	 *	The result.
	 */
	public function run(IAction $action) : int;
}