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

use \Lightbit\Configuration\ConfigurationException;
use \Lightbit\Data\Caching\Cache;

use \Lightbit\Data\Caching\IOpCache;

/**
 * OpCache.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class OpCache extends Cache implements IOpCache
{
	/**
	 * The key hash map.
	 *
	 * @var array
	 */
	private $keyHashMap;

	/**
	 * The key meta map.
	 *
	 * @var array
	 */
	private $keyMetaMap;

	/**
	 * The key value map.
	 *
	 * @var array
	 */
	private $keyValueMap;

	/**
	 * The output directory path.
	 *
	 * @var string
	 */
	private $outputDirectoryPath;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->keyHashMap = [];
		$this->keyMetaMap = [];
		$this->keyValueMap = [];
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
		return $this->getKeyMeta($key)['key_value_available'];
	}

	/**
	 * Gets the output directory path.
	 *
	 * @return string
	 *	The output directory path.
	 */
	public final function getOutputDirectoryPath() : string
	{
		return ($this->outputDirectoryPath ?? ($this->outputDirectoryPath = LB_PATH_APPLICATION_TEMPORARY));
	}

	/**
	 * Gets a key hash.
	 *
	 * @return string
	 *	The key hash.
	 */
	private function getKeyHash(string $key) : string
	{
		return ($this->keyHashMap[$key] ?? ($this->keyHashMap[$key] = md5($key)));
	}

	/**
	 * Gets the key meta.
	 *
	 * @return array
	 *	The key meta.
	 */
	private function getKeyMeta(string $key) : ?array
	{
		if (!isset($this->keyMetaMap[$key]))
		{
			$filePath = $this->getKeyMetaFilePath($key);

			if (file_exists($filePath))
			{
				$this->keyMetaMap[$key] = (require ($filePath));
				$this->keyMetaMap[$key] += [
					'key_value_available' => ((!isset($this->keyMetaMap[$key]['key_expire_on'])) || ($this->keyMetaMap[$key]['key_expire_on'] > (microtime(true)))),
					'key_value_fetch' => false
				];
			}
			else
			{
				$this->keyMetaMap[$key] = [
					'key' => $key,
					'key_expire_on' => null,
					'key_value_available' => false,
					'key_value_fetch' => false
				];
			}
		}

		return $this->keyMetaMap[$key];
	}

	/**
	 * Gets the key meta file path.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return string
	 *	The key meta file path.
	 */
	private function getKeyMetaFilePath(string $key) : string
	{
		return ($this->keyMetaFilePathMap[$key] ?? (
			$this->keyMetaFilePathMap[$key] = $this->getOutputDirectoryPath()
				. DIRECTORY_SEPARATOR
				. $this->getKeyHash($key)
				. '.meta.php'
		));
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
	private function getKeyValueFilePath(string $key) : string
	{
		return ($this->keyValueFilePathMap[$key] ?? (
			$this->keyValueFilePathMap[$key] = $this->getOutputDirectoryPath()
				. DIRECTORY_SEPARATOR
				. $this->getKeyHash($key)
				. '.php'
		));
	}

	/**
	 * Reads a value.
	 *
	 * @throws CacheReadException
	 *	Thrown if the key value is set but fails to be read because it can not
	 *	be unserialized from persistent storage.
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
			if (!$this->keyMetaMap[$key]['key_value_fetch'])
			{
				$this->keyValueMap[$key] = (require ($this->getKeyValueFilePath($key)));
				$this->keyMetaMap[$key]['key_value_fetch'] = true;
			}

			$value = $this->keyValueMap[$key];
			return true;
		}

		$value = null;
		return false;
	}

	/**
	 * Compiles a value for storage.
	 *
	 * @param mixed $subject
	 *	The compilation subject.
	 *
	 * @return string
	 *	The compilation result.
	 */
	private function compile($subject) : string
	{
		$php = '<?php return (';

		if ($this->validate($subject))
		{
			$php .= var_export($subject, true);
		}
		else
		{
			$php .= 'unserialize(base64_decode(\'' . base64_encode(serialize($subject)) . '\'))';
		}

		return ($php . '); // Lightbit/2.0.0');
	}

	/**
	 * Validates a subject.
	 *
	 * @param mixed $subject
	 *	The validation subject.
	 *
	 * @return bool
	 *	The validation result.
	 */
	private function validate($subject) : bool
	{
		if (isset($subject) && !is_scalar($subject))
		{
			if (!is_array($subject))
			{
				return false;
			}

			foreach ($subject as $k => $v)
			{
				if (!$this->validate($v))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Writes a value.
	 *
	 * @throws CacheWriteException
	 *	Thrown if the key value write fails because it can not be serialized
	 *	to persistent storage.
	 *
	 * @param string $key
	 *	The value key.
	 *
	 * @param mixed $value
	 *	The value.
	 *
	 * @param int $timeToLive
	 *	The value time to live.
	 *
	 * @return bool
	 *	The success status.
	 */
	public final function write(string $key, $value, int $timeToLive = null) : bool
	{
		if (isset($timeToLive))
		{
			$timeToLive += intval(microtime(true) * 1000);
		}

		// Create the key meta and write it to disk along with the
		// value, after compilation.
		$keyMeta = [
			'key' => $key,
			'key_expire_on' => $timeToLive,
		];

		$success = (
			file_put_contents(
				$this->getKeyMetaFilePath($key),
				$this->compile($keyMeta),
				LOCK_EX
			)

			&&

			file_put_contents(
				$this->getKeyValueFilePath($key),
				$this->compile($value),
				LOCK_EX
			)
		);

		$this->keyMetaMap[$key] = $keyMeta + [
			'key_value_available' => true,
			'key_value_fetch' => true,
			'key_value' => $value
		];

		return $success;
	}
}
