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

use \Lightbit\Base\Action;

$__LIGHTBIT_ACTION = null;

function __action() : Action
{
	global $__LIGHTBIT_ACTION;

	if (!isset($__LIGHTBIT_ACTION))
	{
		__throw_state('Can not get current action, not set.');
	}

	return $__LIGHTBIT_ACTION;
}

function __action_context() : Context
{
	global $__LIGHTBIT_ACTION;

	if (!isset($__LIGHTBIT_ACTION))
	{
		__throw_state('Can not get current action, not set.');
	}

	return $__LIGHTBIT_ACTION->getContext();
}

function __action_get() : ?Action
{
	global $__LIGHTBIT_ACTION;
 	return $__LIGHTBIT_ACTION;
}

function __action_replace(?Action $action) : ?Action
{
	global $__LIGHTBIT_ACTION;

	$previous = $__LIGHTBIT_ACTION;
	$__LIGHTBIT_ACTION = $action;

	return $action;
}

function __action_set(?Action $action) : void
{
	global $__LIGHTBIT_ACTION;
	$__LIGHTBIT_ACTION = $action;
}
