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

namespace Lightbit\Routing;

use \Lightbit;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IController;
use \Lightbit\Cli\ICliController;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Http\IHttpController;
use \Lightbit\Routing\Action;
use \Lightbit\Routing\ActionEnvironmentMismatchException;
use \Lightbit\Runtime\RuntimeEnvironment;

/**
 * Action.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Action
{
	/**
	 * The arguments.
	 *
	 * @var string
	 */
	private $arguments;

	/**
	 * The controller.
	 *
	 * @var IController
	 */
	private $controller;

	/**
	 * The default flag.
	 *
	 * @var bool
	 */
	private $default;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The method name.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * The virtual path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Constructor.
	 *
	 * @param IController $controller
	 *	The action controller.
	 *
	 * @param string $id
	 *	The action identifier
	 *
	 * @param array $arguments
	 *	The action arguments.
	 */
	public function __construct(IController $controller, string $id, string $method, array $arguments)
	{
		$this->arguments = $arguments;
		$this->controller = $controller;
		$this->id = $id;
		$this->method = $method;
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
	public function getController() : IController
	{
		return $this->controller;
	}

	/**
	 * Gets the method name.
	 *
	 * @return string
	 *	The method name.
	 */
	public function getMethodName() : string
	{
		return $this->method;
	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the virtual path.
	 *
	 * @return string
	 *	The virtual path.
	 */
	public function getPath() : string
	{
		if (!isset($this->path))
		{
			$this->path = $this->controller->getID() . '/' . $this->id;

			$current = $this->controller->getContext();
			while ($next = $current->getContext())
			{
				$this->path = $current->getID() . '/' . $this->path;
				$current = $next;
			}

			$this->path = '//' . $this->path;
		}

		return $this->path;
	}

	/**
	 * Checks if this action is default.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isDefault() : bool
	{
		if (!isset($this->default))
		{
			$subject = Lightbit::getInstance()->getApplication()->getDefaultAction();
			$this->default = ($subject->controller === $this->controller && $subject->id === $this->id);
		}

		return $this->default;
	}

	/**
	 * Runs the action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function run() : int
	{
		// As a security measure, we'll ensure there is no way a controller
		// action runs outside of the expected environment.
		$environment = RuntimeEnvironment::getInstance();

		if ($environment->isHttp() && !($this->controller instanceof IHttpController))
		{
			throw new ActionEnvironmentMismatchException($this, sprintf('Can not run action, runtime environment mismatch: "%s"', $environment->getID()));
		}

		if ($environment->isCli() && !($this->controller instanceof ICliController))
		{
			throw new ActionEnvironmentMismatchException($this, sprintf('Can not run action, runtime environment mismatch: "%s"', $environment->getID()));
		}

		$lightbit = Lightbit::getInstance();

		$result;
		$action = $lightbit->setAction($this);
		$context = $lightbit->setContext($this->controller->getContext());

		try
		{
			$result = $this->controller->run($this->method, $this->arguments);
		}
		catch (Throwable $e)
		{
			$lightbit->setAction($action);
			$lightbit->setContext($context);
			throw $e;
		}

		$lightbit->setAction($action);
		$lightbit->setContext($context);

		if (isset($result))
		{
			$this->controller->result($result);
		}

		return (is_int($result) ? $result : 0);
	}
}