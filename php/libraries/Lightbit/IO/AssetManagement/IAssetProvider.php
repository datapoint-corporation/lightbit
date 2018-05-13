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
use \Lightbit\IO\AssetManagement\Directory\IDirectoryAsset;
use \Lightbit\IO\AssetManagement\Php\IPhpAsset;

/**
 * IAssetProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IAssetProvider
{
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
	public function getAsset(string $type, string $asset) : IAsset;

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
	public function getDirectoryAsset(string $asset) : IDirectoryAsset;

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
	public function getPhpAsset(string $asset) : IPhpAsset;
}
