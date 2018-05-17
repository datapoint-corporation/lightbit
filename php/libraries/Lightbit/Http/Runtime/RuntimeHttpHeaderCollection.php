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

namespace Lightbit\Http\Runtime;

use \Lightbit\Data\Parsing\ParserException;
use \Lightbit\Data\Parsing\ParserProvider;
use \Lightbit\Http\IHttpHeaderCollection;

/**
 * RuntimeHttpHeaderCollection.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class RuntimeHttpHeaderCollection implements IHttpHeaderCollection
{
	/**
	 * The instance.
	 *
	 * @var IHttpHeaderCollection
	 */
	private static $instance;

	/**
	 * Gets the header collection.
	 *
	 * @return IHttpHeaderCollection
	 *	The header collection.
	 */
	public static final function getInstance() : IHttpHeaderCollection
	{
		return (self::$instance ?? (self::$instance = new RuntimeHttpHeaderCollection()));
	}

	/**
	 * The map.
	 *
	 * @var array
	 */
	private $map;

	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Gets the global.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return string
	 *	The global.
	 */
	private function getGlobal(string $header) : ?string
	{
		$variable = 'HTTP_' . strtoupper(strtr($header, [ '-' => '_' ]));

		if (isset($_SERVER[$variable]) && is_string($variable))
		{
			return $_SERVER[$variable];
		}

		return null;
	}

	/**
	 * Checks if a header is set.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function contains(string $header) : bool
	{
		return isset($_SERVER[$this->getGlobal($header)]);
	}

	/**
	 * Gets a header content, as a boolean.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotFoundException
	 *	Thrown if the header content is not set and, when caught by the global
	 *	exception handler on a Web environment, it should generate the proper
	 *	"400 Bad Request" error document.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param bool $optional
	 *	The header optional flag.
	 *
	 * @param bool $default
	 *	The header content default.
	 *
	 * @return bool
	 *	The header content.
	 */
	public final function getBoolean(string $header, bool $optional = false, bool $default = null) : ?bool
	{
		if ($subject = $this->getGlobal($header))
		{
			try
			{
				return ParserProvider::getInstance()->getParser('boolean')->parse($subject);
			}
			catch (ParserException $e)
			{
				throw new HttpHeaderContentFormatException(sprintf('Can not parse header content, wrong format: "%s"', $header), $e);
			}
		}

		if (!$optional)
		{
			throw new HttpHeaderNotFoundException(sprintf('Can not get header content, not found: "%s"', $header));
		}

		return $default;
	}

	/**
	 * Gets a header content, as a float.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotFoundException
	 *	Thrown if the header content is not set and, when caught by the global
	 *	exception handler on a Web environment, it should generate the proper
	 *	"400 Bad Request" error document.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param bool $optional
	 *	The header optional flag.
	 *
	 * @param float $default
	 *	The header content default.
	 *
	 * @return float
	 *	The header content.
	 */
	public final function getFloat(string $header, bool $optional = false, float $default = null) : ?float
	{
		if ($subject = $this->getGlobal($header))
		{
			try
			{
				return ParserProvider::getInstance()->getParser('float')->parse($subject);
			}
			catch (ParserException $e)
			{
				throw new HttpHeaderContentFormatException(sprintf('Can not parse header content, wrong format: "%s"', $header), $e);
			}
		}

		if (!$optional)
		{
			throw new HttpHeaderNotFoundException(sprintf('Can not get header content, not found: "%s"', $header));
		}

		return $default;
	}

	/**
	 * Gets a header content, as a integer.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotFoundException
	 *	Thrown if the header content is not set and, when caught by the global
	 *	exception handler on a Web environment, it should generate the proper
	 *	"400 Bad Request" error document.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param bool $optional
	 *	The header optional flag.
	 *
	 * @param int $default
	 *	The header content default.
	 *
	 * @return int
	 *	The header content.
	 */
	public final function getInteger(string $header, bool $optional = false, int $default = null) : ?int
	{
		if ($subject = $this->getGlobal($header))
		{
			try
			{
				return ParserProvider::getInstance()->getParser('integer')->parse($subject);
			}
			catch (ParserException $e)
			{
				throw new HttpHeaderContentFormatException(sprintf('Can not parse header content, wrong format: "%s"', $header), $e);
			}
		}

		if (!$optional)
		{
			throw new HttpHeaderNotFoundException(sprintf('Can not get header content, not found: "%s"', $header));
		}

		return $default;
	}

	/**
	 * Gets a header content, as a string.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotFoundException
	 *	Thrown if the header content is not set and, when caught by the global
	 *	exception handler on a Web environment, it should generate the proper
	 *	"400 Bad Request" error document.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @param bool $optional
	 *	The header optional flag.
	 *
	 * @param string $default
	 *	The header content default.
	 *
	 * @return string
	 *	The header content.
	 */
	public final function getString(string $header, bool $optional = false, string $default = null) : ?string
	{
		if ($subject = $this->getGlobal($header))
		{
			try
			{
				return ParserProvider::getInstance()->getParser('string')->parse($subject);
			}
			catch (ParserException $e)
			{
				throw new HttpHeaderContentFormatException(sprintf('Can not parse header content, wrong format: "%s"', $header), $e);
			}
		}

		if (!$optional)
		{
			throw new HttpHeaderNotFoundException(sprintf('Can not get header content, not found: "%s"', $header));
		}

		return $default;
	}

	/**
	 * Gets the collection, as a array.
	 *
	 * @return array
	 *	The collection.
	 */
	public final function toArray() : array
	{
		if (!isset($this->map))
		{
			if (function_exists('apache_request_headers') && ($this->map = apache_request_headers()))
			{
				return $this->map;
			}

			$this->map = [];

			foreach ($_SERVER as $property => $value)
			{
				if (strpos($property, 'HTTP_') === 0)
				{
					$this->map[strtr(ucwords(strtolower(strtr(substr($property, 5), [ '_' => ' ' ]))), [ ' ' => '-' ])] = $value;
				}
			}
		}

		return $this->map;
	}
}
