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

use \Lightbit\Base\View;
use \Lightbit\Http\HttpStatusException;

function __exit(int $code = 0) : void
{
	if ($application = __application_get())
	{
		$application->dispose();
	}

	__state_save();
	exit($code);
}

function __lightbit_autoload(string $__CLASS__) : void
{
	try
	{
		__include_file(__class_path_resolve($__CLASS__));
	}
	catch (Throwable $e)
	{
		__throw_class_not_found($__CLASS__, sprintf('Can not load class: class %s', $__CLASS__), $e);
	}
}

function __lightbit_error_handler(int $code, string $message, string $file, int $line) : bool
{
	$category = 'UNKNOWN ERROR';

	switch ($code)
	{
		case E_ERROR:
		case E_USER_ERROR:
			$category = 'FATAL ERROR';
			break;

		case E_WARNING:
		case E_NOTICE:
		case E_DEPRECATED:
		case E_STRICT:
		case E_RECOVERABLE_ERROR:
		case E_USER_WARNING:
		case E_USER_NOTICE:
		case E_USER_DEPRECATED:
			$category = 'WARNING';
			break;

		case E_PARSE:
			$category = 'PARSE ERROR';
			break;

		case E_COMPILE_ERROR:
			$category = 'COMPILE ERROR';
			break;

		case E_CORE_ERROR:
			$category = 'CORE ERROR';
			break;

		case E_CORE_WARNING:
			$category = 'CORE WARNING';
			break;

		case E_COMPILE_ERROR:
			$category = 'COMPILE ERROR';
			break;

		case E_COMPILE_WARNING:
			$category = 'COMPILE WARNING';
			break;
	}

	__throw(sprintf('%s: %s at %s :%d', $category, $message, $file, $line));
	return true;
}

function __lightbit_exception_handler(Throwable $throwable) : void
{
	// If something goes wrong, we want to ensure we don't accidentaly leak
	// any information about this error.
	__lightbit_output_reset();

	if (__environment_is_web())
	{
		http_response_code
		(
			($throwable instanceof \Lightbit\Http\HttpStatusException)
			? $throwable->getStatusCode()
			: 500
		);
	}

	// If an action is available, we'll go through the controller and its
	// parent contexts giving the opportunity to generate the proper response.
	if ($action = __action_get())
	{
		$controller = $action->getController();

		if ($controller->throwable($throwable))
		{
			goto __lightbit_exception_handler_j9;
		}

		$context = $controller->getContext();

		__lightbit_exception_handler_j1:
		if ($context->throwable($throwable))
		{
			goto __lightbit_exception_handler_j9;
		}

		if ($context = $context->getContext())
		{
			goto __lightbit_exception_handler_j1;
		}
	}
	else
	{
		// If a context is available, we'll go through it and its parents
		// contexts giving the opportunity to generate the proper response.
		if ($context = __context_get())
		{
			__lightbit_exception_handler_j2:
			if ($context->throwable($throwable))
			{
				goto __lightbit_exception_handler_j9;
			}

			if ($context = $context->getContext())
			{
				__context_set($context);
				goto __lightbit_exception_handler_j2;
			}
		}
	}

	// If the application is available, we also give it a chance to 
	// generate the proper response, if it hasn't already.
	$application = __application_get();

	if ($application)
	{
		__context_set($application);

		if ($application->throwable($throwable))
		{
			goto __lightbit_exception_handler_j9;
		}
	}

	// Finally, at the end, we'll generate the response ourselfs through
	// the default throwable implementation.
	__context_set(null);
	__lightbit_output_reset();
	__lightbit_throwable($throwable);

	__lightbit_exception_handler_j9:
	__exit(1);
	return;
}

function __lightbit_output_reset() : void
{
	if (ob_get_level() < 1)
	{
		// This should never happen, since an output buffer is explicitly
		// started at the framework main script.
		__exit(1);
		return;
	}

	if (__environment_is_web() && !headers_sent())
	{
		header_remove();
	}

	header('Content-Type: text/html; charset=utf-8');

	while (ob_get_level() > 1)
	{
		ob_end_clean();
	}

	ob_clean();
}

function __lightbit_throwable(Throwable $throwable) : void
{
	if (__environment_is_web())
	{
		// If a status exception is caught, we will use its status within
		// the generated response code.
		$status = ($throwable instanceof \Lightbit\Http\HttpStatusException)
			? $throwable->getStatusCode()
			: 500;

		if ($action = __action_get())
		{
			$context = $action->getContext();

			do
			{
				// When a context is available, we'll go through it in order to
				// find the a matching error document.
				$prefix = $context->getViewsBasePath() 
					. DIRECTORY_SEPARATOR
					. 'error-documents'
					. DIRECTORY_SEPARATOR;

				foreach ([ (string) $status, 'default' ] as $i => $token)
				{
					$path = $prefix . $token . '.php';

					if (is_file($path))
					{
						(new View($context, $path))->run([ 'status' => $status, 'throwable' => $throwable ]);
						return;
					}
				}
			}

			while ($context = $context->getContext());
		}

		if ($application = __application_get())
		{
			$prefix = LIGHTBIT_PATH
				. DIRECTORY_SEPARATOR
				. 'views'
				. DIRECTORY_SEPARATOR
				. 'error-documents'
				. DIRECTORY_SEPARATOR;

			foreach ([ (string) $status, 'default' ] as $i => $token)
			{
				$path = $prefix . $token . '.php';

				if (is_file($path))
				{
					(new View($application, $path))->run([ 'status' => $status, 'throwable' => $throwable ]);
					return;
				}
			}
		}

		// It will most likely never get here but, if it does, we need to
		// have a way of showing what just happened.
		echo '<h1>OUT OF CONTEXT EXCEPTION</h1>';
		echo '<hr />';

		do
		{
			echo sprintf
			(
				'<strong>%s: %s at %s, line %d</strong>',
				__html_encode(__type_of($throwable)), 
				__html_encode($throwable->getMessage()),
				__html_encode($throwable->getFile()), 
				$throwable->getLine()
			);
			echo '<br /><pre>', nl2br(__html_encode($throwable->getTraceAsString())), '</pre><br /><br />';
		}

		while ($throwable = $throwable->getPrevious());
		return;
	}

	do
	{
		echo sprintf('%s: %s at %s, line %d', __type_of($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()), PHP_EOL;
		echo $throwable->getTraceAsString(), PHP_EOL;
		echo PHP_EOL;
	}
	while ($throwable = $throwable->getPrevious());
}

function __lightbit_next_id() : int
{
	static $id = -1;
	return ++$id;
}

function __lightbit_version() : string
{
	return '1.0.0';
}
