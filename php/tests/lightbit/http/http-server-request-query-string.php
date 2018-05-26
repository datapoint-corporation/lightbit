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

use \Lightbit\Http\HttpServerRequestQueryString;

//
// Suite
//
$this->setTitle(HttpServerRequestQueryString::class);

//
// Objects
//
$queryString = new HttpServerRequestQueryString([
	'id' => '3109',
	'offset' => '-31',
	'latitude' => '31.319028',
	'longitude' => '-31.31298',
	'emulation' => 'true'
]);

//
// Test cases
//
$this->exactly(
	3109,

	'(HttpServerRequestQueryString)->getInt("id") must return an integer (3109)',
	function() use ($queryString)
	{
		return $queryString->getInt('id');
	}
);

$this->exactly(
	-31,

	'(HttpServerRequestQueryString)->getInt("offset") must return an integer (-31)',
	function() use ($queryString)
	{
		return $queryString->getInt('offset');
	}
);

$this->exactly(
	31.319028,

	'(HttpServerRequestQueryString)->getFloat("latitude") must return a float (31.319028)',
	function() use ($queryString)
	{
		return $queryString->getFloat('latitude');
	}
);

$this->exactly(
	-31.31298,

	'(HttpServerRequestQueryString)->getFloat("longitude") must return a float (-31.31298)',
	function() use ($queryString)
	{
		return $queryString->getFloat('longitude');
	}
);

$this->exactly(
	true,

	'(HttpServerRequestQueryString)->getBool("emulation") must return a boolean (true)',
	function() use ($queryString)
	{
		return $queryString->getBool('emulation');
	}
);

$this->exactly(
	'true',

	'(HttpServerRequestQueryString)->getString("emulation") must return a string (true)',
	function() use ($queryString)
	{
		return $queryString->getString('emulation');
	}
);

$this->throws(
	Lightbit\Http\HttpQueryStringParameterNotSetException::class,

	'(HttpServerRequestQueryString)->getString("not-set") should throw a not set exception',
	function() use ($queryString)
	{
		return $queryString->getString('not-set');
	}
);

$this->throws(
	Lightbit\Http\HttpQueryStringParameterParseException::class,

	'(HttpServerRequestQueryString)->getBool("longitude") should throw a parse exception',
	function() use ($queryString)
	{
		return $queryString->getBool('longitude');
	}
);
