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

namespace Lightbit\Rendering\Php;

use \Lightbit\Rendering\Php\IPhpView;

/**
 * PhpViewScope.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class PhpViewScope
{
	/**
	 * The base view.
	 *
	 * @var IPhpView
	 */
	private $baseView;

	/**
	 * The view.
	 *
	 * @var IPhpView
	 */
	private $view;

	/**
	 * Constructor.
	 *
	 * @param string $view
	 *	The view scope view.
	 */
	public function __construct(IPhpView $view)
	{
		$this->view = $view;
	}

	/**
	 * Gets the base view.
	 *
	 * @return IPhpView
	 *	The base view.
	 */
	public final function getBaseView() : ?IPhpView
	{
		return $this->baseView;
	}

	/**
	 * Sets the base view.
	 *
	 * @param string $view
	 *	The base view identifier.
	 */
	public final function inherit(string $view) : void
	{
		$this->baseView = $this->view->createBaseView($view);
	}

	/**
	 * Renders a view.
	 *
	 * @param string $view
	 *	The view identifier.
	 *
	 * @param array $variables
	 *	The view variables.
	 *
	 * @return string
	 *	The content.
	 */
	public final function render(string $view, array $variables = null) : string
	{
		return ViewProvider::getInstance()->getView($view)->render($variables);
	}
}
