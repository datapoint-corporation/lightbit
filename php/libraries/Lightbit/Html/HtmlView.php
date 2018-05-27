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

use \Throwable;

use \Lightbit;
use \Lightbit\BufferException;
use \Lightbit\Html\HtmlViewRenderException;

use \Lightbit\Html\IHtmlView;

class HtmlView implements IHtmlView
{
	private $id;

	private $path;

	private $scope;

	public function __construct(string $id, string $path)
	{
		$this->id = $id;
		$this->path = $path;
		$this->scope = new HtmlViewScope($this);
	}

	public function render(array $variables = null) : string
	{
		if (!ob_start())
		{
			throw new BufferException(sprintf(
				'Can not start view output buffer: "%s"',
				$this->id
			));
		}

		$buffer = ob_get_level();

		try
		{
			Lightbit::getInstance()->includeAs(
				$this->scope,
				$this->path,
				$variables
			);
		}
		catch (Throwable $e)
		{
			for ($i = ob_get_level(); $i > $buffer; --$i)
			{
				ob_end_flush();
			}

			ob_end_clean();

			throw new HtmlViewRenderException(
				$this,
				sprintf('Can not render view, uncaught throwable: "%s"', $this->id),
				$e
			);
		}

		for ($i = ob_get_level(); $i > $buffer; --$i)
		{
			ob_end_flush();
		}

		return ob_get_clean();
	}
}
