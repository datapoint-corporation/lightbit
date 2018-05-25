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

$queryString = new HttpServerRequestQueryString([
	'id' => '3109',
	'offset' => '-31',
	'latitude' => '31.319028',
	'longitude' => '-31.31298',
	'emulation' => 'true'
]);

$this->exactly(
	3109,

	'Getting integer parameter "id" should return 3109 (int)',
	function() use ($queryString)
	{
		return $queryString->getInt('id');
	}
);

$this->exactly(
	-31,

	'Getting integer parameter "offset" should return -31 (int)',
	function() use ($queryString)
	{
		return $queryString->getInt('offset');
	}
);

$this->exactly(
	31.319028,

	'Getting integer parameter "latitude" should return 31.319028 (float)',
	function() use ($queryString)
	{
		return $queryString->getFloat('latitude');
	}
);

$this->exactly(
	-31.31298,

	'Getting integer parameter "longitude" should return -31.31298 (float)',
	function() use ($queryString)
	{
		return $queryString->getFloat('longitude');
	}
);

$this->exactly(
	true,

	'Getting integer parameter "emulation" should return true (bool)',
	function() use ($queryString)
	{
		return $queryString->getBool('emulation');
	}
);
