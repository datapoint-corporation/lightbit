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

namespace Lightbit\IO\AssetManagement;

use \Lightbit\IO\AssetManagement\IAsset;
use \Lightbit\IO\AssetManagement\IAssetProvider;
use \Lightbit\IO\AssetManagement\Directory\IDirectoryAsset;
use \Lightbit\IO\AssetManagement\Php\IPhpAsset;

/**
 * AssetProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class AssetProvider implements IAssetProvider
{
	/**
	 * The instance.
	 *
	 * @var AssetProvider
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return AssetProvider
	 *	The asset provider.
	 */
	public static final function getInstance() : AssetProvider
	{
		if (!self::$instance)
		{
			self::$instance = new AssetProvider();

			// Set the standard application prefixes.
			self::$instance->setAssetPrefixPath('application', LB_PATH_APPLICATION);

			foreach ([ 'hooks', 'layouts', 'messages', 'settings', 'views' ] as $i => $token)
			{
				self::$instance->setAssetPrefixPath($token, LB_PATH_APPLICATION . DIRECTORY_SEPARATOR . $token);
			}

			// Set the lightbit prefixes.
			self::$instance->setAssetPrefixPath('lightbit', LB_PATH_LIGHTBIT);
		}

		return (self::$instance ?? (self::$instance = new AssetProvider()));
	}

	/**
	 * The asset factory.
	 *
	 * @var IAssetFactory
	 */
	private $assetFactory;

	/**
	 * The prefixes path.
	 *
	 * @var array
	 */
	private $prefixesPath;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->assetFactory = new AssetFactory();
		$this->assetPrefixesPath = [];
	}

	/**
	 * Sets an additional asset path prefix path.
	 *
	 * @param string $prefix
	 *	The asset prefix name.
	 *
	 * @param string $path
	 *	The asset prefix path.
	 */
	public final function addAssetPrefixPath(string $prefix, string $path) : void
	{
		if (isset($this->assetPrefixesPath[$prefix]))
		{
			array_unshift($this->assetPrefixesPath[$prefix], $path);
		}
		else
		{
			$this->assetPrefixesPath[$prefix] = [ $path ];
		}
	}

	/**
	 * Gets an asset.
	 *
	 * @throws AssetNotFoundAssetProviderException
	 *	Thrown on failure to locate an asset matching the given identifier,
	 *	which needs the applicable access and read file system permission.
	 *
	 * @param string $type
	 *	The asset type.
	 *
	 * @param string $asset
	 *	The asset identifier.
	 *
	 * @return IAsset
	 *	The asset.
	 */
	public function getAsset(string $type, string $asset) : IAsset
	{
		list($prefix, $uri) = explode(':/', $asset);

		$filePathSuffix =  strtr($uri, [ '/' => DIRECTORY_SEPARATOR ]);

		switch ($type)
		{
			case 'directory':
				break;

			default:
				$filePathSuffix .= '.' . strtolower($type);
				break;
		}

		foreach ($this->getAssetPrefixPaths($prefix) as $i => $basePath)
		{
			$filePath = $basePath . $filePathSuffix;

			if (file_exists($filePath))
			{
				return $this->getAssetFactory()->createAsset($type, $asset, $filePath);
			}
		}

		throw new AssetNotFoundAssetProviderException($this, $asset);
	}

	/**
	 * Gets a directory asset.
	 *
	 * @throws AssetNotFoundAssetProviderException
	 *	Thrown on failure to locate an asset matching the given identifier,
	 *	which needs the applicable access and read file system permission.
	 *
	 * @param string $asset
	 *	The asset identifier.
	 *
	 * @return IDirectoryAsset
	 *	The directory.
	 */
	public final function getDirectoryAsset(string $asset) : IDirectoryAsset
	{
		return $this->getAsset('directory', $asset);
	}

	/**
	 * Gets a script asset.
	 *
	 * @throws AssetNotFoundAssetProviderException
	 *	Thrown on failure to locate an asset matching the given identifier,
	 *	which needs the applicable access and read file system permission.
	 *
	 * @param string $asset
	 *	The asset identifier.
	 *
	 * @return IPhpAsset
	 *	The asset.
	 */
	public final function getPhpAsset(string $asset) : IPhpAsset
	{
		return $this->getAsset('php', $asset);
	}

	/**
	 * Gets the asset factory.
	 *
	 * @return IAssetFactory
	 *	The asset factory.
	 */
	public final function getAssetFactory() : IAssetFactory
	{
		return $this->assetFactory;
	}

	/**
	 * Gets an asset prefix path.
	 *
	 * @param string $prefix
	 *	The asset prefix.
	 *
	 * @return array
	 *	The asset prefix paths.
	 */
	public final function getAssetPrefixPaths(string $prefix) : array
	{
		if (isset($this->assetPrefixesPath[$prefix]))
		{
			return $this->assetPrefixesPath[$prefix];
		}

		return [];
	}

	/**
	 * Sets the asset factory.
	 *
	 * @throws CommandOutOfSyncException
	 *	Thrown if the asset factory has already been set.
	 *
	 * @param IAssetFactory $assetFactory
	 *	The asset factory.
	 */
	public final function setAssetFactory(IAssetFactory $assetFactory) : void
	{
		$this->assetFactory = $assetFactory;
	}

	/**
	 * Sets a prefix path.
	 *
	 * @param string $prefix
	 *	The prefix.
	 *
	 * @param string $path
	 *	The prefix path.
	 */
	public final function setAssetPrefixPath(string $prefix, string $path) : void
	{
		$this->assetPrefixesPath[$prefix] = [ rtrim(strtr($path, [ '/' => DIRECTORY_SEPARATOR ]), DIRECTORY_SEPARATOR) ];
	}
}
