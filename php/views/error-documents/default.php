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

?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="robots" content="noindex,nofollow" />
		<title><?= htmlentities($statusMessage) ?></title>
		<style type="text/css">
			body
			{
				color: #171717;
				font-family: sans-serif;
				font-size: 14px;
				padding: 1em;
			}

			h1
			{
				font-size: 2.5em;
				line-height: 1.5em;
				margin: 1em 0 1rem 0;
			}

			p
			{
				line-height: 1.5em;
				margin: 1em 0;
			}

			<?php if ($application->isDebug()) : ?>
			div.throwable
			{
				margin: 2rem 0;
			}

			h2.message
			{
				font-size: 1.5em;
				margin: 1em 0 1rem 0;
			}

			p.class
			{
				font-weight: 700;
				margin: 1rem 0 0 0;
			}

			p.monospace
			{
				font-family: monospace;
			}

			p.source
			{
				margin: 0 0 1em 0;
				font-weight: 400;
			}

			hr
			{
				border: none;
				border-top: 1px dashed #171717;
				display: block;
				margin: 2rem 0 2rem 0;
				overflow: visible;
			}

			hr::after
			{
				content: "EXCEPTION STACK TRACE";
				display: block;
				line-height: 1em;
				color: #171717;
				font-family: monospace;
				font-size: .85em;
				margin: .5em 0 0 0;
			}

			pre
			{
				border-left: 2px solid #171717;
				box-sizing: border-box;
				font-family: monospace;
				line-height: 1.5em;
				margin: 1em 0 2em 0;
				padding: 0 0 0 1rem;
			}
			<?php endif; ?>
		</style>
	</head>
	<body>
		<h1><?= htmlentities($statusMessage) ?></h1>
		<?php if ($application->isDebug()) : ?>
		<hr />
		<?php while ($throwable) : ?>
		<div class="throwable">
			<h2 class="message"><?= htmlentities($throwable->getMessage()) ?></h2>
			<p class="monospace class"><?= htmlentities(get_class($throwable)) ?></p>
			<p class="monospace source"><?= htmlentities($throwable->getFile()) ?>(<?= $throwable->getLine() ?>)</p>
			<pre><?= $throwable->getTraceAsString() ?></pre>
			<?php $throwable = $throwable->getPrevious(); ?>
		</div>
		<?php endwhile; ?>
		<?php endif ?>
	</body>
</html>
