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

use \Lightbit\Base\IComponent;

/**
 * IHttpAssetManager.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpAssetManager extends IComponent
{
	/**
	 * Gets the publish directory.
	 *
	 * @return string
	 *	The publish directory.
	 */
	public function getPublishDirectory() : string;

	/**
	 * Gets the publish directory path.
	 *
	 * @return string
	 *	The publish directory path.
	 */
	public function getPublishDirectoryPath() : string;

	/**
	 * Gets the publish directory url.
	 *
	 * @return string
	 *	The publish directory url.
	 */
	public function getPublishDirectoryUrl() : string;

	/**
	 * Gets an asset publish path.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return string
	 *	The asset published path.
	 */
	public function getPublishPath(string $asset) : string;

	/**
	 * Gets an asset publish url.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return string
	 *	The asset published url.
	 */
	public function getPublishUrl(string $asset) : string;

	/**
	 * Gets the refresh flag.
	 *
	 * @return bool
	 *	The refresh flag.
	 */
	public function getRefresh() : bool;

	/**
	 * Checks an asset publish status.
	 *
	 * @param string $resource
	 *	The asset file system alias.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isPublished(string $asset) : bool;

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
	public function publish(string $asset, bool $refresh = false) : string;

	/**
	 * Sets the publish directory.
	 *
	 * @param string $publishDirectory
	 *	The publish directory.
	 */
	public function setPublishDirectory(string $publishDirectory) : void;

	/**
	 * Sets the publish url.
	 *
	 * @param string $publishUrl
	 *	The publish url.
	 */
	public function setPublishUrl(string $publishUrl) : void;

	/**
	 * Sets the refresh flag.
	 *
	 * @param array $refresh
	 *	The refresh flag.
	 */
	public function setRefresh(bool $refresh) : void;
}