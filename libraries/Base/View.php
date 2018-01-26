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
use \Lightbit\Base\Element;
use \Lightbit\Base\IView;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\IO\FileSystem\Asset;
use \Lightbit\Script;

/**
 * View.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class View extends Element implements IView
{
	/**
	 * The controller.
	 *
	 * @var IController
	 */
	private $controller;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param IController $controller
	 *	The view controller.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @param string $path
	 *	The view path.
	 */
	public function __construct(IController $controller, string $id, string $path)
	{
		parent::__construct();

		$this->controller = $controller;
		$this->id = $id;
		$this->path = $path;
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext
	{
		return $this->controller->getContext();
	}

	/**
	 * Gets the controller.
	 *
	 * @return IController
	 *	The controller.
	 */
	public final function getController() : IController
	{
		return $this->controller;
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
	 * @param string $id
	 *	The view identifier.
	 *
	 * @return IView
	 *	The view.
	 */
	public function getView(string $id) : IView
	{
		return new View
		(
			$this->controller,
			$id,
			((new Asset(dirname($this->path), $id))->getPath())
		);
	}

	/**
	 * Renders a view.
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
	public function render(string $id, array $parameters = null, bool $capture = false) : ?string
	{
		return $this->getView($id)->run($parameters, $capture);
	}

	/**
	 * Runs the view.
	 *
	 * @param array $parameters
	 *	The execution parameters.
	 *
	 * @param bool $capture
	 *	The execution output capturing flag.
	 *
	 * @return string
	 *	The output, if captured.
	 */
	public final function run(array $parameters = null, bool $capture = false) : ?string
	{
		$buffer;

		if ($capture)
		{
			$buffer = ob_get_level();

			if (!ob_start())
			{
				throw new ViewOutputBufferException($this, sprintf('Can not run view, output buffer startup failure: "%s"', $this->id));
			}
		}

		$lightbit = Lightbit::getInstance();
		$context = $lightbit->setContext($this->getContext());
		(new Script($this->path))->include($this, $parameters);
		$lightbit->setContext($context);

		if ($capture)
		{
			$result = '';

			while ($buffer < ob_get_level())
			{
				$result .= ob_get_clean();
			}

			return $result;
		}

		return null;
	}
}