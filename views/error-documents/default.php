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

$counter = 0;

$title = '500 Internal Server Error';

if ($throwable instanceof Lightbit\Http\HttpStatusException)
{
	$title = $throwable->getStatusCode() . ' ' . $throwable->getStatusMessage();
}

?>

<!DOCTYPE html />

<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?= htmlspecialchars($title) ?></title>
		<style type="text/css">
			* {
				margin: 0;
				padding: 0;
				border: none;
				border-collapse: collapse;
				box-shadow: none;
				text-shadow: none;
				font: inherit;
				line-height: 1;
			}

			body {
				font-size: 14px;
				font-family: sans-serif;
				line-height: 1.5em;
				padding: 1em;
			}

			h1 {
				font-size: 2.5em;
				line-height: 1.5em;
			}

			<?php if (__debug()) : ?>
			h1, h2{
				margin: 2em 0 1em 0;
			}

			body &gt; h1:first-child {
				margin-top: 1em;
			}

			h2 {
				font-size: 1.5em;
				line-height: 1.5em;
			}

			p {
				margin: 1em 0;
				line-height: 1.5em;
			}

			pre {
				line-height: 1.5em;
				overflow: auto;
			}

			hr {
				margin: 4em 0;
				height: 0;
				border-bottom: 1px solid #000;
			}
			<?php endif; ?>
		</style>
	</head>
	<body>
		<h1><?= htmlspecialchars($title) ?></h1>
		<?php if (__debug()) : ?>
		<?php while ($throwable) : ++$counter; ?>
		<div class="throwable">
			<h2><?= htmlspecialchars($throwable->getMessage()) ?></h2>
			<pre><?php echo '<strong>', htmlspecialchars($throwable->getFile()), ' : ', $throwable->getLine(), '</strong>', PHP_EOL, htmlspecialchars($throwable->getTraceAsString()) ?></pre>
		</div>
		<?php $throwable = $throwable->getPrevious(); endwhile; ?>
		<?php endif; ?>
	</body>
</html>