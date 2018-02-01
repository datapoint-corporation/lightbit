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

use \Lightbit;
use \Lightbit\Base\Application;
use \Lightbit\Http\HttpRouter;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Routing\Route;

/**
 * HttpQueryStringRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpQueryStringRouter extends HttpRouter
{
	/**
	 * The query string action parameter name.
	 *
	 * @var string
	 */
	private const QUERYSTRING_ACTION = 'action';

	/**
	 * Gets the route.
	 *
	 * @return Route
	 *	The route.
	 */
	public function getRoute() : Route
	{
		static $instance;

		if (!isset($instance))
		{
			$route = [ null ];

			if (isset($_GET[self::QUERYSTRING_ACTION]))
			{
				if ($path = trim($_GET[self::QUERYSTRING_ACTION]))
				{
					if (!preg_match('%^([a-z][a-z0-9]+(_[a-z][a-z0-9]+)*)(\\.([a-z][a-z0-9]+(_[a-z][a-z0-9]+)*))*$%', $path))
					{
						throw new HttpStatusException(400, sprintf('Bad format for action parameter: "%s"', $path));
					}

					$route[0] = '//' . strtr($path, [ '.' => '/', '_' => '-' ]);	
				}				
			}

			$route += array_diff_key($_GET, [ self::QUERYSTRING_ACTION => null ]);

			$instance = new Route($this->getContext(), $route);
		}

		return $instance;
	}

	/**
	 * Creates a url.
	 *
	 * It begins by resolving the given route through the current active
	 * context, followed by the creation of a matching url.
	 *
	 * @param array $route
	 *	The route.
	 *
	 * @param bool $absolute
	 *	The absolute url flag.
	 *
	 * @return string
	 *	The result.
	 */
	public function url(array $route, bool $absolute = false) : string
	{
		// Get the route.
		$route = new Route(Lightbit::getInstance()->getContext(), $route);
		$action = $route->resolve();
		
		// Set the query string parameters.
		$parameters = [];

		if (!$action->isDefault())
		{
			// Calculate the query string action token.
			$controller = $action->getController();
			$context = $controller->getContext();

			$token = strtr($controller->getID(), '/', '.') . '.' . $action->getID();

			while ($parent = $context->getContext())
			{
				$token = $context->getID() . '.' . $token;
				$context = $parent;
			}

			$parameters[self::QUERYSTRING_ACTION] = $token;
		}

		$parameters += $route->getParameters(true);

		// Create the url.
		$result = $this->queryString($parameters);

		if ($absolute)
		{
			$result = $this->getBaseUrl() . $result;
		}

		return $result;
	}
}