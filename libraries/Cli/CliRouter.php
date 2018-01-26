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

namespace Lightbit\Cli;

use \Lightbit\Cli\CliRouterException;
use \Lightbit\Cli\ICliRouter;
use \Lightbit\Routing\Route;
use \Lightbit\Routing\Router;
use \Lightbit\Runtime\RuntimeEnvironment;

/**
 * ICliRouter.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class CliRouter extends Router implements ICliRouter
{
	/**
	 * Gets the route.
	 *
	 * @return Route
	 *	The route.
	 */
	public final function getRoute() : Route
	{
		static $instance;

		if (!isset($instance))
		{
			if (!RuntimeEnvironment::getInstance()->isCli())
			{
				throw new CliRouterException($this, 'Can not get command line interface route, incompatible environment');
			}

			if (!isset($_SERVER['argv']))
			{
				throw new CliRouterException($this, 'Can not get command line interface route, arguments information missing');
			}

			$route = [];

			foreach ($_SERVER['argv'] as $i => $argument)
			{
				if (preg_match('%^\\-\\-([^=]+)=(.+)$%', $argument, $tokens))
				{
					if ($tokens[1] === 'action')
					{
						$route[0] = $tokens[2];
					}
					else
					{
						$route[$tokens[1]] = $tokens[2];
					}
				}
			}

			$instance = new Route($this->getContext(), $route);
		}

		return $instance;
	}
}