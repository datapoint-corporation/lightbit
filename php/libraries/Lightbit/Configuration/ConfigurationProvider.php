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

namespace Lightbit\Configuration;

use \Lightbit\Configuration\IConfiguration;
use \Lightbit\Configuration\IConfigurationFactory;

/**
 * ConfigurationProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class ConfigurationProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var ConfigurationProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return ConfigurationProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : ConfigurationProvider
	{
		return (self::$instance ?? (self::$instance = new ConfigurationProvider()));
	}

	/**
	 * The configuration factory.
	 *
	 * @var IConfigurationFactory
	 */
	private $configurationFactory;

	/**
	 * The configuration map.
	 *
	 * @var array
	 */
	private $configurationMap;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->configurationMap = [];
	}

	/**
	 * Gets a configuration.
	 *
	 * @throws ConfigurationFactoryException
	 *	Thrown if the configuration creation fails.
	 *
	 * @param string $configuration
	 *	The configuration resource identifier.
	 *
	 * @return IConfiguration
	 *	The configuration.
	 */
	public final function getConfiguration(string $configuration) : IConfiguration
	{
		return ($this->configurationMap[$configuration] ?? (
			$this->configurationMap[$configuration] = $this->getConfigurationFactory()->createConfiguration($configuration)
		));
	}

	/**
	 * Gets the configuration factory.
	 *
	 * @return IConfigurationFactory
	 *	The configuration factory.
	 */
	public final function getConfigurationFactory() : IConfigurationFactory
	{
		return ($this->configurationFactory ?? ($this->configurationFactory = new ConfigurationFactory()));
	}

	/**
	 * Sets the configuration factory.
	 *
	 * @param IConfigurationFactory $configurationFactory
	 *	The configuration factory.
	 */
	public final function setConfigurationFactory(IConfigurationFactory $configurationFactory) : void
	{
		$this->configurationFactory = $configurationFactory;
		$this->configurationMap = [];
	}
}
