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

use \Lightbit\Base\ParameterRouteException;

/**
 * SlugParseParameterRouteException.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class SlugParseParameterRouteException extends ParameterRouteException
{
	/**
	 * The class name.
	 *
	 * @type string
	 */
	private $className;

	/**
	 * The slug.
	 *
	 * @type string
	 */
	private $slug;

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The context.
	 *
	 * @param array $route
	 *	The route.
	 *
	 * @param string $parameterName
	 *	The parameter name.
	 *
	 * @param string $className
	 *	The class name.
	 *
	 * @param string $slug
	 *	The slug content.
	 *
	 * @param string $message
	 *	The exception message.
	 *
	 * @param Throwable $previous
	 *	The previous throwable.
	 */
	public function __construct(Context $context, array $route, string $parameterName, string $className, string $slug, string $message, \Throwable $previous = null)
	{
		parent::__construct($context, $route, $parameterName, $message, $previous);

		$this->className = $className;
		$this->slug = $slug;
	}

	/**
	 * Gets the class name.
	 *
	 * @return string
	 *	The class name.
	 */
	public final function getClassName() : string
	{
		return $this->className;
	}

	/**
	 * Gets the slug.
	 *
	 * @return string
	 *	The slug
	 */
	public final function getSlug() : string
	{
		return $this->slug;
	}
}
