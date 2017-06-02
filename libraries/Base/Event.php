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

use \Lightbit;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IEvent;
use \Lightbit\Base\Object;
use \Lightbit\Helpers\ObjectHelper;

/**
 * IEvent.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Event extends Object implements IEvent
{
	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * The source.
	 *
	 * @type Object
	 */
	private $source;

	/**
	 * Constructor.
	 *
	 * @param Object $source
	 *	The event source.
	 *
	 * @param string $id
	 *	The event identifier.
	 *
	 * @param array $configuration
	 *	The event configuration.
	 */
	public function __construct(Object $source, string $id, array $configuration = null)
	{
		$this->id = $id;
		$this->source = $source;

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/** 
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : IContext
	{
		static $context;

		if (!$context)
		{
			if ($this->source instanceof IElement)
			{
				$context = $this->source->getContext();
			}

			else if ($this->source instanceof IContext)
			{
				return $context = $this->source;
			}

			else
			{
				$context = Lightbit::getApplication();
			}
		}

		return $context;

	}

	/**
	 * Gets the identifier.
	 *
	 * @return string
	 *	The identifier.
	 */
	public final function getID() : string
	{
		return $this->id;
	}

	/**
	 * Gets the source.
	 *
	 * @return Object
	 *	The source
	 */
	public final function getSource() : Object
	{
		return $this->source;
	}
}