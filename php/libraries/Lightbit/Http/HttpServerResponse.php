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

use \Lightbit\Rendering\ViewRenderException;

use \Lightbit\Html\Rendering\IHtmlView;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Rendering\IView;

/**
 * HttpServerResponse.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HttpServerResponse implements IHttpResponse
{
	/**
	 * The singleton instance.
	 *
	 * @var HttpServerResponse
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HttpServerResponse
	 *	The singleton instance.
	 */
	public static final function getInstance() : HttpServerResponse
	{
		return (self::$instance ?? (self::$instance = new HttpServerResponse()));
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Clears the output buffer and removes any previously set headers,
	 * reverting the response to its initial state.
	 */
	public final function reset() : void
	{
		for ($i = ob_get_level(); $i > 1; --$i)
		{
			ob_end_clean();
		}

		ob_clean();
		header_remove();
	}

	/**
	 * Render a view, setting the response headers according to the view type
	 * and its content.
	 *
	 * @throws ViewRenderException
	 *	Thrown when the view rendering fails.
	 *
	 * @param IView $view
	 *	The view.
	 *
	 * @param array $variableMap
	 *	The view variable map.
	 */
	public final function render(IView $view, array $variableMap = null) : void
	{
		$content = $view->render($variableMap);

		if ($view instanceof IHtmlView)
		{
			$this->setHeaderMap([
				'Content-Length' => strlen($content),
				'Content-Type' => 'text/html; charset=utf-8'
			]);
		}

		echo $content;
	}

	/**
	 * Sets the content length.
	 *
	 * @param int $contentLength
	 *	The content length.
	 */
	public final function setContentLength(int $contentLength) : void
	{
		if (-1 < $contentLength)
		{
			$this->setHeader('Content-Length', $contentLength);
		}
	}

	/**
	 * Sets the content type.
	 *
	 * @param string $contentType
	 *	The content type.
	 */
	public final function setContentType(string $contentType) : void
	{
		$this->setHeader('Content-Type', $contentType);
	}

	/**
	 * Sets a header.
	 *
	 * @param string $name
	 *	The header name.
	 *
	 * @param string $content
	 *	The header content.
	 */
	public final function setHeader(string $name, string $content) : void
	{
		header(strtr(trim($name), [ ':' => '' ]) . ': ' . trim($content), true);
	}

	/**
	 * Sets multiple headers.
	 *
	 * @param array $headerMap
	 *	The header map.
	 */
	public final function setHeaderMap(array $headerMap) : void
	{
		foreach ($headerMap as $name => $content)
		{
			$this->setHeader($name, $content);
		}
	}
}
