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

use \Lightbit\Exception;
use \Lightbit\Base\IController;
use \Lightbit\Base\Object;

/**
 * Action.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Action extends Object
{
	/**
	 * The instance.
	 *
	 * @type Action
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return Action
	 *	The instance.
	 */
	public static function getInstance() : Action
	{
		if (!self::$instance)
		{
			throw new Exception('Action is not available within context.');
		}

		return self::$instance;
	}

	/**
	 * The arguments.
	 *
	 * @type array
	 */
	private $arguments;

	/**
	 * The context.
	 *
	 * @type IContext
	 */
	private $context;

	/**
	 * The controller.
	 *
	 * @type IController
	 */
	private $controller;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param IController $controller
	 *	The action controller.
	 *
	 * @param string $id
	 *	The action identifier.
	 *
	 * @param array $arguments
	 *	The action arguments.
	 */
	public function __construct(IController $controller, string $id, array $arguments)
	{
		$this->arguments = $arguments;
		$this->controller = $controller;
		$this->id = $id;
	}

	/**
	 * Gets the arguments.
	 *
	 * @return array
	 *	The arguments.
	 */
	public function getArguments() : array
	{
		return $this->arguments;
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
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public function getID() : string
	{
		static $id;

		if (!$id)
		{
			$id = $this->controller->getID() . '/' . $this->id;
		}

		return $id;
	}

	/**
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public function getGlobalID() : string
	{
		static $globalID;

		if (!$globalID)
		{
			$globalID = $this->controller->getContext()->getID()
				. '/' . $this->controller->getID()
				. '/' . $this->id;
		}

		return $globalID;
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public function getName() : string
	{
		return $this->id;
	}

	/**
	 * Runs the action.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function run() // : mixed
	{
		self::$instance = $this;
		$result = $this->controller->{$this->controller->getActionMethodName($this->id)}(...array_values($this->arguments));

		self::$instance = null;
		return $result;
	}
}
