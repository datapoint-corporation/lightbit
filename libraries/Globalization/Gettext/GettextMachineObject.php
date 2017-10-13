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

namespace Lightbit\Globalization\Gettext;

use \Lightbit\Base\Object;

/**
 * GettextMachineObject.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class GettextMachineObject extends Object
{
	/**
	 * The endian.
	 *
	 * @type bool
	 */
	private $endian;

	/**
	 * The length.
	 *
	 * @type int
	 */
	private $length;

	/**
	 * The major version number.
	 *
	 * @type int
	 */
	private $major;

	/**
	 * The messages.
	 *
	 * @type array
	 */
	private $messages;

	/**
	 * The path.
	 *
	 * @type string
	 */
	private $path;

	/**
	 * The revision number.
	 *
	 * @type int
	 */
	private $revision;

	/**
	 * Constructor.
	 *
	 * @param string $path
	 *	The path.
	 */
	public function __construct(string $path)
	{
		$this->path = $path;
		$this->endian = true;

		try
		{
			// Open the file
			$fh = fopen($this->path, 'r');

			if ($fh === false)
			{
				throw new GettextException(sprintf('Can not open file for reading: path %s', $this->path));
			}

			// Byte order.
			$this->endian = (dechex($this->word($fh)) === '950412de');

			// Versioning
			$this->major = $this->short($fh);
			$this->revision = $this->short($fh);

			// For now, we can only support the first version (0.0) of the
			// gettext MO file specification.
			if ($this->major > 0 || $this->revision > 0)
			{
				throw new GettextException(sprintf('Machine object version is not compatible: major %d, revision %d', $this->major, $this->revision));
			}

			// Get the total number of messages
			$this->length = $this->word($fh);

			// Get the messages and translation tables offset.
			$messagesTableOffset = $this->word($fh);
			$translationsTableOffset = $this->word($fh);

			// Seek to the messages table offset
			$this->seek($fh, $messagesTableOffset);

			// Each message is represented by two words, one containing
			// its length and the other its offset within the file.
			$messageOffsetsLength = ($this->length * 2);
			$messageOffsets = $this->read($fh, $messageOffsetsLength);
			$messages = [];

			$i = 0;
			while ($i < $messageOffsetsLength)
			{
				$length = $messageOffsets[++$i];
				$offset = $messageOffsets[++$i];

				if ($length > 0)
				{
					$this->seek($fh, $offset);
					$messages[] = fread($fh, $length);
				}
				else
				{
					$messages[] = '';
				}
			}

			// Seek to the translations table offset.
			$this->seek($fh, $translationsTableOffset);

			// Each translation is represented by two words, one containing
			// its length and the other its offset within the file.
			$translationOffsets = $this->read($fh, $messageOffsetsLength);
			$translations = [];

			$i = 0;
			while ($i < $messageOffsetsLength)
			{
				$length = $translationOffsets[++$i];
				$offset = $translationOffsets[++$i];

				if ($length > 0)
				{
					$this->seek($fh, $offset);
					$translations[] = fread($fh, $length);
				}
				else
				{
					$translations[] = '';
				}
			}

			$this->messages = [];
			for ($i = 0; $i < $this->length; ++$i)
			{
				$this->messages[$messages[$i]] = $translations[$i];
			}

			fclose($fh);
		}
		catch(GettextException $e)
		{
			fclose($fh);

			throw new GettextException
			(
				sprintf('Can not import gettext machine object: %s', lcfirst($e->getMessage())), 
				$e
			);
		}
	}

	/**
	 * Gets a message.
	 *
	 * @param string $message
	 *	The original message.
	 *
	 * @return string
	 *	The result.
	 */
	public function getMessage(string $message) : string
	{
		return isset($this->messages[$message])
			? $this->messages[$message]
			: $message;
	}

	/**
	 * Gets the messages.
	 *
	 * @return array
	 *	The messages.
	 */
	public function getMessages() : array
	{
		return $this->messages;
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Reads a word (Int32) from a file handle.
	 *
	 * @param resource $fh
	 *	The file handle.
	 *
	 * @param int $length
	 *	The number of words to read.
	 *
	 * @return array
	 *	The words.
	 */
	private function read($fh, int $length = 1) : array
	{
		$content;

		if (($content = fread($fh, $length * 4)) === false)
		{
			throw new GettextException(sprintf('Can not read file, unexpected unknown error: path %s', $this->path));
		}

		$result = unpack((($this->endian ? 'N' : 'V') . $length), $content);

		if (!isset($result[$length]))
		{
			throw new GettextException(sprintf('Can not read file, unpack error: path %s', $this->path));
		}

		return $result;
	}

	/**
	 * Seeks to a offset through a file handle.
	 *
	 * @param resource $fh
	 *	The file handle.
	 *
	 * @param int $offset
	 *	The offset.
	 */
	private function seek($fh, int $offset) : void
	{
		if (fseek($fh, $offset) < 0)
		{
			throw new GettextException(sprintf('Can not seek to messages table, at offset %s', $this->messageOffset));
		}
	}

	/**
	 * Reads a single short int (Int16) from a file handle.
	 *
	 * @param resource $fh
	 *	The file handle.
	 *
	 * @return int
	 *	The result.
	 */
	private function short($fh) : int
	{
		$content;

		if (($content = fread($fh, 2)) === false)
		{
			throw new GettextException(sprintf('Can not read file, unexpected unknown error: path %s', $this->path));
		}

		$result = unpack(($this->endian ? 'n1' : 'v1'), $content);

		if (!isset($result[1]))
		{
			throw new GettextException(sprintf('Can not read file, unpack error: path %s', $this->path));
		}

		return $result[1];
	}

	/**
	 * Reads a single word (Int32) from a file handle.
	 *
	 * @param resource $fh
	 *	The file handle.
	 *
	 * @return int
	 *	The result.
	 */
	private function word($fh) : int
	{
		return $this->read($fh)[1];
	}

	/**
	 * Checks if it is available.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isAvailable()
	{
		return is_file($this->path);
	}
}