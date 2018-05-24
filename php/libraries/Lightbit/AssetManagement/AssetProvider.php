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

namespace Lightbit\AssetManagement;

/**
 * AssetProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class AssetProvider
{
	/**
	 * The singleton instance.
	 *
	 * @var AssetProvider
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return AssetProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : AssetProvider
	{
		return (self::$instance ?? (self::$instance = new AssetProvider()));
	}

	/**
	 * The asset factory.
	 *
	 * @var IAssetFactory
	 */
	private $assetFactory;

	/**
	 * The asset lists.
	 *
	 * @var array
	 */
	private $assetLists;

	/**
	 * The asset prefix lookup path map.
	 *
	 * @var array
	 */
	private $assetPrefixLookupPathMap;

	/**
	 * The asset path suffix map.
	 *
	 * @var array
	 */
	private $assetPathSuffixMap;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->assetPathSuffixMap = [];
		$this->assetPrefixLookupPathMap = [];
		$this->assets = [];
	}

	/**
	 * Sets an additional asset prefix lookup path.
	 *
	 * @param string $prefix
	 *	The asset prefix.
	 *
	 * @param string $path
	 *	The asset prefix lookup path.
	 */
	public final function addAssetPrefixLookupPath(string $prefix, string $path) : void
	{
		$this->assetPrefixLookupPathMap[$prefix][] = rtrim(strtr($path, [ '/' => DIRECTORY_SEPARATOR ]), DIRECTORY_SEPARATOR);
	}

	/**
	 * Sets an additional asset prefix lookup path map.
	 *
	 * @param array $assetPrefixLookupPathMap
	 *	The asset prefix lookup path map.
	 */
	public final function addAssetPrefixLookupPathMap(array $assetPrefixLookupPathMap) : void
	{
		foreach ($assetPrefixLookupPathMap as $prefix => $path)
		{
			$this->addAssetPrefixLookupPath($prefix, $path);
		}
	}

	/**
	 * Gets an asset.
	 *
	 * @throws AssetNotFoundException
	 *	Thrown if the asset fails to be located due to its file not existing
	 *	within the available lookup paths.
	 *
	 * @throws AssetFactoryException
	 *	Thrown if the asset fails to be created, regardless of the
	 *	actual reason, which should be defined in the exception chain.
	 *
	 * @param string $type
	 *	The asset type.
	 *
	 * @param string $identifier
	 *	The asset identifier.
	 *
	 * @return IAsset
	 *	The asset.
	 */
	public final function getAsset(string $type, string $identifier) : IAsset
	{
		if ($list = $this->getAssetList($type, $identifier))
		{
			return $list[0];
		}

		throw new AssetNotFoundException($this, sprintf('Can not get asset, not found: "%s", of type "%s"', $identifier, $type));
	}

	/**
	 * Gets the asset factory.
	 *
	 * @return IAssetFactory
	 *	The asset factory.
	 */
	public final function getAssetFactory() : IAssetFactory
	{
		return ($this->assetFactory ?? ($this->assetFactory = new AssetFactory()));
	}

	/**
	 * Gets an asset list.
	 *
	 * @throws AssetFactoryException
	 *	Thrown if the asset fails to be created, regardless of the
	 *	actual reason, which should be defined in the exception chain.
	 *
	 * @param string $type
	 *	The asset type.
	 *
	 * @param string $identifier
	 *	The asset identifier.
	 *
	 * @return array
	 *	The asset list.
	 */
	public final function getAssetList(string $type, string $identifier) : array
	{
		if (!isset($this->assetLists[$type]))
		{
			$this->assetLists[$type] = [];
		}

		if (!isset($this->assetLists[$type][$identifier]))
		{
			$match = null;
			$factory = $this->getAssetFactory();

			// Starts empty, by default
			$this->assetLists[$type][$identifier] = [];

			// Split the prefix from the subject, from the asset identifier,
			// in order to assemble the file path suffix.
			list($prefix, $subject) = explode('://', $identifier);

			$filePathSuffix = DIRECTORY_SEPARATOR . strtr($subject, [ '/' => DIRECTORY_SEPARATOR ])
				. ($this->assetPathSuffixMap[$type] ?? ($this->assetPathSuffixMap[$type] = ('.' . strtolower($type))));

			if (isset($this->assetPrefixLookupPathMap[$prefix]))
			{
				// Go through each asset prefix lookup path and attempty to find
				// the first matching asset.
				foreach ($this->assetPrefixLookupPathMap[$prefix] as $i => $basePath)
				{
					$filePath = $basePath . $filePathSuffix;

					if (file_exists($filePath))
					{
						$this->assetLists[$type][$identifier][] = ($match = $factory->createAsset($type, $filePath));
					}
				}
			}
		}

		return $this->assetLists[$type][$identifier];
	}

	/**
	 * Sets the asset factory.
	 *
	 * @param IAssetFactory $assetFactory
	 *	The asset factory.
	 */
	public final function setAssetFactory(IAssetFactory $assetFactory) : void
	{
		$this->assetFactory = $assetFactory;
		$this->assets = [];
	}

	/**
	 * Sets an asset path suffix.
	 *
	 * @param string $type
	 *	The asset type.
	 *
	 * @param string $filePathSuffix
	 *	The asset file path suffix.
	 */
	public final function setAssetPathSuffix(string $type, string $suffix) : void
	{
		$this->assetPathSuffixMap[$type] = $suffix;
	}
}
