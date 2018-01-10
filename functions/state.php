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

function __state_save()
{
	global $__LIGHTBIT_ASSET;
	global $__LIGHTBIT_CLASS;
	global $__LIGHTBIT_NAMESPACE;

	if (__state_allow_save())
	{
		apcu_store('__lightbit.state.asset', $__LIGHTBIT_ASSET);
		apcu_store('__lightbit.state.class', $__LIGHTBIT_CLASS);
		apcu_store('__lightbit.state.namespace', $__LIGHTBIT_NAMESPACE);
	}
}

function __state_resume()
{
	global $__LIGHTBIT_ASSET;
	global $__LIGHTBIT_CLASS;
	global $__LIGHTBIT_NAMESPACE;

	if (__state_allow_save())
	{
		if (apcu_exists('__lightbit.state.asset'))
		{
			$__LIGHTBIT_ASSET = apcu_fetch('__lightbit.state.asset');
		}

		if (apcu_exists('__lightbit.state.class'))
		{
			$__LIGHTBIT_CLASS = apcu_fetch('__lightbit.state.class');
		}

		if (apcu_exists('__lightbit.state.namespace'))
		{
			$__LIGHTBIT_NAMESPACE = apcu_fetch('__lightbit.state.namespace');
		}
	}
}

function __state_allow_save()
{
	static $result;

	if (!isset($result))
	{
		$result = (!__debug() && extension_loaded('apcu'));
	}
	
	return $result;
}