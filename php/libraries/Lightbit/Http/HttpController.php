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

use \Lightbit\Html\Rendering\HtmlViewProvider;
use \Lightbit\Http\Routing\HttpRouterProvider;

use \Lightbit\Http\Routing\IHttpAction;
use \Lightbit\Http\Routing\IHttpRouter;
use \Lightbit\Http\IHttpController;

/**
 * HttpController.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
abstract class HttpController implements IHttpController
{
	/**
	 * The action.
	 *
	 * @var IHttpAction
	 */
	private $action;

	/**
	 * Constructor.
	 *
	 * @param IHttpAction $action
	 *	The action.
	 */
	public function __construct(IHttpAction $action)
	{
		$this->action = $action;
	}

	/**
	 * Gets the context.
	 *
	 * @return IHttpContext
	 *	The context.
	 */
	public final function getContext() : IHttpContext
	{
		return $this->action->getContext();
	}

	/**
	 * Gets the response.
	 *
	 * @return IHttpResponse
	 *	The response.
	 */
	protected final function getResponse() : IHttpResponse
	{
		return $this->action->getContext()->getResponse();
	}

	/**
	 * Gets the router.
	 *
	 * @return IHttpRouter
	 *	The router.
	 */
	protected final function getRouter() : IHttpRouter
	{
		return HttpRouterProvider::getInstance()->getRouter();
	}

	/**
	 * Creates a redirect response.
	 *
	 * @param string $controllerClassName
	 *	The route controller class name.
	 *
	 * @param string $controllerMethodName
	 *	The route controller method name.
	 *
	 * @param array $argumentMap
	 *	The route argument map.
	 */
	protected final function redirect(string $controllerClassName, string $controllerMethodName, array $argumentMap = null) : void
	{
		$response = $this->getResponse();
		$router = $this->getRouter();

		$response->setStatusCode(303);
		$response->setHeader(
			'Location',
			$router->createUrl(
				'GET',
				$controllerClassName,
				$controllerMethodName,
				$argumentMap
			)
		);
	}

	/**
	 * Renders an html view.
	 *
	 * @throws HtmlViewFactoryException
	 *	Thrown when the view creation fails.
	 *
	 * @throws HtmlViewRenderException
	 *	Thrown when the view rendering fails.
	 *
	 * @param string $view
	 *	The view resource identifier.
	 *
	 * @param array $variableMap
	 *	The view variable map.
	 */
	protected final function html(string $view, array $variableMap = null) : void
	{
		$this->getResponse()->render(
			HtmlViewProvider::getInstance()->getView($view),
			$variableMap
		);
	}
}
