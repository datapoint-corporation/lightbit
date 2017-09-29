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
use \Lightbit\Base\Context;
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
	public function __construct(Context $context, string $id, array $configuration = null);

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
	public function getContext() : Context;

	/**
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string;

	/**
	 * Gets the layout.
	 *
	 * @return View
	 *	The layout.
	 */
	public function getLayout() : ?View;

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string;

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
	public function resolve(string $id, array $parameters) : Action;

	/**
	 * Runs an action.
	 *
	 * @param Action $action
	 *	The action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function run(Action $action); // : mixed
}