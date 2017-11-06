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

use \Lightbit\Exception;
use \Lightbit\Base\Element;
use \Lightbit\Base\IWidget;
use \Lightbit\IO\FileSystem\FileNotFoundException;

/**
 * View.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IView extends IElement
{
	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The view context.
	 *
	 * @param string $path
	 *	The view path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(?IContext $context, string $path, array $configuration = null);

	/**
	 * Gets the base path.
	 *
	 * @return string
	 *	The base path.
	 */
	public function getBasePath() : string;

	/**
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public function getContext() : IContext;

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string;

	/**
	 * Imports a variable.
	 *
	 * @param mixed $variable
	 *	The variable to import.
	 *
	 * @param mixed $default
	 *	The variable default value.
	 *
	 * @param Closure $closure
	 *	The variable validation closure.
	 */
	public function import(&$variable, \Closure $closure = null) : void;

	/**
	 * Checks if the view is available.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAvailable() : bool;

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
	 * Runs the view.
	 *
	 * @param array $parameters
	 *	The parameters.
	 *
	 * @param bool $capture
	 *	The capture flag which, when set, will use an additional output
	 *	buffer to capture any generated contents.
	 *
	 * @return string
	 *	The captured content.
	 */
	public function run(array $parameters = null, bool $capture = false) : ?string;

	/**
	 * Creates a widget.
	 *
	 * @param string $className
	 *	The widget class name.
	 *
	 * @param array $arguments
	 *	The widget constructor arguments.
	 *
	 * @return IWidget
	 *	The widget.
	 */
	public function widget(string $className, ...$arguments) : IWidget;

	/**
	 * Creates and instantly inflates an inline widget.
	 *
	 * @param string $className
	 *	The widget class name.
	 *
	 * @param array $arguments
	 *	The widget constructor arguments.
	 *
	 * @return string
	 *	The content.
	 */
	public function inflate(string $className, ...$arguments) : string;
}
