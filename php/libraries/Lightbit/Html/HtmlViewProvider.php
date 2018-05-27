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

use \Lightbit\Html\HtmlViewFactory;
use \Lightbit\Html\HtmlViewNotFoundException;

use \Lightbit\Html\IHtmlView;
use \Lightbit\Html\IHtmlViewFactory;

/**
 * HtmlViewProvider.
 *
 * @author Datapoint - Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HtmlViewProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var HtmlViewProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HtmlViewProvider
	 *	The singleton instance.
	 */
	public final static function getInstance() : HtmlViewProvider
	{
		return (self::$instance ?? (self::$instance = new HtmlViewProvider()));
	}

	/**
	 * The view factory.
	 *
	 * @var IHtmlViewFactory
	 */
	private $viewFactory;

	/**
	 * The view map.
	 *
	 * @var array
	 */
	private $viewMap;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->viewMap = [];
	}

	/**
	 * Gets a view.
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
	public final function getView(string $view) : IHtmlView
	{
		strpos($view, '://') || $view = ('views://' . $view);

		return ($this->viewMap[$view] ?? ($this->viewMap[$view] = $this->getViewFactory()->createView($view)));
	}

	/**
	 * Gets a view by preference.
	 *
	 * @throws HtmlViewFactoryException
	 *	Thrown when the view creation fails.
	 *
	 * @param string $views
	 *	The view resource identifiers.
	 *
	 * @return IHtmlView
	 *	The view.
	 */
	public final function getViewByPreference(string ...$views) : IHtmlView
	{
		if (!$views)
		{
			throw new ArgumentException(sprintf(
				'Argument exception, preferred views list can not be empty: at class, "%s", at function, "%s", at parameter "%s"',
				__CLASS__,
				__FUNCTION__,
				'views'
			));
		}

		$htmlViewNotFoundException = null;

		foreach ($views as $i => $view)
		{
			try
			{
				return $this->getView($view);
			}
			catch (HtmlViewNotFoundException $e)
			{
				$htmlViewNotFoundException ?? ($htmlViewNotFoundException = $e);
			}
		}

		throw $htmlViewNotFoundException;
	}

	/**
	 * Gets the view factory.
	 *
	 * @return IHtmlViewFactory
	 *	The view factory.
	 */
	public final function getViewFactory() : IHtmlViewFactory
	{
		return ($this->viewFactory ?? ($this->viewFactory = new HtmlViewFactory()));
	}

	/**
	 * Sets the view factory.
	 *
	 * @param IHtmlViewFactory $viewFactory
	 *	The view factory.
	 */
	public final function setViewFactory(IHtmlViewFactory $viewFactory) : void
	{
		$this->viewFactory = $viewFactory;
	}
}
