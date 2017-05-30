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

use \Lightbit\ContextException;
use \Lightbit\Base\IContext;
use \Lightbit\Base\Exception;

/**
 * ModuleNotFoundException.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class ModuleNotFoundException extends ContextException
{
	/**
	 * The module identifier.
	 *
	 * @type string
	 */
	private $moduleID;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @param string $moduleID
	 *	The module identifier.
	 *
	 * @param string $message
	 *	The exception message.
	 *
	 * @param Throwable $previous
	 *	The previous throwable.
	 */
	public function __construct(IContext $context, string $moduleID, string $message, \Throwable $previous = null)
	{
		parent::__construct($context, $message, $previous);
	}

	/**
	 * Gets the module identifier.
	 *
	 * @return string
	 *	The module identifier.
	 */
	public final function getModuleID() : string
	{
		return $this->moduleID;
	}
}
