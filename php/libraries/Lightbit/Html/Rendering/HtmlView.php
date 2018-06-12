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

namespace Lightbit\Html\Rendering;

use \Throwable;

use \Lightbit;
use \Lightbit\BufferException;
use \Lightbit\Html\Rendering\HtmlViewRenderException;

use \Lightbit\Html\Rendering\IHtmlView;

/**
 * HtmlView.
 *
 * @author Datapoint - Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlView implements IHtmlView
{
	/**
	 * The base view.
	 *
	 * @var IHtmlView
	 */
	private $baseView;

	/**
	 * The base view variable map.
	 *
	 * @var array
	 */
	private $baseViewVariableMap;

	/**
	 * The file path.
	 *
	 * @var string
	 */
	private $filePath;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The scope.
	 *
	 * @var HtmlViewScope
	 */
	private $scope;

	/**
	 * Constructor.
	 *
	 * @param string $id
	 *	The view identifier.
	 *
	 * @param string $filePath
	 *	The view file path.
	 */
	public function __construct(string $id, string $filePath)
	{
		$this->filePath = $filePath;
		$this->id = $id;
		$this->scope = new HtmlViewScope($this);
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
	 * Renders the view.
	 *
	 * @throws HtmlViewRenderException
	 *	Thrown when the view rendering fails.
	 *
	 * @param array $variableMap
	 *	The view variable map.
	 *
	 * @return string
	 *	The view content.
	 */
	public final function render(array $variables = null) : string
	{
		if (!ob_start())
		{
			throw new HtmlViewRenderException($this, sprintf(
				'Can not start view output buffer: "%s"',
				$this->id
			));
		}

		$bufferLevel = ob_get_level();

		try
		{
			Lightbit::getInstance()->includeAs(
				$this->scope,
				$this->filePath,
				$variables
			);
		}
		catch (Throwable $e)
		{
			for ($i = ob_get_level(); $i > $bufferLevel; --$i)
			{
				ob_end_flush();
			}

			ob_end_clean();

			throw new HtmlViewRenderException(
				$this,
				sprintf(
					'Can not render view, uncaught throwable: "%s"',
					$this->id
				),
				$e
			);
		}

		for ($i = ob_get_level(); $i > $bufferLevel; --$i)
		{
			ob_end_flush();
		}

		$content = ob_get_clean();

		if ($this->baseView)
		{
			try
			{
				$content = $this->baseView->render(
					[ 'content' => $content ] +
					($this->baseViewVariableMap ?? [])
				);
			}
			catch (Throwable $e)
			{
				throw new HtmlViewRenderException(
					$this,
					sprintf(
						'Can not render base view, uncaught throwable: "%s"',
						$this->baseView->getID()
					),
					$e
				);
			}

		}

		return $content;
	}

	/**
	 * Sets the base view.
	 *
	 * @throws HtmlViewFactoryException
	 *	Thrown when the view creation fails.
	 *
	 * @param IHtmlView $baseView
	 *	The base view.
	 *
	 * @param array $baseViewVariableMap
	 *	The base view variable map.
	 */
	public final function setBaseView(?IHtmlView $baseView, ?array $baseViewVariableMap) : void
	{
		$this->baseView = $baseView;
		$this->baseViewVariableMap = ($baseView ? $baseViewVariableMap : null);
	}
}
