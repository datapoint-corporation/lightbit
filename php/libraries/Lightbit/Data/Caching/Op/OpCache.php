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

namespace Lightbit\Data\Caching\Op;

use \Lightbit;
use \Lightbit\Configuration\ConfigurationProvider;

use \Lightbit\Data\Caching\IFileCache;
use \Lightbit\Data\Caching\IMemoryCache;

/**
 * OpCache.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class OpCache implements IFileCache, IMemoryCache
{
	/**
	 * The key file path map.
	 *
	 * @var array
	 */
	private $keyValueFilePathMap;

	/**
	 * The key value map.
	 *
	 * @var array
	 */
	private $keyValueMap;

	/**
	 * The key value status file path map.
	 *
	 * @var array
	 */
	private $keyValueStatusFilePathMap;

	/**
	 * Construct.
	 */
	public function __construct()
	{
		$this->keyValueFilePathMap = [];
		$this->keyValueMap = [];
		$this->keyValueStatusFilePathMap = [];
		$this->keyValueStatusMap = [];

		ConfigurationProvider::getInstance()->getConfiguration(
			'lightbit.data.caching.file'
		)

		->accept($this, []);
	}

	/**
	 * Checks if a key is set.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return bool
	 *	The key status.
	 */
	public final function contains(string $key) : bool
	{
		if (!isset($this->keyValueMap[$key]))
		{
			$filePath = $this->getKeyValueStatusFilePath($key);

			if (file_exists($filePath))
			{
				$this->keyValueMap[$key] = (require($filePath)) + [
					'status_available' => ((!isset($this->keyValueStatusMap[$key]['expiration'])) || (microtime(true) > $this->keyValueStatusMap[$key]['expiration'])),
					'status_include' => false
				];
			}
			else
			{
				$this->keyValueMap[$key] = [
					'status_available' => false,
					'status_include' => false
				];
			}
		}

		return $this->keyValueMap[$key]['status_available'];
	}

	/**
	 * Gets the key expiry file path.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return string
	 *	The key expiry file path.
	 */
	private final function getKeyValueStatusFilePath(string $key) : string
	{
		return ($this->keyValueStatusFilePathMap[$key] ?? ($this->keyValueStatusFilePathMap[$key] = ($this->getDirectoryPath() . DIRECTORY_SEPARATOR . $key . '.status.php')));
	}

	/**
	 * Gets the key value file path.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return string
	 *	The key value file path.
	 */
	private final function getKeyValueFilePath(string $key) : string
	{
		return ($this->keyValueFilePathMap[$key] ?? ($this->keyExpiryFilePathMap[$key] = ($this->getDirectoryPath() . DIRECTORY_SEPARATOR . $key . '.php')));
	}

	/**
	 * Gets the directory path.
	 *
	 * @return string
	 *	The directory path.
	 */
	public final function getDirectoryPath() : string
	{
		return LB_PATH_APPLICATION_TEMPORARY;
	}

	/**
	 * Reads a value.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value output variable.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function read(string $key, &$value) : bool
	{
		if ($this->contains($key))
		{
			if (!$this->keyValueMap[$key]['status_include'])
			{
				$this->keyValueMap[$key]['value'] = require($this->getKeyValueFilePath($key));
				$this->keyValueMap[$key]['status_include'] = true;
			}

			$value = $this->keyValueMap[$key]['value'];
			return true;
		}

		$value = null;
		return false;
	}

	/**
	 * Writes a value.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 *
	 * @param int $timeToLive
	 *	The value time to live, in milliseconds.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function write(string $key, $value, int $timeToLive = null) : bool
	{
		if (isset($timeToLive))
		{
			$timeToLive = (microtime(true) + ($timeToLive / 1000));
		}

		$this->keyValueMap[$key] = [
			'status_available' => true,
			'status_include' => true,
			'expiration' => $timeToLive,
			'value' => $value
		];

		if (!(($content = var_export($value, true)) === null))
		{
			file_put_contents(
				$this->getKeyValueFilePath($key),
				'<?php return (' . var_export($value, true) . ');'
			);

			file_put_contents(
				$this->getKeyValueStatusFilePath($key),
				'<?php return (' . var_export(([ 'expiration' => $timeToLive ]), true) . ');'
			);

			return true;
		}

		return false;
	}
}
