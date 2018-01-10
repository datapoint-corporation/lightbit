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

use \Lightbit\Base\Context;

$__LIGHTBIT_CONTEXT = null;

function __context() : Context
{
	global $__LIGHTBIT_CONTEXT;

	if (!isset($__LIGHTBIT_CONTEXT))
	{
		__throw_illegal_state('Context does not exist.');
	}

	return $__LIGHTBIT_CONTEXT;
}

function __context_get() : ?Context
{
	global $__LIGHTBIT_CONTEXT;
	return $__LIGHTBIT_CONTEXT;
}

function __context_replace(?Context $context) : ?Context
{
	global $__LIGHTBIT_CONTEXT;

	$previous = $__LIGHTBIT_CONTEXT;
	$__LIGHTBIT_CONTEXT = $context;

	return $previous;
}

function __context_set(?Context $context) : void
{
	global $__LIGHTBIT_CONTEXT;
	$__LIGHTBIT_CONTEXT = $context;
}
