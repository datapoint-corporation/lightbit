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

use \Lightbit\Base\Context;
use \Lightbit\Base\IApplication;
use \Lightbit\Cli\CliRouter;
use \Lightbit\Cli\ICliRouter;
use \Lightbit\Data\Caching\NoCache;
use \Lightbit\Data\Sql\My\MySqlConnection;
use \Lightbit\Html\HtmlComposer;
use \Lightbit\Html\HtmlDocument;
use \Lightbit\Http\HttpQueryStringRouter;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\I18n\Icu\IcuLocaleManager;
use \Lightbit\Scope;
use \Lightbit\Routing\Action;
use \Lightbit\Routing\RouteException;
use \Lightbit\Routing\ActionParameterRouteException;
use \Lightbit\Routing\SlugActionParameterRouteException;
use \Lightbit\Runtime\RuntimeEnvironment;
use \Lightbit\IllegalStateException;

/**
 * Application.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Context implements IApplication
{
	/**
	 * The default component configuration.
	 *
	 * @var array
	 */
	private const DEFAULT_COMPONENT_CONFIGURATION =
	[
		'cli.router' => 
		[
			'@class' => CliRouter::class
		],

		'data.cache' =>
		[
			'@class' => NoCache::class
		],

		'data.cache.file' =>
		[
			'@class' => NoCache::class
		],

		'data.cache.memory' =>
		[
			'@class' => NoCache::class
		],

		'data.cache.network' =>
		[
			'@class' => NoCache::class
		],

		'data.sql.connection' =>
		[
			'@class' => MySqlConnection::class
		],

		'html.composer' =>
		[
			'@class' => HtmlComposer::class
		],

		'html.document' =>
		[
			'@class' => HtmlDocument::class
		],

		'http.request' =>
		[
			'@class' => HttpRequest::class
		],

		'http.response' =>
		[
			'@class' => HttpResponse::class
		],

		'http.router' =>
		[
			'@class' => HttpQueryStringRouter::class
		],

		'locale.manager' =>
		[
			'@class' => IcuLocaleManager::class
		]
	];

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The context parent.
	 *
	 * @param string $id
	 *	The context identifier.
	 *
	 * @param string $path
	 *	The context install path.
	 *
	 * @param array $configuration
	 *	The context configuration.
	 */
	public final function __construct(string $path, array $configuration = null)
	{
		parent::__construct(null, 'application', $path);

		$this->setComponentsConfiguration(self::DEFAULT_COMPONENT_CONFIGURATION);

		$this->onConstruct();

		if ($configuration)
		{
			(new Scope($this))->configure($configuration);
		}

		$this->onAfterConstruct();
	}

	/**
	 * Gets the default route.
	 *
	 * @override
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array
	{
		return [ '//site/index' ];
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The exit status.
	 */
	public final function run() : int
	{
		$action;
		$environment = RuntimeEnvironment::getInstance();

		if ($environment->isHttp())
		{
			try
			{
				$action = $this->getHttpRouter()->getRoute()->resolve();
			}
			catch (SlugActionParameterRouteException $e)
			{
				throw new HttpStatusException(404, 'Document Not Found', $e);
			}
			catch (ActionParameterRouteException $e)
			{
				throw new HttpStatusException(400, 'Bad Request', $e);
			}
			catch (ActionParameterRouteException $e)
			{
				throw new HttpStatusException(400, 'Bad Request', $e);
			}
			catch (RouteException $e)
			{
				throw new HttpStatusException(404, 'Document Not Found', $e);
			}
		}

		else if ($environment->isCli())
		{
			$action = $this->getCliRouter()->getRoute()->resolve();
		}

		else
		{
			$action = $this->resolve($this->getDefaultRoute());
		}

		return $action->run();
	}

	/**
	 * On After Construct.
	 *
	 * It is invoked automatically during the application construction
	 * procedure, after applying the custom configuration.
	 */
	protected function onAfterConstruct() : void
	{
		
	}

	/**
	 * On Construct.
	 *
	 * It is invoked automatically during the application construction
	 * procedure, before applying the custom configuration.
	 */
	protected function onConstruct() : void
	{
		
	}
}