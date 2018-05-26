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

class Configuration implements IConfiguration
{
	private $configuration;

	public function __construct(array $configuration = null)
	{
		$this->configuration = new StringMap($configuration);
	}

	public function accept(object $subject, array $configuration) : void
	{
		$values = $this->configuration->toArray();

		foreach ($configuration as $property => $method)
		{
			try
			{
				if (isset($values[$property]))
				{
					$subject->{$method}($values[$property]);
				}
			}
			catch (Throwable $e)
			{
				throw new ConfigurationException($this, sprintf('Can not set configurable property, uncaught throwable: "%s"', $property), $e);
			}
		}
	}

	public function getBool(string $key, bool $optional = false) : ?bool
	{
		try
		{
			return $this->configuration->getBool($key, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationKeyNotSetException($this, sprintf('Can not get configuration property, it is not set: "%s"', $key), $e);
		}
		catch (StringMapKeyTypeException $e)
		{
			throw new ConfigurationKeyTypeException($this, sprintf('Can not get configuration property, type mismatch: "%s"', $key), $e);
		}
	}

	public function getFloat(string $key, bool $optional = false) : ?float
	{
		try
		{
			return $this->configuration->getFloat($key, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationKeyNotSetException($this, sprintf('Can not get configuration property, it is not set: "%s"', $key), $e);
		}
		catch (StringMapKeyTypeException $e)
		{
			throw new ConfigurationKeyTypeException($this, sprintf('Can not get configuration property, type mismatch: "%s"', $key), $e);
		}
	}

	public function getInt(string $key, bool $optional = false) : ?int
	{
		try
		{
			return $this->configuration->getInt($key, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationKeyNotSetException($this, sprintf('Can not get configuration property, it is not set: "%s"', $key), $e);
		}
		catch (StringMapKeyTypeException $e)
		{
			throw new ConfigurationKeyTypeException($this, sprintf('Can not get configuration property, type mismatch: "%s"', $key), $e);
		}
	}

	public function getString(string $key, bool $optional = false) : ?string
	{
		try
		{
			return $this->configuration->getString($key, $optional);
		}
		catch (StringMapKeyNotSetException $e)
		{
			throw new ConfigurationKeyNotSetException($this, sprintf('Can not get configuration property, it is not set: "%s"', $key), $e);
		}
		catch (StringMapKeyTypeException $e)
		{
			throw new ConfigurationKeyTypeException($this, sprintf('Can not get configuration property, type mismatch: "%s"', $key), $e);
		}
	}

	public final function toArray() : array
	{
		return $this->configuration->toArray();
	}
}
