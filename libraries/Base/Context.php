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
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IContext;
use \Lightbit\Base\Cluster;
use \Lightbit\Base\IElement;
use \Lightbit\Base\ControllerNotFoundException;
use \Lightbit\Data\ICache;
use \Lightbit\Data\IFileCache;
use \Lightbit\Data\IMemoryCache;
use \Lightbit\Data\INetworkCache;
use \Lightbit\Data\ISlugManager;
use \Lightbit\Helpers\ObjectHelper;
use \Lightbit\Html\IHtmlAdapter;
use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Http\IHttpQueryString;
use \Lightbit\Http\IHttpRequest;
use \Lightbit\Http\IHttpResponse;
use \Lightbit\Http\IHttpRouter;
use \Lightbit\Http\IHttpSession;
use \Lightbit\Base\Exception;
use \Lightbit\IO\FileSystem\Alias;
use \Lightbit\Base\ModuleNotFoundException;

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
	 * The modules.
	 *
	 * @type string
	 */
	private $modules;

	/**
	 * The modules path.
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
	 * The plugins requirements.
	 *
	 * @type array
	 */
	private $pluginsRequirements;

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

		$this->modules = [];
		$this->modulesConfiguration = [];

		$this->plugins = [];
		$this->pluginsConfiguration = [];

		$pluginsBasePath = $this->getPluginsBasePath();
		$pluginsPath = [];

		if (file_exists($pluginsBasePath))
		{
			foreach (scandir($pluginsBasePath) as $i => $id)
			{
				if ($id[0] == '.')
				{
					continue;
				}

				$pluginPath = $pluginsBasePath . DIRECTORY_SEPARATOR . $id;

				if (!is_dir($pluginPath))
				{
					continue;
				}

				$pluginRequirementsPath = $pluginPath . DIRECTORY_SEPARATOR . 'requirements.php';

				if (file_exists($pluginRequirementsPath))
				{
					$pluginRequirements = Lightbit::inclusion()($pluginRequirements);

					if (!is_array($pluginRequirements))
					{
						throw new Exception(sprintf('Plugin requirements script is not valid: "%s"', $id));
					}

					$this->pluginRequirements[$id] = $pluginRequirements;
				}

				$pluginConfigurationPath = $pluginPath . DIRECTORY_SEPARATOR . 'plugin.php';

				if (!file_exists($pluginConfigurationPath))
				{
					throw new Exception(sprintf('Plugin configuration script not found: "%s"', $id));
				}

				$pluginConfiguration = Lightbit::inclusion()($pluginConfigurationPath);

				$this->pluginsConfiguration[$id] = is_array($pluginConfiguration)
					? $pluginConfiguration
					: [];

				if (!isset($this->pluginsConfiguration[$id]['@class']))
				{
					$this->pluginsConfiguration[$id]['@class'] = Plugin::class;	
				}

				$pluginsPath[$id] = $pluginPath;
			}
		}

		$modulesBasePath = $this->getModulesBasePath();
		$modulesPath = [];

		if (file_exists($modulesBasePath))
		{
			foreach (scandir($modulesBasePath) as $i => $id)
			{
				if ($id[0] == '.')
				{
					continue;
				}

				$modulePath = $modulesBasePath . DIRECTORY_SEPARATOR . $id;

				if (!is_dir($modulePath))
				{
					continue;
				}

				$moduleConfigurationPath = $modulePath . DIRECTORY_SEPARATOR . 'module.php';

				if (!file_exists($moduleConfigurationPath))
				{
					throw new Exception(sprintf('Module configuration script not found: "%s"', $id));
				}

				$moduleConfiguration = Lightbit::inclusion()($moduleConfigurationPath);
				
				if (!is_array($moduleConfiguration) || !isset($moduleConfiguration['@class']))
				{
					throw new Exception(sprintf('Module configuration script is not valid: "%s"', $id));
				}

				$this->modulesConfiguration[$id] = $moduleConfiguration;
				$modulesPath[$id] = $modulePath;
			}
		}

		if ($configuration)
		{
			ObjectHelper::configure($this, $configuration);
		}

		foreach ($pluginsPath as $id => $pluginPath)
		{
			$this->plugin($pluginsBasePath, $pluginPath, $id);
		}

		foreach ($this->modulesConfiguration as $id => $configuration)
		{
			$this->modules[$id] 
				= new $configuration['@class']
					($this, $id, $modulesPath[$id], $configuration);
		}
	}

	/**
	 * Loads a plugin recursively.
	 *
	 * @param string $pluginsBasePath
	 *	The plugins base path.
	 *
	 * @param string $pluginPath
	 *	The plugin path.
	 *
	 * @param string $id
	 *	The plugin identifier.
	 *
	 * @return IPlugin
	 *	The plugin.
	 */
	private function plugin(string $pluginsBasePath, string $pluginPath, string $id) : IPlugin
	{
		if (!isset($this->plugins[$id]))
		{
			if (isset($this->pluginsRequirements[$id]))
			{
				if (isset($this->pluginsRequirements[$id]['plugins']))
				{
					foreach ($plugins as $i => $requiredPluginID)
					{
						$this->plugin($pluginsBasePath, $pluginPath, $requiredPluginID);
					}
				}
			}

			return $this->plugins[$id] 
				= new $this->pluginsConfiguration[$id]['@class']
					($this, $id, $pluginPath, $this->pluginsConfiguration[$id]);
		}

		return $this->plugins[$id];
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

				throw new Exception(sprintf('Component configuration not found: "%s"', $id));
			}

			if (!isset($this->componentsConfiguration[$id]['@class']))
			{
				throw new Exception(sprintf('Component class name not available: "%s" ("@class")', $id));
			}

			$className = $this->componentsConfiguration[$id]['@class'];

			$component 
				= $this->components[$id] 
					= new $className($this, $id, $this->componentsConfiguration[$id]);

			if ($component instanceof IResource)
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
			throw new ModuleNotFoundException($this, $id, sprintf('Module not found: "%s", at context "%s"', $id, $this->getPrefix()));
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
			throw new Exception(sprintf('Plugin is not available: "%s"', $id));
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
		return [ $this->getPath() . DIRECTORY_SEPARATOR . 'views' ];
	}
}