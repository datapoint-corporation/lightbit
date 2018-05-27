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

use \Throwable;

use \Lightbit\Data\Collections\StringMap;
use \Lightbit\Configuration\ConfigurationException;
use \Lightbit\Configuration\ConfigurationKeyNotSetException;
use \Lightbit\Configuration\ConfigurationKeyTypeException;
use \Lightbit\Configuration\IConfiguration;

/**
 * IConfiguration.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class Configuration implements IConfiguration
{
	/**
	 * The map.
	 *
	 * @var StringMap
	 */
	private $configuration;

	/**
	 * Constructor.
	 *
	 * @param array $propertyMap
	 *	The configuration property map.
	 */
	public function __construct(array $propertyMap = null)
	{
		$this->map = new StringMap($propertyMap);
	}

	/**
	 * Accepts a configuration into a subject object, by invoking the
	 * matching setter method for each existing property.
	 *
	 * @throws ConfigurationException
	 *	Thrown when a property fails to be set.
	 *
	 * @param object $subject
	 *	The configuration subject.
	 *
	 * @param array $propertyMethodMap
	 *	The configuration property method map.
	 */
	public function accept(object $subject, array $propertyMethodMap) : void
	{
		$propertyMap = $this->map->toArray();

		foreach ($propertyMethodMap as $property => $method)
		{
			try
			{
				if (isset($propertyMap[$property]))
				{
					$subject->{$method}($propertyMap[$property]);
				}
			}
			catch (Throwable $e)
			{
				throw new ConfigurationException(
					$this,
					sprintf(
						'Can not set configurable property, uncaught throwable: "%s"',
						$property
					),
					$e
				);
			}
		}
	}

	/**
	 * Gets a boolean.
	 *
	 * @throws ConfigurationPropertyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws ConfigurationPropertyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a boolean.
	 *
	 * @throws ConfigurationPropertyValueTypeMismatchException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @return bool
	 *	The property value.
	 */
	public final function getBool(string $property, bool $optional = false) : ?bool
	{
		try
		{
			return $this->map->getBool($property, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationPropertyNotSetException($this, sprintf(
				'Can not get configuration property, not set: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueParseException $e)
		{
			throw new ConfigurationPropertyValueParseException($this, sprintf(
				'Can not get configuration property, parsing failure: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueTypeMismatchException $e)
		{
			throw new ConfigurationPropertyValueTypeMismatchException($this, sprintf(
				'Can not get configuration property, value type mismatch: "%s"',
				$property
			));
		}
	}

	/**
	 * Gets a float.
	 *
	 * @throws ConfigurationPropertyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws ConfigurationPropertyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as a float.
	 *
	 * @throws ConfigurationPropertyValueTypeMismatchException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @return float
	 *	The property value.
	 */
	public final function getFloat(string $property, bool $optional = false) : ?float
	{
		try
		{
			return $this->map->getFloat($property, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationPropertyNotSetException($this, sprintf(
				'Can not get configuration property, not set: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueParseException $e)
		{
			throw new ConfigurationPropertyValueParseException($this, sprintf(
				'Can not get configuration property, parsing failure: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueTypeMismatchException $e)
		{
			throw new ConfigurationPropertyValueTypeMismatchException($this, sprintf(
				'Can not get configuration property, value type mismatch: "%s"',
				$property
			));
		}
	}

	/**
	 * Gets an integer.
	 *
	 * @throws ConfigurationPropertyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws ConfigurationPropertyValueParseException
	 *	Thrown when the value can not be retrieved because it set as a string
	 *	and can not parsed as an integer.
	 *
	 * @throws ConfigurationPropertyValueTypeMismatchException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @return int
	 *	The property value.
	 */
	public final function getInt(string $property, bool $optional = false) : ?int
	{
		try
		{
			return $this->map->getInt($property, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationPropertyNotSetException($this, sprintf(
				'Can not get configuration property, not set: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueParseException $e)
		{
			throw new ConfigurationPropertyValueParseException($this, sprintf(
				'Can not get configuration property, parsing failure: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueTypeMismatchException $e)
		{
			throw new ConfigurationPropertyValueTypeMismatchException($this, sprintf(
				'Can not get configuration property, value type mismatch: "%s"',
				$property
			));
		}
	}

	/**
	 * Gets a string.
	 *
	 * @throws ConfigurationPropertyNotSetException
	 *	Thrown when the key is not optional and it's value can not be retrieved
	 *  because it is not set.
	 *
	 * @throws ConfigurationPropertyValueTypeMismatchException
	 *	Thrown when the value can not be retrieved because it is set as an
	 *	incompatible and inconvertible type.
	 *
	 * @param string $property
	 *	The property name.
	 *
	 * @param bool $optional
	 *	The property optional flag.
	 *
	 * @return string
	 *	The property value.
	 */
	public final function getString(string $property, bool $optional = false) : ?string
	{
		try
		{
			return $this->map->getString($property, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationPropertyNotSetException($this, sprintf(
				'Can not get configuration property, not set: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueParseException $e)
		{
			throw new ConfigurationPropertyValueParseException($this, sprintf(
				'Can not get configuration property, parsing failure: "%s"',
				$property
			));
		}
		catch (StringMapKeyValueTypeMismatchException $e)
		{
			throw new ConfigurationPropertyValueTypeMismatchException($this, sprintf(
				'Can not get configuration property, value type mismatch: "%s"',
				$property
			));
		}
	}

	/**
	 * Converts to an array.
	 *
	 * @return array
	 *	The result.
	 */
	public final function toArray() : array
	{
		return $this->map->toArray();
	}
}
