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

namespace Lightbit\Http;

use \Lightbit\Base\Controller;
use \Lightbit\Data\Json\JsonSerializer;
use \Lightbit\Http\IHttpController;

/**
 * HttpController.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class HttpController extends Controller implements IHttpController
{
	/**
	 * Generates a redirect response.
	 *
	 * It begins by resetting the current response, setting the applicable
	 * status and location headers.
	 *
	 * @param array $route
	 *	The redirect route.
	 */
	public function redirect(array $route) : void
	{
		$context = $this->getContext();
		$response = $context->getHttpResponse();
		$router = $context->getHttpRouter();

		$response->reset();
		$response->setStatus(303);
		$response->setHeader('Location', $router->url($route, true));
	}

	/**
	 * On Result.
	 *
	 * It is invoked automatically during the action run procedure, after 
	 * and only if a value is returned by its implementation.
	 *
	 * @param mixed $result
	 *	The result.
	 */
	protected function onResult($result) : void
	{
		parent::onResult($result);

		if (is_int($result))
		{
			if ($result > 99 && $result < 300)
			{
				$response = $this->getContext()->getHttpResponse();
				$response->setStatus($result);
			}

			else if ($result > 399 && $result < 600)
			{
				throw new HttpStatusException($result, (new HttpStatus($result))->getMessage());
			}
		}

		else if (is_array($result))
		{
			// Get the response status and filter the result.
			$status = ((isset($result['@status']) && is_int($result['@status']) && $result['@status'] > 99 && $result['@status'] < 600) ? $result['@status'] : null);

			foreach (array_keys($result) as $i => $index)
			{
				if ($index && is_string($index) && $index[0] === '@')
				{
					unset($result[$index]);
				}
			}

			// Generate the output.
			$output = (new JsonSerializer())->serialize($result);

			// Set the response.
			$response = $this->getContext()->getHttpResponse();
			$response->reset();
			$response->setStatus($status);
			$response->setHeader('Content-Length', strlen($output));
			$response->setHeader('Content-Type', 'application/json; charset=utf-8');
			echo $output;
		}
	}
}