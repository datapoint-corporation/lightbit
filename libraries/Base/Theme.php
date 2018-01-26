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

use \Lightbit;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Base\IView;
use \Lightbit\IO\FileSystem\Asset;

/**
 * ITheme.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Theme implements ITheme
{
	/**
	 * The context.
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The layout.
	 *
	 * @var string
	 */
	private $layout;

	/**
	 * The path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The theme contextt.
	 *
	 * @param string $id
	 *	The theme identifier.
	 *
	 * @param string $path
	 *	The theme path.
	 */
	public function __construct(IContext $context, string $id, string $path)
	{
		$this->context = $context;
		$this->id = $id;
		$this->layout = 'main';
		$this->path = $path;
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : IContext
	{
		return $this->context;
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public final function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Gets a view.
	 *
	 * @param IController $controller
	 *	The view controller.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public function getView(IController $controller, string $id) : ?IView
	{
		$base = '';
		$context = $controller->getContext();

		while ($context !== $this->context)
		{
			$base = $context->getID() . DIRECTORY_SEPARATOR . $base;
			$context = $context->getContext();
		}
		
		$base = $this->getViewsPath() 
			. DIRECTORY_SEPARATOR
			. $base
			. strtr($controller->getID(), [ '/' => DIRECTORY_SEPARATOR ]);

		$path = (new Asset($base, $id))->getPath();

		if (is_file($path))
		{
			return new ThemeView($this, $controller, $id, $path);
		}

		return null;
	}

	/**
	 * Gets the views path.
	 *
	 * @return string
	 *	The views path.
	 */
	public function getViewsPath() : string
	{
		return $this->path . DIRECTORY_SEPARATOR . 'views';
	}

	/**
	 * Runs a view through the theme.
	 *
	 * @param IView $view
	 *	The view.
	 *
	 * @param array $parameters
	 *	The view parameters.
	 */
	public function run(IView $view, array $parameters = null) : void
	{
		(
			new Layout
			(
				$this, 
				$view->getController(), 
				$this->layout, 
				(new Asset($this->path, $this->layout))->getPath()
			)
		)
		
		->run([ 'content' => $view->run($parameters, true) ]);
	}
}