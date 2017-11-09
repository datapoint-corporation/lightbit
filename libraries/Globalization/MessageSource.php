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

use \Lightbit\Exception;
use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Globalization\Locale;

use \Lightbit\Base\IContext;
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
	 * @var string
	 */
	private $directory;

	/**
	 * The directory path.
	 *
	 * @var string
	 */
	private $directoryPath;

	/**
	 * The messages.
	 *
	 * @var array
	 */
	private $messages;

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
	 * @param Locale $locale
	 *	The locale.
	 *
	 * @return array
	 *	The message collection.
	 */
	private function getMessageCollection(ILocale $locale) : array
	{
		$localeID = $locale->getID();

		if (!isset($this->messages[$localeID]))
		{
			$filePathPrefix = $this->getDirectoryPath() . DIRECTORY_SEPARATOR;
			$filePathSuffix = '.php';

			foreach ([ $locale->getLanguageCode(), $locale->getID() ] as $i => $token)
			{
				$filePath = $filePathPrefix . $token . $filePathSuffix;

				if (file_exists($filePath))
				{
					$translation = __include_file_ex($filePath);

					if (!is_array($translation))
					{
						throw new Exception(sprintf('Locale message translation table script must return an array: %s, at %s', $locale->getID(), $filePath));
					}

					return $this->messages[$localeID] = $translation;
				}
			}

			$this->messages[$localeID] = [];
		}

		return $this->messages[$localeID];
	}

	/**
	 * Reads a message.
	 *
	 * @param ILocale $locale
	 *	The message locale.
	 *
	 * @param string $message
	 *	The message to read.
	 *
	 * @return string
	 *	The message.
	 */
	public function read(?ILocale $locale, string $message) : string
	{
		$locale = $locale ?? $this->getLocale();
		$collection = $this->getMessageCollection($locale);

		if (isset($collection[$message]))
		{
			return $collection[$message];
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

	/**
	 * On Construct.
	 *
	 * This method is invoked during the component construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		parent::onConstruct();

		$this->directory = 'messages';
		$this->messages = [];
	}
}
