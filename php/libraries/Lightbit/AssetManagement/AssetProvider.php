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

use \Throwable;

use \Lightbit\AssetManagement\IAsset;
use \Lightbit\AssetManagement\AssetConstructionException;
use \Lightbit\AssetManagement\AssetNotFoundException;

final class AssetProvider
{
	private static $instance;

	public static final function getInstance() : AssetProvider
	{
		return (self::$instance ?? (self::$instance = new AssetProvider()));
	}

	private $assetFactory;

	private $assetLists;

	private $assetPrefixLookupPathMap;

	private $assetPathSuffixMap;

	private function __construct()
	{
		$this->assetPathSuffixMap = [];
		$this->assetPrefixLookupPathMap = [];
		$this->assets = [];
	}

	public final function addAssetPrefixLookupPath(string $prefix, string $path) : void
	{
		$this->assetPrefixLookupPathMap[$prefix][] = rtrim(strtr($path, [ '/' => DIRECTORY_SEPARATOR ]), DIRECTORY_SEPARATOR);
	}

	public final function addAssetPrefixLookupPathMap(array $assetPrefixLookupPathMap) : void
	{
		foreach ($assetPrefixLookupPathMap as $prefix => $path)
		{
			$this->addAssetPrefixLookupPath($prefix, $path);
		}
	}

	public final function getAsset(string $type, string $identifier) : IAsset
	{
		if ($list = $this->getAssetList($type, $identifier))
		{
			return $list[0];
		}

		throw new AssetNotFoundException($this, sprintf('Can not get asset, not found: "%s", of type "%s"', $identifier, $type));
	}

	public final function getAssetFactory() : IAssetFactory
	{
		return ($this->assetFactory ?? ($this->assetFactory = new AssetFactory()));
	}

	public final function getAssetList(string $type, string $id) : array
	{
		if (!isset($this->assetLists[$type]))
		{
			$this->assetLists[$type] = [];
		}

		if (!isset($this->assetLists[$type][$id]))
		{
			$match = null;
			$factory = $this->getAssetFactory();

			// Starts empty, by default
			$this->assetLists[$type][$id] = [];

			// Split the prefix from the subject, from the asset identifier,
			// in order to assemble the file path suffix.
			list($prefix, $subject) = explode('://', $id);

			$pathSuffix = DIRECTORY_SEPARATOR . strtr($subject, [ '/' => DIRECTORY_SEPARATOR ])
				. ($this->assetPathSuffixMap[$type] ?? ($this->assetPathSuffixMap[$type] = ('.' . strtolower($type))));

			if (isset($this->assetPrefixLookupPathMap[$prefix]))
			{
				// Go through each asset prefix lookup path and attempty to find
				// the first matching asset.
				foreach ($this->assetPrefixLookupPathMap[$prefix] as $i => $path)
				{
					$path .= $pathSuffix;

					if (file_exists($path))
					{
						$this->assetLists[$type][$id][] = ($match = $factory->createAsset($type, $id, $path));
					}
				}
			}
		}

		return $this->assetLists[$type][$id];
	}

	public final function setAssetFactory(IAssetFactory $assetFactory) : void
	{
		$this->assetFactory = $assetFactory;
		$this->assets = [];
	}

	public final function setAssetPathSuffix(string $type, string $suffix) : void
	{
		$this->assetPathSuffixMap[$type] = $suffix;
	}
}
