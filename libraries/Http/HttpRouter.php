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

namespace Lightbit\Http;

use \Lightbit\Base\IAction;
use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IllegalParameterRouteException;
use \Lightbit\Base\MissingParameterRouteException;
use \Lightbit\Base\ParameterRouteException;
use \Lightbit\Base\RouteException;
use \Lightbit\Exception;
use \Lightbit\Http\HttpRouterBase;

/**
 * HttpRouter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpRouter extends HttpRouterBase
{
	/**
	 * The script name.
	 *
	 * @var string
	 */
	private $scriptName;

	/**
	 * Gets the script name.
	 *
	 * @return string
	 *	The script name.
	 */
	public final function getScriptName() : ?string
	{
		return $this->scriptName;
	}

	/**
	 * Sets the script name.
	 *
	 * @param string $scriptName
	 *	The script name.
	 */
	public final function setScriptName(?string $scriptName) : void
	{
		$this->scriptName = $scriptName;
	}

	/**
	 * Resolves the current request to a controller action.
	 *
	 * @return IAction
	 *	The action.
	 */
	public final function resolve() : IAction
	{
		$path = strtr
		(
			'/' . $this->getHttpQueryString()->get('?string', 'action'),
			[ '.' => '/', '_' => '-' ]
		);

		try
		{
			return __application()->resolve([ ($path === '/' ? null : $path) ] + $_GET);
		}
		catch (IllegalParameterRouteException $e)
		{
			throw new HttpStatusException(404, $e->getMessage(), $e);
		}
		catch (MissingParameterRouteException $e)
		{
			throw new HttpStatusException(400, $e->getMessage(), $e);
		}
		catch (RouteException $e)
		{
			throw new HttpStatusException(404, $e->getMessage(), $e);
		}
	}

	/**
	 * Creates an url.
	 *
	 * @param array $route
	 *	The route to resolve to.
	 *
	 * @param bool $absolute
	 *	The absolute flag which, when set, will cause the url to be
	 *	created as an absolute url.
	 *
	 * @return string
	 *	The result.
	 */
	public function url(array $route, bool $absolute = false) : string
	{
		$action = __action_context()->resolve($route);

		$result = '' . $this->scriptName;

		$parameters = [ 'action' => strtr($action->getGlobalID(), '/-', '._') ]
			+ $action->getParameters()
			+ $route;

		unset($parameters[0]);

		if ($parameters)
		{
			$result .= '?' . __http_query_encode($parameters);
		}

		if ($absolute)
		{
			$result = $this->getBaseUrl() . $result;
		}

		return $result;
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the component construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		parent::onConstruct();

		if (isset($_SERVER['SCRIPT_NAME']))
		{
			$this->scriptName = basename($_SERVER['SCRIPT_NAME']);
		}
	}
}
