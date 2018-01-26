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

use \Lightbit\Http\HttpStatus;

$lightbit = Lightbit::getInstance();

?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="robots" content="noindex,nofollow" />
		<title><?= htmlentities($status->toString()) ?></title>
		<style type="text/css">
			<?php include (__DIR__ . '/css/main.php') ?>
		</style>
	</head>
	<body>
		<h1><?= htmlentities($status->toString()) ?></h1>
		<?php if ($lightbit->isDebug()) : ?>
		<hr />
		<?php while ($throwable) : ?>
		<div class="throwable">
			<h2 class="message"><?= htmlentities($throwable->getMessage()) ?></h2>
			<p class="monospace class"><?= htmlentities(get_class($throwable)) ?></p>
			<p class="monospace source"><?= htmlentities($throwable->getFile()) ?> (:<?= $throwable->getLine() ?>)</p>
			<pre><?= $throwable->getTraceAsString() ?></pre>
			<?php $throwable = $throwable->getPrevious(); ?>
		</div>
		<?php endwhile; ?>
		<?php endif ?>
	</body>
</html>