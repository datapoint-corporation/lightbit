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

use \Lightbit;
use \Lightbit\Action;
use \Lightbit\Base\IApplication;
use \Lightbit\Base\IController;
use \Lightbit\Base\IPlugin;
use \Lightbit\Base\IResource;
use \Lightbit\Base\Object;
use \Lightbit\Base\Plugin;
use \Lightbit\Data\ICache;
use \Lightbit\Data\IFileCache;
use \Lightbit\Data\IMemoryCache;
use \Lightbit\Data\INetworkCache;
use \Lightbit\Data\NoCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Data\SlugManager;
use \Lightbit\Exception;
use \Lightbit\Http\HttpQueryString;
use \Lightbit\Http\HttpResponse;
use \Lightbit\Http\HttpRequest;
use \Lightbit\Http\HttpSession;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;
use \Lightbit\Http\QueryStringHttpRouter;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\RouteException;

/**
 * Application.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Application extends Object implements IApplication
{
	/**
	 * The controllers.
	 *
	 * @type array
	 */
	private $controllers;

	/**
	 * The components.
	 *
	 * @type array
	 */
	private $components;

	/**
	 * The components configuration.
	 *
	 * @type array
	 */
	private $componentsConfiguration;

	/**
	 * The layout.
	 *
	 * @type string
	 */
	private $layout;

	/**
	 * The layout path.
	 *
	 * @type string
	 */
	private $layoutPath;

	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * The plugins.
	 *
	 * @type array
	 */
	private $plugins;

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The path.
	 *
	 * @param array $configuration
	 *	The configuration.
	 */
	public function __construct(string $path, array $configuration = null)
	{
		$this->controllers = [];
		$this->components = [];
		$this->path = $path;
		$this->plugins = [];

		// Before the application is configured, we'll have to load all
		// plugins as some may defined classes required for this procedure.
		$pluginsPath = $this->getPluginsPath();

		if (file_exists($pluginsPath))
		{
			$entries = scandir($pluginsPath);

			if ($entries)
			{
				foreach ($entries as $i => $plugin)
				{
					if ($plugin[0] == '.' || $plugin[0] == '~' || $plugin[0] == '_')
					{
						continue;
					}

					$this->plugin($pluginsPath, $plugin, false);
				}
			}
		}

		// Set the components default configuration.
		$this->componentsConfiguration =
		[
			'data.cache' => [ '@class' => NoCache::class ],
			'data.cache.file' => [ '@class' => NoCache::class ],
			'data.cache.memory' => [ '@class' => NoCache::class ],
			'data.cache.network' => [ '@class' => NoCache::class ],
			'data.slug.manager' => [ '@class' => SlugManager::class ],
			'http.query.string' => [ '@class' => HttpQueryString::class ],
			'http.request' => [ '@class' => HttpRequest::class ],
			'http.response' => [ '@class' => HttpRequest::class ],
			'http.router' => [ '@class' => QueryStringHttpRouter::class ],
			'http.session' => [ '@class' => HttpSession::class ]
		];

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	/**
	 * Creates a controller class name.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return string
	 *	The controller class name.
	 */
	protected function controllerClassName(string $id) : string
	{
		return $controllersClassName[$id]
			= $this->getNamespaceName()
			. '\\Controllers\\'
			. strtr(ucwords(strtr($id, [ '-' =>  ' ', '/' => ' \\ '])), [ ' ' => '' ])
			. 'Controller';
	}

	/**
	 * Diposes the application by closing any resources in reverse order as they
	 * were started in order to ensure 
	 */
	public function dispose() : void
	{
		foreach (array_reverse($this->components) as $i => $component)
		{
			if ($component instanceof IResource)
			{
				if (!$component->isClosed())
				{
					$component->close();
				}
			}
		}
	}

	/**
	 * Gets the cache.
	 *
	 * @return ICache
	 *	The cache.
	 */
	public final function getCache() : ICache
	{
		return $this->getComponent('data.cache');
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
			list($container, $className) = $this->getControllerMeta($id);
			$this->controllers[$id] = new $className($container, $id, null);
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
	public final function getControllerClassName(string $id) : string
	{
		return $this->getControllerMeta($id)[1];
	}

	/**
	 * Gets a controller meta.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return array
	 *	The controller meta.
	 */
	private function getControllerMeta(string $id) : array
	{
		static $controllersMeta = [];

		if (!isset($controllersMeta[$id]))
		{
			$selfClassName = $this->controllerClassName($id);

			if (!Lightbit::hasClass($selfClassName))
			{
				foreach ($this->getPlugins() as $i => $plugin)
				{
					$className = $plugin->getControllerClassName($id);

					if (Lightbit::hasClass($className))
					{
						return $controllersMeta[$id] = [ $plugin, $className ];
					}
				}
			}

			$controllersMeta[$id] = [ $this, $selfClassName ];
		}

		return $controllersMeta[$id];
	}

	/**
	 * Gets a component.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param bool $start
	 *	The start flag which, when set, will cause a component/resource
	 *	to be started before being returned for use.
	 *
	 * @return IComponent
	 *	The component.
	 */
	public final function getComponent(string $id, bool $start = true) : IComponent
	{
		if (!isset($this->components[$id]))
		{
			if (!isset($this->componentsConfiguration[$id]))
			{
				throw new Exception(sprintf('Component configuration not found: "%s"', $id));
			}

			if (!isset($this->componentsConfiguration[$id]['@class']))
			{
				throw new Exception(sprintf('Component class name not available: "%s" ("@class")', $id));
			}

			$className = $this->componentsConfiguration[$id]['@class'];

			$component 
				= $this->components[$id] 
					= new $className($id, $this->componentsConfiguration[$id]);

			if ($component instanceof IResource)
			{
				$component->start();
			}
		}

		return $this->components[$id];
	}

	/**
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	protected function getDefaultRoute() : array
	{
		return [ '/site/index' ];
	}

	/**
	 * Gets the file cache.
	 *
	 * @return IFileCache
	 *	The file cache.
	 */
	public final function getFileCache() : IFileCache
	{
		return $this->getComponent('data.cache.file');
	}

	/**
	 * Gets the http query string.
	 *
	 * @return IHttpQueryString
	 *	The http query string.
	 */
	public final function getHttpQueryString() : IHttpQueryString
	{
		return $this->getComponent('http.query.string');
	}

	/**
	 * Gets the http request.
	 *
	 * @return IHttpRequest
	 *	The http request.
	 */
	public final function getHttpRequest() : IHttpRequest
	{
		return $this->getComponent('http.request');
	}

	/**
	 * Gets the http router.
	 *
	 * @return IHttpRouter
	 *	The http router.
	 */
	public final function getHttpRouter() : IHttpRouter
	{
		return $this->getComponent('http.router');
	}

	/**
	 * Gets the http session.
	 *
	 * @return IHttpSession
	 *	The http session.
	 */
	public final function getHttpSession() : IHttpSession
	{
		return $this->getComponent('http.session');
	}

	/**
	 * Gets the layout.
	 *
	 * @return string
	 *	The layout.
	 */
	public final function getLayout() : ?string
	{
		if (!$this->layout)
		{
			return $this->getApplication()->getLayout();
		}

		return $this->layout;
	}

	/**
	 * Gets the layout path.
	 *
	 * @return string
	 *	The layout path.
	 */
	public final function getLayoutPath() : ?string
	{
		if ($this->layout && !$this->layoutPath)
		{
			$this->layoutPath = (new Alias($this->layout))
				->resolve('php', $this->getPath());
		}

		return $this->layoutPath;
	}

	/**
	 * Gets the memory cache.
	 *
	 * @return IMemoryCache
	 *	The memory cache.
	 */
	public final function getMemoryCache() : IMemoryCache
	{
		return $this->getComponent('data.cache.memory');
	}

	/**
	 * Gets the namespace name.
	 *
	 * @return string
	 *	The namespace name.
	 */
	public final function getNamespaceName() : string
	{
		static $result;

		if (!$result)
		{
			$result = Lightbit::getClassNamespaceName(static::class);
		}

		return $result;
	}

	/**
	 * Gets the network cache.
	 *
	 * @return INetworkCache
	 *	The network cache.
	 */
	public final function getNetworkCache() : INetworkCache
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
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public function getViewsBasePaths() : array
	{
		return [ $this->getPath() . DIRECTORY_SEPARATOR . 'views' ];
	}

	/**
	 * Gets a plugin.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	public function getPlugin(string $id) : IPlugin
	{
		if (!isset($this->plugins[$id]))
		{
			throw new Exception(sprintf('Plugin not found: "%s"', $id));
		}

		return $this->plugins[$id];
	}

	/**
	 * Gets the plugins.
	 *
	 * @return array
	 *	The plugins.
	 */
	public function getPlugins() : array
	{
		return $this->plugins;
	}

	/**
	 * Gets the plugins path.
	 *
	 * @return string
	 *	The plugins path.
	 */
	protected function getPluginsPath() : string
	{
		static $result;

		if (!$result)
		{
			$result = $this->getPath() . DIRECTORY_SEPARATOR . 'plugins';
		}

		return $result;
	}

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public final function getSlugManager() : ISlugManager
	{
		return $this->getComponent('data.slug.manager');
	}

	/**
	 * Checks a controller availability.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasController(string $id) : bool
	{
		return Lightbit::hasClass($this->getControllerClassName($id));
	}

	/**
	 * Checks a component availability.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasComponent(string $id) : bool
	{
		return isset($this->components[$id])
			|| isset($this->componentsConfiguration[$id]);
	}

	/**
	 * Checks a plugin availability.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPlugin(string $id) : bool
	{
		return isset($this->plugins[$id]);
	}

	/**
	 * Sets the component configuration.
	 *
	 * @param string $id
	 *	The identifier.
	 *
	 * @param array $configuration
	 *	The configuration.
	 *
	 * @param bool $merge
	 *	The merge flag which, when true, will cause the given configuration
	 *	to be merged with the existing.
	 */
	public final function setComponentConfiguration(string $id, array $configuration, bool $merge = true) : void
	{
		$this->componentsConfiguration[$id]
			= ($merge && isset($this->componentsConfiguration[$id])) 
			? array_replace_recursive($this->componentsConfiguration[$id], $configuration)
			: $configuration;
	}

	/**
	 * Sets the components configuration.
	 *
	 * @param array $configuration
	 *	The configuration.
	 *
	 * @param bool $merge
	 *	The merge flag which, when true, will cause the given configuration
	 *	to be merged with the existing.
	 */
	public final function setComponentsConfiguration(array $configuration, bool $merge = true) : void
	{
		if (!$merge)
		{
			$this->componentsConfiguration = [];
		}

		foreach ($configuration as $id => $configuration)
		{
			$this->componentsConfiguration[$id]
				= ($merge && isset($this->componentsConfiguration[$id])) 
				? array_replace_recursive($this->componentsConfiguration[$id], $configuration)
				: $configuration;
		}
	}

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public final function setLayout(?string $layout) : void
	{
		$this->layout = $layout;
		$this->layoutPath = null;
	}

	/**
	 * Loads a plugin.
	 *
	 * @param string $pluginsPath
	 *	The plugins path.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @param bool $require
	 *	The require flag.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	private function plugin(string $pluginsPath, string $id, bool $require) : ?IPlugin
	{
		// Due to the dependency management, it's possible this plugin
		// has already been loaded despite being further down the list.
		if (isset($this->plugins[$id]))
		{
			return $this->plugins[$id];
		}

		// The plugin "plugin.php" script file is required and will
		// provide us with the default configuration.
		$pluginPath = $pluginsPath . DIRECTORY_SEPARATOR . $id;
		$pluginScriptPath = $pluginPath . DIRECTORY_SEPARATOR . 'plugin.php';

		if (!file_exists($pluginScriptPath))
		{
			if ($require)
			{
				throw new Exception(sprintf('Plugin not found: "%s"', $id, $pluginScriptPath));
			}

			return null;
		}

		// The plugin "require.php", if it exists, will provide us
		// with a list of dependencies.
		$pluginDependencyScriptPath = $pluginPath . DIRECTORY_SEPARATOR . 'require.php';

		if (file_exists($pluginDependencyScriptPath))
		{
			$pluginDependecy = Lightbit::inclusion()($pluginDependencyScriptPath);

			if (is_array($pluginDependecy))
			{
				if (isset($pluginDependecy['plugins']))
				{
					foreach ($pluginDependecy['plugins'] as $i => $dependency)
					{
						$this->plugin($pluginsPath, $dependency, true);
					}
				}
			}
		}

		// Get the plugin configuration and, if available, override the
		// class to the one provided by it.
		$pluginConfiguration = Lightbit::inclusion()($pluginScriptPath);
		$pluginClassName = Plugin::class;

		if (is_array($pluginConfiguration))
		{
			if (isset($pluginConfiguration['@class']))
			{
				$pluginClassName = $pluginConfiguration['@class'];
			}
		}
		else
		{
			$pluginConfiguration = null;
		}

		return $this->plugins[$id] = new $pluginClassName($this, $id, $pluginPath, $pluginConfiguration);
	}

	/**
	 * Resolves a route to a controller action.
	 *
	 * @param array $route
	 *	The route to resolve.
	 *
	 * @return Action
	 *	The action.
	 */
	public final function resolve(array $route = null) : Action
	{
		if (!isset($route))
		{
			$route = $this->getDefaultRoute();
		}

		else if (!isset($route[0]))
		{
			$route = $this->getDefaultRoute() + $route;
		}

		$path = $route[0];
		$i;

		if (!$path || $path[0] != '/' || !($i = strrpos($path, '/')))
		{
			throw new RouteException($route, sprintf('Bad route path format: "%s" (must be "/<controller-id>/<action-name>")', $path));
		}

		$controllerID = substr($path, 1, $i - 1);
		$actionName = substr($path, $i + 1);

		if (!$this->hasController($controllerID))
		{
			throw new RouteException($route, sprintf('Bad route path, controller not found: "%s" (controller "%s")', $path, $controllerID));
		}

		$parameters = $route;
		unset($parameters[0]);

		return $this->getController($controllerID)->resolve($actionName, $parameters);
	}

	/**
	 * Runs the application.
	 *
	 * @return int
	 *	The status code.
	 */
	public function run() : int
	{
		$result = 0;

		if (Lightbit::isCli())
		{
			$result = $this->resolve($this->getDefaultRoute())->run();
		}

		else
		{
			$result = $this->getHttpRouter()->resolve()->run();
		}

		return (is_int($result) ? $result : 0);
	}

	/**
	 * Disposes the application by closing any resources started through it
	 * in proper order, due to their dependencies, before terminating the
	 * script execution.
	 *
	 * @param int $status
	 *	The exit status code.
	 */
	public function terminate(int $status) : void
	{
		$this->dispose();

		exit($status);
	}
}
