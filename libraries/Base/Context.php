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

use \ReflectionClass;
use \Throwable;

use \Lightbit;
use \Lightbit\Base\ComponentNotFoundContextException;
use \Lightbit\Base\ControllerNotFoundContextException;
use \Lightbit\Base\IChannel;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IModule;
use \Lightbit\Base\ITheme;
use \Lightbit\Base\Module;
use \Lightbit\Base\ModuleNotFoundContextException;
use \Lightbit\Base\Theme;
use \Lightbit\Cli\ICliRouter;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;
use \Lightbit\Data\Conversing\StringCamelCaseConversion;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Html\IHtmlComposer;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\I18n\ILocale;
use \Lightbit\I18n\ILocaleManager;
use \Lightbit\IO\FileSystem\Asset;
use \Lightbit\IO\FileSystem\FileAccessException;
use \Lightbit\IO\FileSystem\FileNotFoundException;
use \Lightbit\ObjectFactory;
use \Lightbit\Routing\Action;
use \Lightbit\Routing\IRouter;
use \Lightbit\Routing\Route;
use \Lightbit\RuntimeException;
use \Lightbit\Script;

/**
 * Context.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Context implements IContext
{
	/**
	 * The parent context.
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * The controllers.
	 *
	 * @var array
	 */
	private $controllers;

	/**
	 * The components.
	 *
	 * @var array
	 */
	private $components;

	/**
	 * The components configuration.
	 *
	 * @var array
	 */
	private $componentsConfiguration;

	/**
	 * The default action.
	 *
	 * @var Action
	 */
	private $defaultAction;

	/**
	 * The identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The locale.
	 *
	 * @var string
	 */
	private $locale;

	/**
	 * The modules.
	 *
	 * @var array
	 */
	private $modules;

	/**
	 * The install path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The theme.
	 *
	 * @var ITheme
	 */
	private $theme;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The context parent.
	 *
	 * @param string $id
	 *	The context identifier.
	 *
	 * @param string $path
	 *	The context install path.
	 */
	protected function __construct(?IContext $context, string $id, string $path)
	{
		$this->context = $context;
		$this->controllers = [];
		$this->components = [];
		$this->componentsConfiguration1 = [];
		$this->id = $id;
		$this->modules = [];
		$this->path = $path;

		// Scan the modules path against child module directories and make
		// an attempt at loading them on the fly.
		if (is_dir($modulesPath = $this->getModulesPath()))
		{
			$tokens = scandir($modulesPath);

			if ($tokens === false)
			{
				throw new FileAccessException($modulesPath, sprintf('Can not scan modules directory, access denied: "%s"', $id));
			}

			foreach ($tokens as $i => $token)
			{
				if (!in_array($token[0], [ '.', '_', '~', '$' ]))
				{
					$this->module($token, ($modulesPath . DIRECTORY_SEPARATOR . $token));
				}				
			}
		}
	}

	/**
	 * Disposes the context.
	 *
	 * It disposes each module, followed by each component, in reverse order
	 * as they were first accessed, before disposing any of its own resources.
	 */
	public final function dispose() : void
	{
		$this->onDispose();

		foreach ($this->modules as $i => $module)
		{
			$module->dispose();
		}

		foreach ($this->components as $i => $component)
		{
			$component->dispose();
		}

		$this->onAfterDispose();
	}

	/**
	 * Construct a child module.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @param string $path
	 *	The module path.
	 *
	 * @return IModule
	 *	The module.
	 */
	private function module(string $id, string $path) : IModule
	{
		if (!isset($this->modules[$id]))
		{
			$configuration = (new Script($path . DIRECTORY_SEPARATOR . 'main.php'))->include();

			$this->modules[$id] = (new ObjectFactory())->getObjectOfClass
			(
				IModule::class,
				$configuration['@class'],
				$this,
				$id,
				$path,
				$configuration
			);
		}

		return $this->modules[$id];
	}

	/**
	 * Gets the command line interface router.
	 *
	 * @return ICliRouter
	 *	The command line interface router.
	 */
	public final function getCliRouter() : ICliRouter
	{
		return $this->getComponent('cli.router');
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public final function getContext() : ?IContext
	{
		return $this->context;
	}

	/**
	 * Gets a controller.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return IController
	 *	The controller.
	 */
	public final function getController(string $id) : IController
	{
		if (!isset($this->controllers[$id]))
		{
			$controllerClassName = $this->getControllerClassName($id);

			if (!class_exists($controllerClassName))
			{
				throw new ControllerNotFoundContextException($this, sprintf('Can not get controller, not found: "%s"', $id));
			}

			$this->controllers[$id] = (new ObjectFactory())->getObjectOfClass
			(
				IController::class,
				$controllerClassName,
				$this,
				$id
			);
		}

		return $this->controllers[$id];
	}

	/**
	 * Gets a controller class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	public function getControllerClassName(string $id) : string
	{
		$tokens = explode('/', $id);

		foreach ($tokens as $i => $token)
		{
			$tokens[$i] = (new StringCamelCaseConversion($token))->toUpperCamelCase();
		}

		return (new ReflectionClass(static::class))->getNamespaceName()
			. '\\Controllers'
			. '\\' . implode('\\', $tokens)
			. 'Controller';
	}

	/**
	 * Gets a component.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return IComponent
	 *	The component.
	 */
	public final function getComponent(string $id) : IComponent
	{
		if (!isset($this->components[$id]))
		{
			if (!isset($this->componentsConfiguration[$id]))
			{
				if (isset($this->context))
				{
					try
					{
						return $this->context->getComponent($id);
					}
					catch (ComponentNotFoundContextException $e)
					{
						throw new ComponentNotFoundContextException($this, sprintf('Can not get component, not found: "%s"', $id));
					}
				}

				throw new ComponentNotFoundContextException($this, sprintf('Can not get component, not found: "%s"', $id));
			}

			$instance = (new ObjectFactory())->getObjectOfClass
			(
				IComponent::class,
				$this->componentsConfiguration[$id]['@class'],
				$this,
				$id,
				$this->componentsConfiguration[$id]
			);

			if ($instance instanceof IChannel)
			{
				$instance->start();
			}

			$this->components[$id] = $instance;
		}

		return $this->components[$id];
	}

	/**
	 * Gets the default action.
	 *
	 * @return Action
	 *	The default action.
	 */
	public final function getDefaultAction() : Action
	{
		if (!isset($this->defaultAction))
		{
			$this->defaultAction = (new Route($this, $this->getDefaultRoute()))->resolve();
		}

		return $this->defaultAction;
	}

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array
	{
		return [ '~/default/index' ];
	}

	/**
	 * Gets the file cache.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public function getFileCache() : IFileCache
	{
		return $this->getComponent('data.cache.file');
	}

	/**
	 * Gets the html composer.
	 *
	 * @return IHtmlComposer
	 *	The html composer.
	 */
	public function getHtmlComposer() : IHtmlComposer
	{
		return $this->getComponent('html.composer');
	}

	/**
	 * Gets the html document.
	 *
	 * @return IHtmlDocument
	 *	The html document.
	 */
	public function getHtmlDocument() : IHtmlDocument
	{
		return $this->getComponent('html.document');
	}

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public function getHttpRequest() : IHttpRequest
	{
		return $this->getComponent('http.request');
	}

	/**
	 * Gets the http response.
	 *
	 * @return IHttpRequest
	 *	The http response.
	 */
	public function getHttpResponse() : IHttpResponse
	{
		return $this->getComponent('http.response');
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public function getHttpRouter() : IHttpRouter
	{
		return $this->getComponent('http.router');
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
	 * Gets the locale.
	 *
	 * @return string
	 *	The locale.
	 */
	public final function getLocale() : ILocale
	{
		if (!isset($this->locale))
		{
			if (isset($this->context))
			{
				return $this->context->getLocale();
			}

			$this->locale = $this->getLocaleManager()->getLocale(locale_get_default());
		}

		return $this->locale;
	}

	/**
	 * Gets the locale manager.
	 *
	 * @return ILocaleManager
	 *	The locale manager.
	 */
	public function getLocaleManager() : ILocaleManager
	{
		return $this->getComponent('locale.manager');
	}

	/**
	 * Gets the memory cache.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public function getMemoryCache() : IMemoryCache
	{
		return $this->getComponent('data.cache.memory');
	}

	/**
	 * Gets the messages path.
	 *
	 * @return string
	 *	The messages path.
	 */
	public function getMessagesPath() : string
	{
		return $this->path . DIRECTORY_SEPARATOR . 'messages';
	}

	/**
	 * Gets a module.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return IModule
	 *	The module.
	 */
	public final function getModule(string $id) : IModule
	{
		if (!isset($this->modules[$id]))
		{
			throw new ModuleNotFoundContextException($this, sprintf('Can not get module, not found: "%s"', $id));
		}

		return $this->modules[$id];
	}

	/**
	 * Gets the modules.
	 *
	 * @return array
	 *	The modules.
	 */
	public final function getModules() : array
	{
		return $this->modules;
	}	

	/**
	 * Gets the modules path.
	 *
	 * @return string
	 *	The modules path.
	 */
	public function getModulesPath() : string
	{
		return $this->path . DIRECTORY_SEPARATOR . 'modules';
	}

	/**
	 * Gets the network cache.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public function getNetworkCache() : INetworkCache
	{
		return $this->getComponent('data.cache.network');
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public final function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public final function getSqlConnection() : ISqlConnection
	{
		return $this->getComponent('data.sql.connection');
	}

	/**
	 * Gets the theme.
	 *
	 * @return ITheme
	 *	The theme.
	 */
	public final function getTheme() : ?ITheme
	{
		if (!isset($this->theme))
		{
			$context = $this->context;

			while (isset($context))
			{
				if ($theme = $context->getTheme())
				{
					$this->theme = $theme;
					break;
				}

				$context = $context->getContext();
			}
		}

		return $this->theme;
	}

	/**
	 * Gets the themes path.
	 *
	 * @return string
	 *	The themes path.
	 */
	public function getThemesPath() : string
	{
		return $this->path . DIRECTORY_SEPARATOR . 'themes';
	}

	/**
	 * Gets the views path.
	 *
	 * @return string
	 *	The views path.
	 */
	public function getViewsPath() : string
	{
		return $this->path . DIRECTORY_SEPARATOR . 'views';
	}

	/**
	 * Checks if a controller is available.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasController(string $id) : bool
	{
		return class_exists($this->getControllerClassName($id));
	}

	/**
	 * Checks if a component is available.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasComponent(string $id) : bool
	{
		if (!isset($this->components[$id]) && !isset($this->componentsConfiguration[$id]))
		{
			if (isset($this->context))
			{
				return $this->context->hasComponent($id);
			}

			return false;
		}

		return true;
	}

	/**
	 * Checks if a module is available.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasModule(string $id) : bool
	{
		return isset($this->modules[$id]);
	}

	/**
	 * Sets the component configuration.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public final function setComponentConfiguration(string $id, ?array $configuration)
	{
		if (isset($configuration))
		{
			if (!isset($configuration['@class']) && isset($this->componentsConfiguration[$id], $this->componentsConfiguration[$id]['@class']))
			{
				$configuration['@class'] = $this->componentsConfiguration[$id]['@class'];
			}
		}

		$this->componentsConfiguration[$id] = $configuration;
	}

	/**
	 * Sets the components configuration.
	 *
	 * @param array $componentsConfiguration
	 *	The components configuration.
	 */
	public final function setComponentsConfiguration(array $componentsConfiguration)
	{
		foreach ($componentsConfiguration as $id => $configuration)
		{
			$this->setComponentConfiguration($id, $configuration);
		}
	}

	/**
	 * Sets the locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 */
	public final function setLocale(string $id) : void
	{
		$this->locale = $this->getLocaleManager()->getLocale($id);
	}

	/**
	 * Sets the theme.
	 *
	 * @param string $id
	 *	The theme identifier.
	 */
	public final function setTheme(?string $id) : void
	{
		if (isset($id))
		{
			$this->theme = new Theme
			(
				$this, 
				$id, 
				(new Asset($this->getThemesPath(), $id, null))->getPath()
			);
		}
		else
		{
			$this->theme = null;
		}
	}

	/**
	 * Resolves a route.
	 *
	 * A route is represented through a hybrid array holding a zero indexed
	 * action identifier and the parameters matching the criteria imposed by
	 * the action method signature.
	 *
	 * If a route is not provided, or the action identifier is missing, the
	 * default route will be used as applicable.
	 *
	 * @param array $route
	 *	The route to resolve.
	 *
	 * @return Action
	 *	The action.
	 */
	public final function resolve(?array $route) : Action
	{
		return (new Route($this, $route))->resolve();
	}

	/**
	 * Throwable handling.
	 *
	 * It is invoked automatically once a throwable is caught by the global
	 * handler, giving the controller the opportunity to generate the
	 * applicable error response.
	 *
	 * If the result is positivie, the throwable handling will not propagate
	 * to the parent contexts.
	 *
	 * @param Throwable $throwable
	 *	The throwable.
	 *
	 * @return bool
	 *	The result.
	 */
	public function throwable(Throwable $throwable) : bool
	{
		return false;
	}

	/**
	 * On After Dispose.
	 *
	 * It is invoked automatically during the context disposal procedure, 
	 * after modules and components are disposed.
	 */
	protected function onAfterDispose() : void
	{

	}

	/**
	 * On Dispose.
	 *
	 * It is invoked automatically during the component disposal procedure, 
	 * before the modules and components are disposed.
	 */
	protected function onDispose() : void
	{

	}
}