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

use \Lightbit;
use \Lightbit\Base\Element;
use \Lightbit\Base\IView;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\IO\FileSystem\Alias;

/**
 * View.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class View extends Element implements IView
{
	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $path, array $configuration = null)
	{
		$this->path = $path;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Gets the base path.
	 *
	 * @return string
	 *	The base path.
	 */
	public final function getBasePath() : string
	{
		static $basePath;

		if (!$basePath)
		{
			$basePath = dirname($this->path);
		}

		return $basePath;
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
	public final function render(string $view, array $parameters = null, bool $capture = false) : ?string
	{
		return $this->view((new Alias($view))->resolve('php', $this->getBasePath()))
			->run($parameters, $capture);
	}

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
	public final function run(array $parameters = null, bool $capture = false) : ?string
	{
		$ob;

		if ($capture)
		{
			$ob = ob_get_level();

			if (!ob_start())
			{
				throw new Exception('View output buffer can not start: unknown error');
			}
		}
		
		Lightbit::inclusion()->bindTo($this, null)($this->path, $parameters);

		if ($capture)
		{
			$result = '';

			while ($ob < ob_get_level())
			{
				$result .= ob_get_clean();
			}

			return $result;
		}

		return null;
	}

	/**
	 * Creates a view.
	 *
	 * @param string $path
	 *	The view path.
	 *
	 * @param array $configuration
	 *	The view configuration.
	 *
	 * @return IView
	 *	The view.
	 */
	protected function view(string $path, array $configuration = null) : IView
	{
		return new View($path, $configuration);
	}
}
