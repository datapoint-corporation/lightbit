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

use \ReflectionClass;

use \Lightbit\Http\IHttpController;

/**
 * HttpControllerFactory.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpControllerFactory implements IHttpControllerFactory
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Creates a controller.
	 *
	 * @throws HttpControllerFactoryException
	 *	Thrown as a generic exception when the controller fails to be created
	 *	regardless of the actual reason for it.
	 *
	 * @param IHttpAction $action
	 *	The controller action.
	 *
	 * @return IHttpController
	 *	The controller.
	 */
	public function createController(IHttpAction $action) : IHttpController
	{
		$controllerClassName = $action->getRoute()->getControllerClassName();

		try
		{
			$subject = new ReflectionClass($controllerClassName);

			if ($subject->isAbstract())
			{
				throw new HttpControllerFactoryException($this, sprintf('Can not get controller class, it is abstract: "%s"', $controllerClassName));
			}

			if (!$subject->implementsInterface(IHttpController::class))
			{
				throw new HttpControllerFactoryException($this, sprintf('Can not get controller class, it does not implement the expected interface: "%s"', $controllerClassName));
			}

			return $subject->newInstance($action);
		}
		catch (Throwable $e)
		{
			if (! ($e instanceof HttpControllerFactoryException))
			{
				throw new HttpControllerFactoryException($this, sprintf('Can not create controller, construction failure: "%s"', $controllerClassName), $e);
			}
		}

		throw new HttpControllerFactoryException($this, sprintf('Can not create controller, it is missing a required interface: "%s"', $controllerClassName));
	}
}
