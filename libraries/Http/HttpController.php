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

namespace Lightbit\Http;

use \Lightbit\Base\Controller;
use \Lightbit\Base\Context;
use \Lightbit\Base\IView;
use \Lightbit\Data\IModel;
use \Lightbit\Html\HtmlView;
use \Lightbit\Http\IHttpController;

/**
 * HttpController.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class HttpController extends Controller implements IHttpController
{
	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The context.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(Context $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, $configuration);
	}

	/**
	 * Exports the current http request.
	 *
	 * @param string $method
	 *	The http request method.
	 *
	 * @param IModel $model
	 *	The http request model.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function export(string $method, IModel ...$model) : bool
	{
		$request = $this->getHttpRequest();

		if ($request->isOfMethod($method))
		{
			$result = true;

			foreach ($model as $i => $subject)
			{
				if (!$request->export($subject))
				{
					$result = false;
				}
			}

			return $result;
		}

		return false;
	}

	/**
	 * Sets a response redirection.
	 *
	 * @param array $route
	 *	The response redirection route.
	 *
	 * @param int $statusCode
	 *	The response redirection status code.
	 */
	public final function redirect(array $route, int $statusCode = 303) : void
	{
		$response = $this->getHttpResponse();
		$response->setHeader('Location', $this->getHttpRouter()->url($route, true));
		$response->setStatusCode($statusCode);

		$this->getApplication()->terminate();
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
		return new HtmlView($this->getContext(), $path, $configuration);
	}
}