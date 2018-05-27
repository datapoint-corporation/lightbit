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

namespace Lightbit\Html;

use \Lightbit;
use \Lightbit\Html\HtmlView;
use \Lightbit\Html\HtmlViewFactoryException;

use \Lightbit\Html\IHtmlView;
use \Lightbit\Html\IHtmlViewFactory;

/**
 * HtmlViewFactory.
 *
 * @author Datapoint - Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlViewFactory implements IHtmlViewFactory
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Creates a view.
	 *
	 * @throws HtmlViewFactoryException
	 *	Thrown when the view creation fails.
	 *
	 * @param string $view
	 *	The view resource identifier.
	 *
	 * @return IHtmlView
	 *	The view.
	 */
	public final function createView(string $view) : IHtmlView
	{
		strpos($view, '://') || ($view = 'views://' . $view);

		if ($path = Lightbit::getInstance()->getResourcePath('php', $view))
		{
			return new HtmlView($view, $path);
		}

		throw new HtmlViewFactoryException($this, sprintf(
			'Can not get html view, not found: "%s"',
			$view
		));
	}
}
