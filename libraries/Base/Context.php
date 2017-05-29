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
use \Lightbit\Base\IComponent;
use \Lightbit\Base\IContext;
use \Lightbit\Base\Cluster;
use \Lightbit\Exception;
use \Lightbit\Helpers\ObjectHelper;

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
	private $component;

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
		parent::__construct($path);

		$this->context = $context;
		$this->id = $id;

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
		if ($this->context)
		{
			return $this->context->getComponent($id);
		}

		throw new Exception(sprintf('Component is not available: "%s"'));
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
			throw new Exception(sprintf('Module not found: "%s"', $id));
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
			throw new Exception(sprintf('Plugin is not available: "%s"'));
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
}