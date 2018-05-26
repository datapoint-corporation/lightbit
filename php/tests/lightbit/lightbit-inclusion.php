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

//
// Zzzzzzzzz Zzzzzzzzzzzzz
//
// I know this is nowhere near close to being a good way of iterating through
// each file, but for now, consider this a promise that I'll get back to
// it soon.
//

$prefix = LB_PATH_LIGHTBIT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;
$suffix = '*.php';

for ($i = 0; strlen($prefix . $suffix) < 512; ++$i)
{
	if ($t = glob($prefix . $suffix, GLOB_NOESCAPE))
	{
		foreach ($t as $i => $filePath)
		{
			$this->exactly(
				true,

				sprintf('%s', $filePath),
				function() use ($filePath) {
					include_once ($filePath);
					return true;
				}
			);
		}
	}

	$prefix .= '*' . DIRECTORY_SEPARATOR;
}
