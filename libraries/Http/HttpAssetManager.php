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

namespace Lightbit\Http;

use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Http\IHttpAssetManager;
use \Lightbit\IO\FileSystem\FileNotFoundException;
use \Lightbit\IO\IOException;

/**
 * HttpAssetManager.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HttpAssetManager extends Component implements IHttpAssetManager
{
	/**
	 * The publish directory.
	 *
	 * @type string
	 */
	private $publishDirectory;

	/**
	 * The publish directory path.
	 *
	 * @type string
	 */
	private $publishDirectoryPath;

	/**
	 * The publish directory url.
	 *
	 * @type string
	 */
	private $publishDirectoryUrl;

	/**
	 * The publish url.
	 *
	 * @type string
	 */
	private $publishUrl;

	/**
	 * The refresh flag.
	 *
	 * @type bool
	 */
	private $refresh;

	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(Context $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id);

		$this->publishDirectory = 'public://assets';
		$this->refresh = __environment_debug_get();

		if ($configuration)
		{
			__object_apply($this, $configuration);
		}
	}

	/**
	 * Copies a directory recursively.
	 *
	 * @param string $asset
	 *	The asset being published.
	 *
	 * @param string $source
	 *	The source directory path.
	 *
	 * @param string $destination
	 *	The destination directory path.
	 */
	private function copyDirectory(string $asset, string $source, string $destination) : void
	{
		if (!file_exists($destination) && !mkdir($destination, 0775, true))
		{
			throw new IOException
			(
				sprintf
				(
					'Can not publish asset, publish path creation failure: "%s", publish path "%s", from context "%s"',
					$asset,
					$destination,
					$this->getContext()->getPrefix()
				)
			);
		}

		foreach (scandir($source) as $i => $fileName)
		{
			if ($fileName == '.' || $fileName == '..')
			{
				continue;
			}

			$sourcePath = $source . DIRECTORY_SEPARATOR . $fileName;
			$destinationPath = $destination . DIRECTORY_SEPARATOR . $fileName;

			if (is_dir($sourcePath))
			{
				$this->copyDirectory($asset, $sourcePath, $destinationPath);
				continue;
			}

			if (!copy($sourcePath, $destinationPath))
			{
				throw new IOException
				(
					sprintf
					(
						'Can not publish asset, file copy failure: "%s", source "%s", destination "%s", from context "%s"',
						$asset,
						$privatePath,
						$publishPath,
						$this->getContext()->getPrefix()
					)
				);
			}
		}
	}

	/**
	 * Resolves an asset file system alias.
	 *
	 * @param string $asset
	 *	The asset file system alias.
	 *
	 * @return array
	 *	The result.
	 */
	private function resolve(string $asset) : array
	{
		static $results = [];

		if (!isset($results[$asset]))
		{
			$privatePath = __asset_path_resolve
			(
				$this->getContext()->getPath(),
				null,
				$asset
			);

			$privatePathBaseName = basename($privatePath);

			$privatePathSuffix = ($i = strpos($privatePathBaseName, '.'))
				? substr($privatePathBaseName, $i)
				: '';

			$hash = sprintf('%x', crc32(__lightbit_version() . '//' . $privatePath));

			$publishPath = $this->getPublishDirectoryPath()
				. DIRECTORY_SEPARATOR
				. $hash
				. $privatePathSuffix;

			$publishUrlSuffix = ((file_exists($privatePath) && is_dir($privatePath)) ? '/' : '');

			$publishUrl = $this->getPublishDirectoryUrl()
				. $hash
				. $privatePathSuffix
				. $publishUrlSuffix;

			return $results[$asset] =
			[
				'hash' => $hash,
				'private-path' => $privatePath,
				'publish-path' => $publishPath,
				'publish-url' => $publishUrl
			];
		}

		return $results[$asset];
	}

	/**
	 * Gets the publish directory.
	 *
	 * @return string
	 *	The publish directory.
	 */
	public final function getPublishDirectory() : string
	{
		return $this->publishDirectory;
	}

	/**
	 * Gets the publish directory path.
	 *
	 * @return string
	 *	The publish directory path.
	 */
	public final function getPublishDirectoryPath() : string
	{
		if (!$this->publishDirectoryPath)
		{
			$this->publishDirectoryPath = __asset_path_resolve
			(
				$this->getContext()->getPath(),
				null,
				$this->publishDirectory
			);
		}

		return $this->publishDirectoryPath;
	}

	/**
	 * Gets the publish directory url.
	 *
	 * @return string
	 *	The publish directory url.
	 */
	public final function getPublishDirectoryUrl() : string
	{
		if (!$this->publishDirectoryUrl)
		{
			$this->publishDirectoryUrl = $this->getHttpRouter()->getBaseUrl() . 'assets/';
		}

		return $this->publishDirectoryUrl;
	}

	/**
	 * Gets an asset publish path.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return string
	 *	The asset published path.
	 */
	public final function getPublishPath(string $asset) : string
	{
		return $this->resolve($asset)['publish-path'];
	}

	/**
	 * Gets an asset publish url.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return string
	 *	The asset published url.
	 */
	public final function getPublishUrl(string $asset) : string
	{
		return $this->resolve($asset)['publish-url'];
	}

	/**
	 * Gets the refresh flag.
	 *
	 * @return bool
	 *	The refresh flag.
	 */
	public final function getRefresh() : bool
	{
		return $this->refresh;
	}

	/**
	 * Checks an asset publish status.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isPublished(string $asset) : bool
	{
		return file_exists($this->resolve($asset)['publish-path']);
	}

	/**
	 * Publishes an asset.
	 *
	 * @param string $asset
	 *	The asset file system alias.
	 *
	 * @param bool $refresh
	 *	The refresh flag.
	 *
	 * @return string
	 *	The asset published url.
	 */
	public final function publish(string $asset, bool $refresh = false) : string
	{
		$resolution = $this->resolve($asset);
		$publishPath = $resolution['publish-path'];

		if ($refresh || $this->refresh || !file_exists($publishPath))
		{
			$privatePath = $resolution['private-path'];

			if (!file_exists($privatePath))
			{
				throw new FileNotFoundException
				(
					$privatePath,
					sprintf
					(
						'Can not publish asset, file not found: "%s", expected at file "%s", at context "%s"',
						$asset,
						$privatePath,
						$this->getContext()->getPrefix()
					)
				);
			}

			if (is_file($privatePath))
			{
				if (!file_exists($publishDirectoryPath) && !mkdir($publishDirectoryPath, 0775, true))
				{
					throw new IOException
					(
						sprintf
						(
							'Can not publish asset, publish path creation failure: "%s", at file "%s", at context "%s"',
							$asset,
							$publishDirectoryPath,
							$this->getContext()->getPrefix()
						)
					);
				}

				if (!copy($privatePath, $publishPath))
				{
					throw new IOException
					(
						sprintf
						(
							'Can not publish asset, file copy failure: "%s", source "%s", destination "%s", from context "%s"',
							$asset,
							$privatePath,
							$publishPath,
							$this->getContext()->getPrefix()
						)
					);
				}
			}

			else
			{
				$this->copyDirectory($asset, $privatePath, $publishPath);
			}
		}

		return $resolution['publish-url'];
	}

	/**
	 * Sets the publish directory.
	 *
	 * @param string $publishDirectory
	 *	The publish directory.
	 */
	public final function setPublishDirectory(string $publishDirectory) : void
	{
		$this->publishDirectory = $publishDirectory;
		$this->publishDirectoryPath = null;
	}

	/**
	 * Sets the publish url.
	 *
	 * @param string $publishUrl
	 *	The publish url.
	 */
	public final function setPublishUrl(string $publishUrl) : void
	{
		$this->publishUrl = $publishUrl;
	}

	/**
	 * Sets the refresh flag.
	 *
	 * @param array $refresh
	 *	The refresh flag.
	 */
	public final function setRefresh(bool $refresh) : void
	{
		$this->refresh = $refresh;
	}
}
