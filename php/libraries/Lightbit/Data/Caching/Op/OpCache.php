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
	 * The key data map.
	 *
	 * @var string
	 */
	private $keyDataMap;

	/**
	 * The key file path map.
	 *
	 * @var string
	 */
	private $keyFilePathMap;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->keyDataMap = [];
		$this->keyFilePathMap = [];
	}

	/**
	 * Compiles a value for storage.
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
	 * @return string
	 *	The result.
	 */
	private function compile(string $key, $value, int $timeToLive = null) : string
	{
		return (
			'<?php /* Lightbit/2.0.0 */ return ' .
			var_export(
				[
					'expire_on' => ($timeToLive ? (microtime(true) + ($timeToLive / 1000)) : null),
					'key' => $key,
					'microtime' => microtime(true),
					'value' => $this->transform($value, $transformation),
					'unserialize' => $transformation
				],
				true
			) .
			';'
		);
	}

	/**
	 * Gets the key file path.
	 *
	 * @param string $key
	 *	The key.
	 *
	 * @return string
	 *	The key file path.
	 */
	private function getKeyFilePath(string $key) : string
	{
		return ($this->keyFilePathMap[$key] ?? (
			$this->keyFilePathMap[$key] = (
				LB_PATH_APPLICATION_TEMPORARY .
				DIRECTORY_SEPARATOR .
				md5('Lightbit/2.0.0 ' . $key) .
				'.opcache.php'
			)
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
	public function read(string $key, &$value) : bool
	{
		if (isset($this->keyDataMap[$key]))
		{
			$value = $this->keyDataMap['value'];
			return true;
		}

		$filePath = $this->getKeyFilePath($key);

		if (file_exists($filePath))
		{
			$this->keyDataMap[$key] = Lightbit::getInstance()->include($filePath);

			if ($this->keyDataMap[$key]['expire_on'] && ($this->keyDataMap[$key]['expire_on'] > microtime(true)))
			{
				unset($this->keyDataMap[$key]);
				unlink($filePath);

				$value = null;
				return false;
			}

			if ($this->keyDataMap[$key]['unserialize'])
			{
				$this->keyDataMap[$key]['value'] = unserialize(
					base64_decode($this->keyDataMap[$key]['value'])
				);
			}

			$value = $this->keyDataMap[$key]['value'];
			return true;
		}

		$value = null;
		return false;
	}

	/**
	 * Validates a value recursively to check if it's a scalar, or array of
	 * scalar values and, if not, serializes and b64 encodes it allowing us
	 * to store objects.
	 *
	 * @param mixed $value
	 *	The value to transform.
	 *
	 * @param bool $transformation
	 *	The value transformation flag.
	 *
	 * @return mixed
	 *	The result.
	 */
	private function transform($value, bool &$transformation = null)
	{
		if ($this->validate($value))
		{
			$transformation = false;
			return $value;
		}

		$transformation = true;
		return (base64_encode(serialize($value)));
	}

	/**
	 * Validates a value recursively to check if it's a scalar, or array of
	 * scalar values.
	 *
	 * @param mixed $value
	 *	The value to transform.
	 *
	 * @return bool
	 *	The result.
	 */
	private function validate($value) : bool
	{
		if (is_scalar($value))
		{
			return true;
		}

		if (is_array($value))
		{
			foreach ($value as $i => $v)
			{
				if (!$this->validate($v))
				{
					return false;
				}
			}

			return true;
		}

		return false;
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
	public function write(string $key, $value, int $timeToLive = null) : bool
	{
		return !!file_put_contents(
			$this->getKeyFilePath($key),
			$this->compile($key, $value, $timeToLive)
		);
	}
}
