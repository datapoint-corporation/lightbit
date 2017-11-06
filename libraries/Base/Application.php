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

use \Lightbit\Base\Context;
use \Lightbit\Base\View;
USE \Lightbit\Data\Caching\Cache;
use \Lightbit\Data\SlugManager;
use \Lightbit\Data\Sql\My\MySqlConnection;
use \Lightbit\Globalization\MessageSource;
use \Lightbit\Html\HtmlAdapter;
use \Lightbit\Html\HtmlDocument;
use \Lightbit\Http\HttpAssetManager;
use \Lightbit\Http\HttpQueryString;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpRouter;
use \Lightbit\Http\HttpSession;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\Security\Cryptography\PasswordDigest;

use \Lightbit\Base\IApplication;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Context implements IApplication
{
	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The application path.
	 *
	 * @param array $configuration
	 *	The application configuration.
	 */
	public final function __construct(string $path, array $configuration = null)
	{
		parent::__construct(null, 'application', $path, null);

		$this->setComponentsConfiguration
		(
			[
				'data.cache' => [ '@class' => Cache::class ],
				'data.cache.file' => [ '@class' => Cache::class ],
				'data.cache.memory' => [ '@class' => Cache::class ],
				'data.cache.network' => [ '@class' => Cache::class ],
				'data.slug.manager' => [ '@class' => SlugManager::class ],
				'data.sql.connection' => [ '@class' => MySqlConnection::class ],
				'globalization.message.source' => [ '@class' => MessageSource::class ],
				'html.adapter' => [ '@class' => HtmlAdapter::class ],
				'html.document' => [ '@class' => HtmlDocument::class ],
				'http.asset.manager' => [ '@class' => HttpAssetManager::class ],
				'http.query.string' => [ '@class' => HttpQueryString::class ],
				'http.request' => [ '@class' => HttpRequest::class ],
				'http.response' => [ '@class' => HttpResponse::class ],
				'http.router' => [ '@class' => HttpRouter::class ],
				'http.session' => [ '@class' => HttpSession::class ],
				'security.cryptography.password.digest' => [ '@class' => PasswordDigest::class ],
			]
		);

		$this->setLocale('en-US');

		$this->onConstruct();

		if ($configuration)
		{
			$this->configure($configuration);
		}

		$this->onAfterConstruct();
	}

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array
	{
		return [ '/site/index' ];
	}

	/**
	 * Checks the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDebug() : bool
	{
		return __debug();
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	public function run() : int
	{
		switch (__environment_type())
		{
			case 'web':
				return $this->runAsWeb();

			case 'cli':
				return $this->runAsCli();
		}

		return 1;
	}

	/**
	 * Runs as a command line interface application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	private function runAsCli() : int
	{
		return $this->resolve($this->getDefaultRoute())->run();
	}

	/**
	 * Runs as a web application.
	 *
	 * @return int
	 *	The exit status code.
	 */
	private function runAsWeb() : int
	{
		return $this->getHttpRouter()->resolve()->run();
	}

	/**
	 * Sets the debug flag.
	 *
	 * @param bool $debug
	 *	The debug flag.
	 */
	public final function setDebug(bool $debug) : void
	{
		__debug_set($debug);
	}

	/**
	 * Safely terminates the script execution after disposing of all
	 * application elements.
	 *
	 * @param int $status
	 *	The script exit status code.
	 */
	public final function terminate(int $status = 0) : void
	{
		__exit($status);
	}

	/**
	 * Generates the proper response to a throwable caught by the global
	 * exception handler after application registration.
	 *
	 * @param Throwable $throwable
	 *	The throwable object.
	 */
	public function throwable(\Throwable $throwable) : void
	{
		$this->onThrowable($throwable);

		if ($action = __action_get())
		{
			if ($action->getController()->throwable($throwable))
			{
				$this->onAfterThrowable($throwable);
				return;
			}

			$context = $action->getContext();

			while ($context instanceof IModule)
			{
				if ($context->throwable($throwable))
				{
					$this->onAfterThrowable($throwable);
					return;
				}

				$context = $context->getContext();
			}
		}

		if (__environment_is_web())
		{
			// Calculate the appropriate http status code to be defined
			// in this response.
			$status = ($throwable instanceof HttpStatusException)
				? $throwable->getStatusCode()
				: 500;

			// Reset the current document and response, defining it with
			// the appropriate status code.
			$document = $this->getHtmlDocument();
			$document->reset();

			$response = $this->getHttpResponse();
			$response->reset();
			$response->setStatusCode($status);

			// Resolve to the view containing the applicable error documents,
			// with a default (catch-all) identifier.
			foreach ( [ $status, 'default' ] as $i => $suffix)
			{
				$view = 'error-documents/' . $suffix;

				if ($this->hasView($view))
				{
					$this->getView($view)->run([ 'status' => $status, 'throwable' => $throwable ]);
					$this->onAfterThrowable($throwable);
					return;
				}
			}

			// If no views were found, we'll have to fallback to the one
			// bundled with the framework.
			foreach ( [ $status, 'default' ] as $i => $suffix)
			{
				$view = 'lightbit://views/error-documents/' . $suffix;

				if ($this->hasView($view))
				{
					$this->getView($view)->run([ 'status' => $status, 'throwable' => $throwable ]);
					$this->onAfterThrowable($throwable);
					return;
				}
			}
		}

		__lightbit_throwable($throwable);
		$this->onAfterThrowable($throwable);
		return;
	}

	/**
	 * On After Construct.
	 *
	 * This method is invoked during the application construction procedure,
	 * after the dynamic configuration is applied.
	 */
	protected function onAfterConstruct() : void
	{
		$this->raise('lightbit.base.application.construct.after', $this);
	}

	/**
	 * On After Throwable.
	 *
	 * This method is invoked during the application throwable handling
	 * procedure, after the error response is generated.
	 */
	protected function onAfterThrowable(\Throwable $throwable) : void
	{
		$this->raise('lightbit.base.application.throwable.after', $this, $throwable);
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the application construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		$this->raise('lightbit.base.application.construct', $this);
	}

	/**
	 * On Throwable.
	 *
	 * This method is invoked during the application throwable handling
	 * procedure, before the error response is generated.
	 */
	protected function onThrowable(\Throwable $throwable) : void
	{
		$this->raise('lightbit.base.application.throwable', $this, $throwable);
	}
}
