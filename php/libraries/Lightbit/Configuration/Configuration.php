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

/**
 * Configuration.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class Configuration implements IConfiguration
{
	/**
	 * The configuration.
	 *
	 * @var array
	 */
	private $configuration;

	/**
	 * Constructor.
	 *
	 * @param array $configuration
	 *	The configuration properties map.
	 */
	public function __construct(array $configuration = null)
	{
		$this->configuration = ($configuration ?? []);
	}

	/**
	 * Accepts a configuration to an object.
	 *
	 * @param object $subject
	 *	The configuration subject.
	 *
	 * @param array $configuration
	 *	The configuration setter methods, by property.
	 */
	public function accept(object $subject, array $configuration) : void
	{
		foreach ($configuration as $property => $method)
		{
			if (isset($this->configuration[$property]))
			{
				$subject->{$method}($this->configuration[$property]);
			}
		}
	}

	/**
	 * Gets an array.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @param array $default
	 *	The property default value.
	 *
	 * @return double
	 *	The value.
	 */
	public function getArray(string $property, bool $optional = false, array $default = null) : ?array
	{
		if (isset($this->configuration[$property]))
		{
			if (is_array($this->configuration[$property]))
			{
				return $this->configuration[$property];
			}

			throw new ConfigurationException(
				$this,
				sprintf(
					'Can not get configuration property, wrong type for property value: "%s", got a "%s", expecting a "%s"',
					$property,
					gettype($this->configuration[$property]),
					'array'
				)
			);
		}

		if ($optional)
		{
			return $default;
		}

		throw new ConfigurationException(
			$this,
			sprintf(
				'Can not get configuration property, it is not set: "%s", expecting a "%s"',
				$property,
				'array'
			)
		);
	}

	/**
	 * Gets a double precision point number.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @param double $default
	 *	The property default value.
	 *
	 * @return double
	 *	The value.
	 */
	public function getDouble(string $property, bool $optional = false, double $default = null) : ?double
	{
		if (isset($this->configuration[$property]))
		{
			if (is_float($this->configuration[$property]))
			{
				return $this->configuration[$property];
			}

			throw new ConfigurationException(
				$this,
				sprintf(
					'Can not get configuration property, wrong type for property value: "%s", got a "%s", expecting a "%s"',
					$property,
					gettype($this->configuration[$property]),
					'float'
				)
			);
		}

		if ($optional)
		{
			return $default;
		}

		throw new ConfigurationException(
			$this,
			sprintf(
				'Can not get configuration property, it is not set: "%s", expecting a "%s"',
				$property,
				'float'
			)
		);
	}

	/**
	 * Gets a floating point number.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @param float $default
	 *	The property default value.
	 *
	 * @return float
	 *	The value.
	 */
	public function getFloat(string $property, bool $optional = false, float $default = null) : ?float
	{
		if (isset($this->configuration[$property]))
		{
			if (is_float($this->configuration[$property]))
			{
				return $this->configuration[$property];
			}

			throw new ConfigurationException(
				$this,
				sprintf(
					'Can not get configuration property, wrong type for property value: "%s", got a "%s", expecting a "%s"',
					$property,
					gettype($this->configuration[$property]),
					'float'
				)
			);
		}

		if ($optional)
		{
			return $default;
		}

		throw new ConfigurationException(
			$this,
			sprintf(
				'Can not get configuration property, it is not set: "%s", expecting a "%s"',
				$property,
				'float'
			)
		);
	}

	/**
	 * Gets an integer.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @param int $default
	 *	The property default value.
	 *
	 * @return int
	 *	The value.
	 */
	public function getInteger(string $property, bool $optional = false, int $default = null) : ?int
	{
		if (isset($this->configuration[$property]))
		{
			if (is_int($this->configuration[$property]))
			{
				return $this->configuration[$property];
			}

			throw new ConfigurationException(
				$this,
				sprintf(
					'Can not get configuration property, wrong type for property value: "%s", got a "%s", expecting a "%s"',
					$property,
					gettype($this->configuration[$property]),
					'int'
				)
			);
		}

		if ($optional)
		{
			return $default;
		}

		throw new ConfigurationException(
			$this,
			sprintf(
				'Can not get configuration property, it is not set: "%s", expecting a "%s"',
				$property,
				'int'
			)
		);
	}

	/**
	 * Gets a string.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @param string $default
	 *	The property default value.
	 *
	 * @return string
	 *	The value.
	 */
	public function getString(string $property, bool $optional = false, string $default = null) : ?string
	{
		if (isset($this->configuration[$property]))
		{
			if (is_string($this->configuration[$property]))
			{
				return $this->configuration[$property];
			}

			throw new ConfigurationException(
				$this,
				sprintf(
					'Can not get configuration property, wrong type for property value: "%s", got a "%s", expecting a "%s"',
					$property,
					gettype($this->configuration[$property]),
					'string'
				)
			);
		}

		if ($optional)
		{
			return $default;
		}

		throw new ConfigurationException(
			$this,
			sprintf(
				'Can not get configuration property, it is not set: "%s", expecting a "%s"',
				$property,
				'string'
			)
		);
	}
}
