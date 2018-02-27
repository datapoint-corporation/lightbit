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

namespace Lightbit\I18n\Gettext;

use \Lightbit\Base\Component;
use \Lightbit\Base\IContext;
use \Lightbit\Exception;
use \Lightbit\I18n\Gettext\GettextMachineObject;
use \Lightbit\I18n\ILocale;
use \Lightbit\I18n\IMessageSource;
use \Lightbit\IO\FileSystem\Asset;

/**
 * GettextMessageSource.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class GettextMessageSource extends Component implements IMessageSource
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
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id, null);

		$this->directory = 'messages';
		$this->messages = [];

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
			$this->directoryPath = (new Asset($this->getContext()->getPath(), $this->directory, null))->getPath();
		}

		return $this->directoryPath;
	}

	/**
	 * Reads a message pattern translation.
	 *
	 * If a message pattern translation is not found within this source, the
	 * original pattern is to be returned instead.
	 *
	 * @param ILocale $locale
	 *	The message locale.
	 *
	 * @param string $message
	 *	The message pattern.
	 *
	 * @return string
	 *	The result.
	 */
	public function read(ILocale $locale, string $message) : string
	{
		$localeID = $locale->getID();

		if (!isset($this->messages[$localeID]))
		{
			// Gettext parsing is a bit taxing if done every request, so we
			// simply keep the entire message collection cached in-memory,
			// just like the real thing, in order to speed things up.
			$memory = $this->getContext()->getMemoryCache();
			$guid = 'lightbit.i18n.gettext://' . $localeID;

			if (!$memory->read($guid, $this->messages[$localeID]))
			{
				$this->messages[$localeID] = [];

				$filePathPrefix = $this->getDirectoryPath() . DIRECTORY_SEPARATOR;
				$filePathSuffix = '.mo';

				foreach ([ $locale->getID(), $locale->getLanguageCode() ] as $i => $token)
				{
					$filePath = $filePathPrefix . $token . $filePathSuffix;

					if (is_file($filePath))
					{
						$this->messages[$localeID] 
							+= (new GettextMachineObject($filePath))
								->getMessages();

						$memory->write($guid, $this->messages[$localeID]);
						break;
					}
				}
			}
		}

		// Even if we get the messages from the MO file, there's still a
		// big chance the message is not defined in it.
		if (isset($this->messages[$localeID][$message]))
		{
			return $this->messages[$localeID][$message];
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
