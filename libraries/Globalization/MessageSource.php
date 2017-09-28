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

namespace Lightbit\Globalization;

use \Lightbit;
use \Lightbit\Exception;
use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Globalization\ILocale;
use \Lightbit\Globalization\IMessageSource;

/**
 * MessageSource.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class MessageSource extends Component implements IMessageSource
{
	/**
	 * The directory.
	 *
	 * @type string
	 */
	private $directory;

	/**
	 * The directory path.
	 *
	 * @type string
	 */
	private $directoryPath;

	/**
	 * The message collections.
	 *
	 * @type array
	 */
	private $messageCollections;

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
		parent::__construct($context, $id, null);

		$this->directory = 'messages';
		$this->messageCollections = [];

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Gets the directory.
	 *
	 * @return string
	 *	The directory.
	 */
	public function getDirectory() : string
	{
		return $this->directory;
	}

	/**
	 * Gets the directory path.
	 *
	 * @return string
	 *	The directory path.
	 */
	public function getDirectoryPath() : string
	{
		if (!$this->directoryPath)
		{
			$this->directoryPath = __asset_path_resolve
			(
				$this->getContext()->getPath(),
				null,
				$this->directory
			);
		}

		return $this->directoryPath;
	}

	/**
	 * Gets a message collection.
	 *
	 * @param ILocale $locale
	 *	The locale.
	 *
	 * @param string $category
	 *	The category.
	 *
	 * @return string
	 */
	private function getMessageCollection(ILocale $locale, string $category) : array
	{
		$localeID = $locale->getID();

		if (!isset($this->messageCollections[$localeID]))
		{
			$this->messageCollections[$localeID] = [];
		}

		if (!isset($this->messageCollections[$localeID][$category]))
		{
			$this->messageCollections[$localeID][$category] = [];

			$filePathPrefix = $this->getDirectoryPath() . DIRECTORY_SEPARATOR;
			$filePathSuffix = DIRECTORY_SEPARATOR
				. strtr($category, [ '/' => DIRECTORY_SEPARATOR ])
				. '.php';

			foreach ([ $locale->getLanguageCode(), $locale->getID() ] as $i => $token)
			{
				$filePath = $filePathPrefix . $token . $filePathSuffix;

				if (file_exists($filePath))
				{
					$extension = __include($filePath, [ 'locale' => $locale ]);

					if (!is_array($extension))
					{
						throw new Exception(sprintf('Locale message script must return an array: "%s", at "%s"', $locale->getID(), $filePath));
					}

					$this->messageCollections[$localeID][$category] += $extension;
				}
			}
		}

		return $this->messageCollections[$localeID][$category];
	}

	/**
	 * Reads a message.
	 *
	 * If the message is not available at the source and the component context
	 * has a parent, an attempt will be made to read the message source through
	 * that parent context.
	 *
	 * Ultimately, if a message can not be found, the original message will
	 * be returned instead.
	 *
	 * @param ILocale $locale
	 *	The message locale.
	 *
	 * @param string $category
	 *	The message category.
	 *
	 * @param string $message
	 *	The message to read.
	 *
	 * @return string
	 *	The message.
	 */
	public function read(?ILocale $locale, string $category, string $message) : string
	{
		$locale = $locale ?? $this->getLocale();
		$collection = $this->getMessageCollection($locale, $category);

		if (isset($collection[$message]))
		{
			return $collection[$message];
		}

		$context = $this->getContext()->getContext();

		if ($context)
		{
			return $context->getMessageSource()->read($locale, $category, $message);
		}

		return $message;
	}

	/**
	 * Sets the directory.
	 *
	 * @param string $directory
	 *	The directory.
	 *
	 * @return string
	 *	The directory.
	 */
	public final function setDirectory(string $directory) : void
	{
		$this->directory = $directory;
		$this->directoryPath = null;
	}
}
