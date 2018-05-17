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

namespace Lightbit\Http;

/**
 * IHttpHeaderCollection.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHttpHeaderCollection
{
	/**
	 * Checks if a header is set.
	 *
	 * @param string $header
	 *	The header name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function contains(string $header) : bool;

	/**
	 * Gets a header content, as a boolean.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotSetException
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
	public function getBoolean(string $header, bool $optional = false, bool $default = null) : ?bool;

	/**
	 * Gets a header content, as a float.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotSetException
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
	public function getFloat(string $header, bool $optional = false, float $default = null) : ?float;

	/**
	 * Gets a header content, as a integer.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotSetException
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
	public function getInteger(string $header, bool $optional = false, int $default = null) : ?int;

	/**
	 * Gets a header content, as a string.
	 *
	 * @throws HttpHeaderContentFormatException
	 *	Thrown if the header content does not match the expected format and,
	 *	when caught by the global exception handler on a Web environment,
	 * 	it should generate the proper "400 Bad Request" error document.
	 *
	 * @throws HttpHeaderNotSetException
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
	public function getString(string $header, bool $optional = false, string $default = null) : ?string;

	/**
	 * Gets the collection, as a array.
	 *
	 * @return array
	 *	The collection.
	 */
	public function toArray() : array;
}
