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

namespace Lightbit\Html\Composition;

use \Throwable;

use \Lightbit\Configuration\ConfigurationProvider;
use \Lightbit\Html\Composition\HtmlComposer;
use \Lightbit\Html\Composition\HtmlComposerFactoryException;

use \Lightbit\Html\Composition\IHtmlComposer;
use \Lightbit\Html\Composition\IHtmlComposerFactory;

/**
 * HtmlComposerFactory.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlComposerFactory implements IHtmlComposerFactory
{
	/**
	 * Creates a composer.
	 *
	 * @throws HtmlComposerFactoryException
	 *	Thrown if the composer creation fails.
	 *
	 * @return IHtmlComposer
	 *	The composer.
	 */
	public function createComposer() : IHtmlComposer
	{
		try
		{
			return new HtmlComposer(
				ConfigurationProvider::getInstance()->getConfiguration(
					'lightbit.html.composer'
				)
			);
		}
		catch (Throwable $e)
		{
			throw new HtmlComposerFactoryException(
				$this,
				sprintf('Can not create html composer, uncaught throwable.'),
				$e
			);
		}
	}
}
