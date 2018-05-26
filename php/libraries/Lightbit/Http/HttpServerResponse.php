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

namespace Lightbit\Http;

use \Lightbit\Base\IView;
use \Lightbit\Html\IHtmlView;
use \Lightbit\Http\IHttpRequest;

final class HttpServerResponse implements IHttpResponse
{
	private static $instance;

	public static final function getInstance() : HttpServerResponse
	{
		return (self::$instance ?? (self::$instance = new HttpServerResponse()));
	}

	private function __construct()
	{

	}

	public final function reset() : void
	{
		for ($i = ob_get_level(); $i > 1; --$i)
		{
			ob_end_clean();
		}

		ob_clean();
		header_remove();
	}

	public final function render(IView $view, array $variables = null) : void
	{
		$content = $view->render($variables);

		if ($view instanceof IHtmlView)
		{
			$this->setHeaderMap([
				'Content-Length' => strlen($content),
				'Content-Type' => 'text/html; charset=utf-8'
			]);
		}

		echo $content;
	}

	public final function setContentLength(int $contentLength) : void
	{
		$this->setHeader('Content-Length', $contentLength);
	}

	public final function setContentType(string $contentType) : void
	{
		$this->setHeader('Content-Type', $contentType);
	}

	public final function setHeader(string $name, string $content) : void
	{
		header(strtr(trim($name), [ ':' => '' ]) . ': ' . trim($content), true);
	}

	public final function setHeaderMap(array $headerMap) : void
	{
		foreach ($headerMap as $name => $content)
		{
			$this->setHeader($name, $content);
		}
	}
}
