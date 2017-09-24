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

/**
 * Throws an exception.
 *
 * @param string $message
 *	The exception message.
 *
 * @param array $arguments
 *	The exception message arguments.
 */
function __throw(string $message, ...$arguments) : void
{
	if (class_exists(Lightbit\Exception::class))
	{
		throw new \Lightbit\Exception(sprintf($message, ...$arguments));
	}

	throw new Exception(sprintf($message, ...$arguments));
}

function __throw_state(string $message, ...$arguments) : void
{
	if (class_exists(Lightbit\IllegalStateException::class))
	{
		throw new \Lightbit\IllegalStateException(sprintf($message, ...$arguments));
	}

	throw new IllegalStateException(sprintf($message, ...$arguments));
}
