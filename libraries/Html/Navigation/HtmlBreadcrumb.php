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

namespace Lightbit\Html\Navigation;

use \Lightbit\Base\Object;
use \Lightbit\Html\Navigation\IHtmlBreadcrumb;

/**
 * HtmlBreadcrumb.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HtmlBreadcrumb extends Object implements IHtmlBreadcrumb
{
	public static function create(array $breadcrumb) : IHtmlBreadcrumb
	{
		return __object_create_ex(self::class, null, $breadcrumb);
	}

	/**
	 * The route.
	 *
	 * @type array
	 */
	private $route;

	/**
	 * The title.
	 *
	 * @type string
	 */
	private $title;

	/**
	 * Constructor.
	 *
	 * @param string $title
	 *	The breadcrumb title.
	 *
	 * @param array $route
	 *	The breadcrumb route.
	 *
	 * @param array $configuration
	 *	The breadcrumb configuration.
	 */
	public function __construct(string $title, array $route = null, array $configuration = null)
	{
		$this->route = $route;
		$this->title = $title;

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}

	/**
	 * Gets the route.
	 *
	 * @return array
	 *	The route.
	 */
	public function getRoute() : ?array
	{
		return $this->route;
	}

	/**
	 * Gets the title.
	 *
	 * @return string
	 *	The title.
	 */
	public function getTitle() : string
	{
		return $this->title;
	}
}