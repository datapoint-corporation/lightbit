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

namespace Lightbit\Base;

use \Lightbit\Base\IChannel;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IComponent;
use \Lightbit\Scope;

/**
 * Component.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Component implements IComponent
{
	/**
	 * The context.
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration map.
	 */
	public final function __construct(IContext $context, string $id, array $configuration = null)
	{
		$this->context = $context;
		$this->id = $id;

		$this->onConstruct();

		if ($configuration)
		{
			(new Scope($this))->configure($configuration);
		}

		$this->onAfterConstruct();
	}

	/**
	 * Disposes.
	 *
	 * It first closes any persistent resources, followed by the disposal of
	 * any temporary memory information and, finally, the state.
	 */
	public final function dispose() : void
	{
		$this->onDispose();

		if ($this instanceof IChannel)
		{
			if (!$this->isClosed())
			{
				$this->close();
			}
		}

		$this->onAfterDispose();
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : IContext
	{
		return $this->context;
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
	 * On After Construct.
	 *
	 * It is invoked automatically during the component construction
	 * procedure, after applying the custom configuration.
	 */
	protected function onAfterConstruct() : void
	{

	}

	/**
	 * On After Dispose.
	 *
	 * It is invoked automatically during the component disposal procedure, 
	 * after the component is closed.
	 */
	protected function onAfterDispose() : void
	{

	}

	/**
	 * On Construct.
	 *
	 * It is invoked automatically during the component construction
	 * procedure, before applying the custom configuration.
	 */
	protected function onConstruct() : void
	{

	}

	/**
	 * On Dispose.
	 *
	 * It is invoked automatically during the component construction
	 * procedure, before the channel is closed.
	 */
	protected function onDispose() : void
	{

	}
}