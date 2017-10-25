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

namespace Lightbit\Data\Sql;

use \Lightbit\Data\Sql\ISqlReader;
use \Lightbit\Data\Sql\ISqlStatement;

/**
 * ISqlReader.
 *
 * A SQL reader is a basic stream reader that fetches one result at a time and,
 * as such, should be closed and safely disposed after use. If it's left open,
 * depending on the underlying database management system, you may not be able
 * to prepare and/or run the next statement and memory leaks may have a 
 * negative impact on the application performance.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ISqlReader
{
	/**
	 * Fetches the remaining results in the current result set and safely
	 * closes the reader.
	 *
	 * @param bool $numeric
	 *	The numeric fetch flag which, when set, causes all results to be
	 *	fetched as numeric arrays.
	 *
	 * @return array
	 *	The results.
	 */
	public function all(bool $numeric = false) : array;

	/**
	 * Safely closes the reader.
	 *
	 * After closing, some information will persist regarding the parent
	 * transaction and any encountered errors. It has a neglible impact on
	 * memory consumption but, if you want to completely dispose of the reader,
	 * just unset it (unset($reader)).
	 */
	public function close() : void;

	/**
	 * Gets the sql connection.
	 *
	 * @return ISqlConnection
	 *	The sql connection.
	 */
	public function getSqlConnection() : ISqlConnection;

	/**
	 * Gets the sql statement.
	 *
	 * @return ISqlStatement
	 *	The sql statement.
	 */
	public function getSqlStatement() : ISqlStatement;

	/**
	 * Checks if the reader is closed.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isClosed() : bool;

	/**
	 * Fetches the next result in the current result set.
	 *
	 * Be aware that even if there is no more rows in the current result set,
	 * in which case null is returned instead, that does not mean the reader
	 * is, will be or has been closed!
	 *
	 * @return array
	 *	The next result.
	 */
	public function next(bool $numeric = false) : ?array;

	/**
	 * Fetches the next result in the current result set, returning the
	 * first cell after closing the reader.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * @return string
	 *	The result.
	 */
	public function scalar() : ?string;

	/**
	 * Fetches the next result in the current result set and returns it 
	 * after closing the reader.
	 *
	 * Be aware that once a reader is closed, it can not be reset and ends
	 * up being useless. Further calls to functions expecting it to be open
	 * may result in unexpected behaviour.
	 *
	 * In this case, if there are no more rows within the current result set,
	 * the reader is still implicitly closed.
	 *
	 * @return array
	 *	The result.
	 */
	public function single(bool $numeric = false) : ?array;
}