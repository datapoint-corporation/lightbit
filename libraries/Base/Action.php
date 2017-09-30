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

use \Lightbit\Base\IController;
use \Lightbit\Base\Object;
use \Lightbit\IllegalStateException;

/**
 * Action.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Action extends Object
{
	/**
	 * The arguments.
	 *
	 * @type array
	 */
	private $arguments;

	/**
	 * The context.
	 *
	 * @type Context
	 */
	private $context;

	/**
	 * The controller.
	 *
	 * @type IController
	 */
	private $controller;

	/**
	 * The global identifier.
	 *
	 * @type string
	 */
	private $globalID;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * The name.
	 *
	 * @type string
	 */
	private $name;

	/**
	 * The parameters.
	 *
	 * @type array
	 */
	private $parameters;

	/**
	 * Constructor.
	 *
	 * @param IController $controller
	 *	The action controller.
	 *
	 * @param string $name
	 *	The action name.
	 *
	 * @param array $parameters
	 *	The action parameters.
	 */
	public function __construct(IController $controller, string $name, array $parameters)
	{
		$this->parameters = $parameters;
		$this->controller = $controller;
		$this->name = $name;
	}

	/**
	 * Compares to a route.
	 *
	 * Comparison is based on the resolution of the given route through the
	 * context this action is attached to.
	 *
	 * The following list of integers can be returned:
	 *
	 *	0)	The route resolves to a different controller, either within the
	 *		same context or at one of the parent modules.
	 *
	 *	1)	The route resolves to the same context and controller with a
	 *		a matching action name - parameters may differ.
	 *
	 *	2)	The route resolves to an action within the same context and
	 *		controller.
	 *
	 *	3)	The route resolves to an action in a different controller, that
	 *		may be part of either the same context or a child of it.
	 *
	 * @param array $route
	 *	The route to compare against.
	 *
	 * @return int
	 *	The result.
	 */
	public function compare(array $route) : int
	{
		$action = $this->controller->getContext()->resolve($route);

		if ($this->controller === $action->controller)
		{
			return ($this->name === $this->name ? 1 : 2);
		}

		$self = $this->controller->getContext();
		$subject = $action->controller->getContext();

		while ($subject)
		{
			if ($self === $subject)
			{
				return 3;
			}

			$subject = $subject->getContext();
		}

		return 0;
	}

	/**
	 * Gets the arguments.
	 *
	 * @return array
	 *	The arguments.
	 */
	public function getArguments() : array
	{
		if (!isset($this->arguments))
		{
			$this->arguments = array_values($parameters);
		}

		return $this->arguments;
	}

	/**
	 * Gets the parameters.
	 *
	 * @return array
	 *	The parameters.
	 */
	public function getParameters() : array
	{
		return $this->parameters;
	}

	/**
	 * Gets the context.
	 *
	 * @return Context
	 *	The context.
	 */
	public function getContext() : Context
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
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
	{
		if (!$this->id)
		{
			$this->id = $this->controller->getGlobalID() . '/' . $this->name;
		}

		return $this->id;
	}

	/**
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string
	{
		if (!$this->globalID)
		{
			$this->globalID = $this->controller->getContext()->getID()
				. '/' . $this->controller->getID()
				. '/' . $this->name;
		}

		return $this->globalID;
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Runs the action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function run() // : mixed
	{
		return $this->controller->run($this);
	}
}
