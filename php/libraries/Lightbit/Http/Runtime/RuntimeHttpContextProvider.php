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

namespace Lightbit\Http\Runtime;

use \Lightbit\Http\Runtime\IRuntimeHttpContext;
use \Lightbit\Http\Runtime\IRuntimeHttpContextFactory;
use \Lightbit\Http\Runtime\IRuntimeHttpContextProvider;
use \Lightbit\Http\Runtime\RuntimeHttpContext;

/**
 * RuntimeHttpContextProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class RuntimeHttpContextProvider implements IRuntimeHttpContextProvider
{
	/**
	 * The instance.
	 *
	 * @var IRuntimeHttpContextProvider
	 */
	private static $instance;

	/**
	 * Gets the context provider.
	 *
	 * @return IRuntimeHttpContextProvider
	 *	The context provider.
	 */
	public static final function getInstance() : IRuntimeHttpContextProvider
	{
		return (self::$instance ?? (self::$instance = new RuntimeHttpContextProvider()));
	}

	/**
	 * The context.
	 */
	private $context;

	/**
	 * The context factory.
	 *
	 * @var IRuntimeHttpContextFactory
	 */
	private $contextFactory;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->contextFactory = new RuntimeHttpContextFactory();
	}

	/**
	 * Gets the context.
	 *
	 * @return IHttpContext
	 *	The context.
	 */
	public final function getContext() : IRuntimeHttpContext
	{
		return ($this->context ?? ($this->context = $this->contextFactory->createContext()));
	}

	/**
	 * Sets the context factory.
	 *
	 * @param IRuntimeHttpContextFactory $contextFactory
	 *	The context factory.
	 */
	public final function setContextFactory(IRuntimeHttpContextFactory $contextFactory) : void
	{
		$this->contextFactory = $contextFactory;
	}
}
