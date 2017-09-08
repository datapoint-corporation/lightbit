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
use \Lightbit\Base\Action;
use \Lightbit\Base\Cluster;
use \Lightbit\Base\ControllerNotFoundException;
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IElement;
use \Lightbit\Base\IEnvironment;
use \Lightbit\Base\ModuleNotFoundException;
use \Lightbit\Data\Caching\ICache;
use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;
use \Lightbit\Data\Caching\INetworkCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Data\Sql\ISqlConnection;
use \Lightbit\Exception;
use \Lightbit\Globalization\ILocale;
use \Lightbit\Globalization\IMessageSource;
use \Lightbit\Globalization\Locale;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Html\IHtmlAdapter;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpAssetManager;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\Security\Cryptography\IPasswordDigest;

/**
 * Context.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Context extends Cluster implements IContext
{
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
	 * The context.
	 *
	 * @type IContext
	 */
	private $context;

	/**
	 * The event listeners.
	 *
	 * @type array
	 */
	private $eventListeners;

	/**
	 * The identifier.
	 *
	 * @type string
	 */
	private $id;

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
	 * The context locale.
	 *
	 * @type ILocale
	 */
	private $locale;

	/**
	 * The modules.
	 *
	 * @type string
	 */
	private $modules;

	/**
	 * The modules base path.
	 *
	 * @type string
	 */
	private $modulesBasePath;

	/**
	 * The modules configuration.
	 *
	 * @type array
	 */
	private $modulesConfiguration;

	/**
	 * The plugins.
	 *
	 * @type array
	 */
	private $plugins;

	/**
	 * The plugins base path.
	 *
	 * @type string
	 */
	private $pluginsBasePath;

	/**
	 * The plugins configuration.
	 *
	 * @type array
	 */
	private $pluginsConfiguration;

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The application path.
	 *
	 * @param array $configuration
	 *	The application configuration.
	 */
	protected function __construct(?IContext $context, string $id, string $path, array $configuration = null)
	{
		parent::__construct($this, $path);

		$this->context = $context;
		$this->id = $id;

		$this->components = [];
		$this->componentsConfiguration = [];

		$this->eventListeners = [];

		$this->modules = [];
		$this->modulesConfiguration = [];

		$this->plugins = [];
		$this->pluginsConfiguration = [];

		$modulesBasePath = $this->getModulesBasePath();

		if (file_exists($modulesBasePath))
		{
			foreach (scandir($modulesBasePath) as $i => $id)
			{
				if ($id[0] === '.' || $id[0] === '_')
				{
					continue;
				}

				$this->getModule($id);
			}
		}

		$pluginsBasePath = $this->getPluginsBasePath();

		if (file_exists($pluginsBasePath))
		{
			foreach (scandir($pluginsBasePath) as $i => $id)
			{
				if ($id[0] === '.' || $id[0] === '_')
				{
					continue;
				}

				$this->getPlugin($id);
			}
		}

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}
	}

	private function _dependencies(array $requirements) : void
	{
		foreach ($requirements as $class => $subjects)
		{
			if ($class === 'modules')
			{
				foreach ($subjects as $i => $module)
				{
					$this->getModule($module);
				}
			}

			if ($class === 'plugins')
			{
				foreach ($subjects as $i => $plugin)
				{
					$this->getPlugin($plugin);
				}
			}
		}
	}

	/**
	 * Disposes the context.
	 */
	public function dispose() : void
	{
		foreach (array_reverse($this->modules) as $id => $module)
		{
			$module->dispose();
		}

		$this->modules = [];
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
				if ($this->context)
				{
					return $this->context->getComponent($id);
				}

				throw new ComponentNotFoundException($this, $id, sprintf('Component configuration not found: "%s"', $id));
			}

			if (!isset($this->componentsConfiguration[$id]['@class']))
			{
				throw new ComponentConfigurationException($this, $id, sprintf('Component class name is undefined: "%s"', $id));
			}

			$className = $this->componentsConfiguration[$id]['@class'];

			$component 
				= $this->components[$id] 
					= new $className($this, $id, $this->componentsConfiguration[$id]);

			if (($component instanceof IChannel) && $component->isClosed())
			{
				$component->start();
			}
		}

		return $this->components[$id];
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
	 * Gets the default route.
	 *
	 * @return array
	 *	The default route.
	 */
	public function getDefaultRoute() : array
	{
		return [ '@/default/index' ];
	}

	/**
	 * Gets the environment.
	 *
	 * @return IEnvironment
	 *	The environment.
	 */
	public final function getEnvironment() : IEnvironment
	{
		return $this->getComponent('environment');
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
	 * Gets the global identifier.
	 *
	 * @return string
	 *	The global identifier.
	 */
	public final function getGlobalID() : string
	{
		static $globalID;

		if (!$globalID)
		{
			$tokens = [];
			$context = $this;

			do
			{
				$tokens[] = $context->getID();
			}
			while ($context = $context->getContext());

			$globalID = implode('/', array_reverse($tokens));
		}

		return $globalID;
	}

	/**
	 * Gets the html adapter.
	 *
	 * @return IHtmlAdapter
	 *	The html adapter.
	 */
	public function getHtmlAdapter() : IHtmlAdapter
	{
		return $this->getComponent('html.adapter');
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
	 * Gets the http asset manager.
	 *
	 * @return IHttpAssetManager
	 *	The http asset manager.
	 */
	public function getHttpAssetManager() : IHttpAssetManager
	{
		return $this->getComponent('http.asset.manager');
	}

	/**
	 * Gets the http query string.
	 *
	 * @return IHttpQueryString
	 *	The http query string.
	 */
	public function getHttpQueryString() : IHttpQueryString
	{
		return $this->getComponent('http.query.string');
	}

	/**
	 * Gets the http request component.
	 *
	 * @param IHttpRequest
	 *	The http request component.
	 */
	public function getHttpRequest() : IHttpRequest
	{
		return $this->getComponent('http.request');
	}

	/**
	 * Gets the http response component.
	 *
	 * @param IHttpResponse
	 *	The http response component.
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
	 * Gets the http session.
	 *
	 * @return IHttpSession
	 *	The http session.
	 */
	public function getHttpSession() : IHttpSession
	{
		return $this->getComponent('http.session');
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
	 * Gets the layout.
	 *
	 * @return string
	 *	The layout.
	 */
	public final function getLayout() : ?string
	{
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
			$this->layoutPath = (new Alias($this->layout))->resolve('php', $this->getPath());
		}

		return $this->layoutPath;
	}

	/**
	 * Gets the locale.
	 *
	 * @return Locale
	 *	The locale.
	 */
	public final function getLocale() : ILocale
	{
		if ($this->locale)
		{
			return $this->locale;
		}

		if ($this->context)
		{
			return $this->context->getLocale();
		}

		throw new Exception(sprintf('Locale is not defined for base context: "%s"', $this->getPrefix));
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
	 * Gets the message source.
	 *
	 * @return IMessageSource
	 *	The message source.
	 */
	public function getMessageSource() : IMessageSource
	{
		return $this->getComponent('globalization.message.source');
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
			$modulePath = $this->getModulesBasePath() . DIRECTORY_SEPARATOR . $id;

			if (!file_exists($modulePath))
			{
				throw new ModuleNotFoundException($this, $id, sprintf('Module not found, not available: "%s", at context "%s"', $id, $this->getPrefix()));
			}

			$moduleConfigurationPath = $modulePath . DIRECTORY_SEPARATOR . 'module.php';

			if (!file_exists($moduleConfigurationPath))
			{
				throw new ModuleNotFoundException($this, $id, sprintf('Module not found, configuration script is missing: "%s", at context "%s"', $id, $this->getPrefix())); 
			}

			$moduleRequirementPath = $modulePath . DIRECTORY_SEPARATOR . 'module-dependencies.php';

			if (file_exists($moduleRequirementPath))
			{
				$moduleRequirement = Lightbit::inclusion()($moduleRequirementPath);

				if (is_array($moduleRequirement))
				{
					$this->_dependencies($moduleRequirement);
				}
			}

			$moduleConfiguration = Lightbit::inclusion()($moduleConfigurationPath);

			if (!isset($moduleConfiguration['@class']))
			{
				throw new ModuleNotFoundException($this, $id, sprintf('Module not found, configuration script is invalid: "%s", at context "%s"', $id, $this->getPrefix())); 
			}

			$className = $moduleConfiguration['@class'];
			return $this->modules[$id] = new $className($this, $id, $modulePath, $moduleConfiguration);
		}

		return $this->modules[$id];
	}

	/**
	 * Gets the modules base path.
	 *
	 * @return string
	 *	The modules base path.
	 */
	public final function getModulesBasePath() : string
	{
		if (!$this->modulesBasePath)
		{
			$this->modulesBasePath = $this->getPath() . DIRECTORY_SEPARATOR . 'modules';
		}

		return $this->modulesBasePath;
	}

	/**
	 * Gets the password digest.
	 *
	 * @return IPasswordDigest
	 *	The password digest.
	 */
	public function getPasswordDigest() : IPasswordDigest
	{
		return $this->getComponent('security.cryptography.password.digest');
	}

	/**
	 * Gets a plugin.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	public function getPlugin(string $id) : IPlugin
	{
		if (!isset($this->plugins[$id]))
		{
			$pluginPath = $this->getPluginsBasePath() . DIRECTORY_SEPARATOR . $id;

			if (!file_exists($pluginPath))
			{
				throw new PluginNotFoundException($this, $id, sprintf('Plugin not found, not available: "%s", at context "%s"', $id, $this->getPrefix()));
			}

			$pluginConfigurationPath = $pluginPath . DIRECTORY_SEPARATOR . 'plugin.php';

			if (!file_exists($pluginConfigurationPath))
			{
				throw new PluginNotFoundException($this, $id, sprintf('Plugin not found, configuration script is missing: "%s", at context "%s"', $id, $this->getPrefix())); 
			}

			$pluginRequirementPath = $pluginPath . DIRECTORY_SEPARATOR . 'plugin-dependencies.php';

			if (file_exists($pluginRequirementPath))
			{
				$pluginRequirement = Lightbit::inclusion()($pluginRequirementPath);

				if (is_array($pluginRequirement))
				{
					$this->_dependencies($pluginRequirement);
				}
			}

			$pluginConfiguration = Lightbit::inclusion()($pluginConfigurationPath);

			if (!isset($pluginConfiguration['@class']))
			{
				throw new PluginNotFoundException($this, $id, sprintf('Plugin not found, configuration script is invalid: "%s", at context "%s"', $id, $this->getPrefix())); 
			}

			$className = $pluginConfiguration['@class'];
			return $this->plugins[$id] = new $className($this, $id, $pluginPath, $pluginConfiguration);
		}

		return $this->plugins[$id];
	}

	/**
	 * Gets the plugins base path.
	 *
	 * @return string
	 *	The plugins base path.
	 */
	public final function getPluginsBasePath() : string
	{
		if (!$this->pluginsBasePath)
		{
			$this->pluginsBasePath = $this->getPath() . DIRECTORY_SEPARATOR . 'plugins';
		}

		return $this->pluginsBasePath;
	}

	/**
	 * Gets the prefix.
	 *
	 * @return string
	 *	The prefix.
	 */
	public final function getPrefix() : string
	{
		static $prefix;

		if (!$prefix)
		{
			$tokens = [];
			$context = $this;

			while ($previous = $context->getContext())
			{
				$tokens[] = $context->getID();
				$context = $previous;
			}

			$prefix = '/' . implode('/', array_reverse($tokens));
		}		

		return $prefix;
	}

	/**
	 * Gets the slug manager.
	 *
	 * @return ISlugManager
	 *	The slug manager.
	 */
	public function getSlugManager() : ISlugManager
	{
		return $this->getComponent('data.slug.manager');
	}

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection
	{
		return $this->getComponent('data.sql.connection');
	}

	/**
	 * Gets the views base paths.
	 *
	 * @return array
	 *	The views base paths.
	 */
	public final function getViewsBasePaths() : array
	{
		static $viewsBasePaths;

		if (!$viewsBasePaths)
		{
			$viewsBasePaths = $this->viewsBasePaths();
		}

		return $viewsBasePaths;
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
		return false;
	}

	/**
	 * Checks for a module availability.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function hasModule(string $id) : string
	{
		return isset($this->modules[$id]);
	}

	/**
	 * Sets an event listener.
	 *
	 * @param string $id
	 *	The event identifier.
	 *
	 * @param Closure $closure
	 *	The event listener callback.
	 */
	public final function on(string $id, \Closure $closure) : void
	{
		$this->eventListeners[$id][] = $closure;
	}

	/**
	 * Raises an event.
	 *
	 * @param string $id
	 *	The event identifier.
	 *
	 * @param mixed $arguments
	 *	The event arguments.
	 *
	 * @return array
	 *	The event results.
	 */
	public function raise(string $id, ...$arguments) : array
	{
		$results = [];

		if (isset($this->eventListeners[$id]))
		{
			foreach ($this->eventListeners[$id] as $i => $closure)
			{
				$result = $closure(...$arguments);

				if (isset($result))
				{
					$results[] = $result;
				}
			}
		}

		if ($this->context)
		{
			$propagation = $this->context->raise($id, ...$arguments);

			if ($propagation)
			{
				$results = ($results ? $propagation : array_merge($results, $propagation));
			}
		}

		return $results;
	}

	/**
	 * Sets a component configuration.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 *
	 * @param bool $merge
	 *	The components configuration merge flag.
	 */
	public function setComponentConfiguration(string $id, array $configuration, bool $merge = true) : void
	{
		$this->componentsConfiguration[$id]
			= ($merge && isset($this->componentsConfiguration[$id]))
			? array_replace_recursive($this->componentsConfiguration[$id], $configuration)
			: $configuration;
	}

	/**
	 * Sets the components configuration.
	 *
	 * @param array $componentsConfiguration
	 *	The components configuration.
	 *
	 * @param bool $merge
	 *	The components configuration merge flag.
	 */
	public function setComponentsConfiguration(array $componentsConfiguration, bool $merge = true) : void
	{
		foreach ($componentsConfiguration as $id => $configuration)
		{
			$this->setComponentConfiguration($id, $configuration, $merge);
		}
	}

	/**
	 * Sets the layout.
	 *
	 * @param string $layout
	 *	The layout.
	 */
	public function setLayout(?string $layout) : void
	{
		$this->layout = $layout;
		$this->layoutPath = null;
	}

	/**
	 * Sets the locale.
	 *
	 * @param string $id
	 *	The locale identifier.
	 */
	public final function setLocale(string $id) : void
	{
		$this->locale = Locale::getLocale($id);
	}

	/**
	 * Sets a module configuration.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @param array $configuration
	 *	The module configuration.
	 *
	 * @param bool $merge
	 *	The module configuration merge flag.
	 */
	public final function setModuleConfiguration(string $id, array $configuration, bool $merge = true) : void
	{
		if (isset($this->modules[$id]))
		{
			throw new Exception(sprintf('Module is already in use: "%s"', $id));
		}

		if (!isset($this->modulesConfiguration[$id]))
		{
			throw new Exception(sprintf('Module is not available: "%s"', $id));
		}

		if (isset($configuration['@class']))
		{
			throw new Exception(sprintf('Module class can not be changed: "%s"', $id));
		}

		$this->modulesConfiguration[$id] = [ '@class' => $this->modulesConfiguration[$id]['@class'] ] + 
		(
			$merge
			? array_replace_recursive($this->modulesConfiguration[$id], $configuration) 
			: $configuration
		);
	}

	/**
	 * Sets the modules configuration.
	 *
	 * @param array $modulesConfiguration
	 *	The modules configuration.
	 *
	 * @param bool $merge
	 *	The module configuration merge flag.
	 */
	public final function setModulesConfiguration(array $modulesConfiguration, bool $merge = true) : void
	{
		foreach ($modulesConfiguration as $id => $configuration)
		{
			$this->setModuleConfiguration($id, $configuration, $merge);
		}
	}

	/**
	 * Sets a plugin configuration.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @param array $configuration
	 *	The plugin configuration.
	 *
	 * @param bool $merge
	 *	The plugin configuration merge flag.
	 */
	public final function setPluginConfiguration(string $id, array $configuration, bool $merge = true) : void
	{
		if (isset($this->plugins[$id]))
		{
			throw new Exception(sprintf('Plugin is already in use: "%s"', $id));
		}

		if (!isset($this->pluginsConfiguration[$id]))
		{
			throw new Exception(sprintf('Plugin is not available: "%s"', $id));
		}

		if (isset($configuration['@class']))
		{
			throw new Exception(sprintf('Plugin class can not be changed: "%s"', $id));
		}

		$this->pluginsConfiguration[$id] = [ '@class' => $this->pluginsConfiguration[$id]['@class'] ] + 
		(
			$merge
			? array_replace_recursive($this->pluginsConfiguration[$id], $configuration) 
			: $configuration
		);
	}

	/**
	 * Sets the plugins configuration.
	 *
	 * @param array $modulesConfiguration
	 *	The plugins configuration.
	 *
	 * @param bool $merge
	 *	The plugins configuration merge flag.
	 */
	public final function setPluginsConfiguration(array $pluginsConfiguration, bool $merge = true) : void
	{
		foreach ($pluginsConfiguration as $id => $configuration)
		{
			$this->setPluginConfiguration($id, $configuration, $merge);
		}
	}

	/**
	 * Creates the views base paths collection.
	 *
	 * @return array
	 *	The views base paths collection.
	 */
	protected function viewsBasePaths() : array
	{
		$result = [ $this->getPath() . DIRECTORY_SEPARATOR . 'views' ];

		$layoutPath = $this->getLayoutPath();

		if ($layoutPath)
		{
			array_unshift($result, dirname($layoutPath) . DIRECTORY_SEPARATOR . 'views');
		}

		return $result;
	}
}