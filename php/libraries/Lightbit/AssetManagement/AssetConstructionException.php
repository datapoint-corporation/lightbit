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

use \Lightbit\Exception;
use \Lightbit\AssetManagement\IAssetFactory;
use \Lightbit\AssetManagement\AssetFactoryException;

/**
 * AssetConstructionException.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class AssetConstructionException extends AssetFactoryException
{
	/**
	 * The asset identifier.
	 *
	 * @var string
	 */
	private $assetID;

	/**
	 * The asset path.
	 *
	 * @var string
	 */
	private $assetPath;

	/**
	 * The asset type.
	 *
	 * @var string
	 */
	private $assetType;

	/**
	 * Constructor.
	 *
	 * @param IAssetFactory $assetFactory
	 *	The exception asset factory.
	 *
	 * @param string $assetID
	 *	The exception asset identifier.
	 *
	 * @param string $message
	 *	The exception message.
	 *
	 * @param Throwable $previous
	 *	The exception previous throwable.
	 */
	public function __construct(IAssetFactory $assetFactory, string $assetType, string $assetID, string $assetPath, string $message, Throwable $previous = null)
	{
		parent::__construct($assetFactory, $message, $previous);

		$this->assetID = $assetID;
		$this->assetPath = $assetPath;
		$this->assetType = $assetType;
	}

	/**
	 * Gets the asset identifier.
	 *
	 * @return string
	 *	The asset identifier.
	 */
	public final function getAssetID() : string
	{
		return $this->assetID;
	}

	/**
	 * Gets the asset path.
	 *
	 * @return string
	 *	The asset path.
	 */
	public final function getAssetPath() : string
	{
		return $this->assetPath;
	}

	/**
	 * Gets the asset type.
	 *
	 * @return string
	 *	The asset type.
	 */
	public final function getAssetType() : string
	{
		return $this->assetType;
	}
}
