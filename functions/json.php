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

function __json_encode(array $content, bool $object = false) : string
{
	global $__LIGHTBIT_DEBUG;

	$options = JSON_UNESCAPED_UNICODE;

	if ($object)
	{
		$options = $options | JSON_FORCE_OBJECT;
	}

	if ($__LIGHTBIT_DEBUG)
	{
		$options = $options | JSON_PRETTY_PRINT;
	}

	$result = json_encode($content, $options, 512);

	if ($result === false)
	{
		__throw(sprintf('Can not encode json content: %s', lcfirst(json_last_error_msg())));
	}

	return $result;
}

function __json_decode(string $content) : array
{
	$result = json_decode($content, true, 512, JSON_UNESCAPED_UNICODE);

	if (!isset($result) && json_last_error() !== JSON_ERROR_NONE)
	{
		__throw(sprintf('Can not encode json content: %s', lcfirst(json_last_error_msg())));	
	}

	return $result;
}