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

use \Lightbit\Html\Composition\HtmlComposerFactory;
use \Lightbit\Html\Composition\HtmlComposerFactoryException;

use \Lightbit\Html\Composition\IHtmlComposer;
use \Lightbit\Html\Composition\IHtmlComposerFactory;

/**
 * HtmlComposerProvider.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
final class HtmlComposerProvider
{
	/**
	 * The singleton instance.
	 */
	private static $instance;

	/**
	 * Gets the singleton instance.
	 *
	 * @return HtmlComposerProvider
	 *	The singleton instance.
	 */
	public static final function getInstance() : HtmlComposerProvider
	{
		return (self::$instance ?? (self::$instance = new HtmlComposerProvider()));
	}

	/**
	 * The composer.
	 *
	 * @var IHtmlComposer
	 */
	private $composer;

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the composer.
	 *
	 * @throws HtmlComposerFactoryException
	 *	Thrown if the composer creation fails.
	 *
	 * @return IHtmlComposer
	 *	The composer.
	 */
	public final function getComposer() : IHtmlComposer
	{
		return ($this->composer ?? ($this->composer = $this->getComposerFactory()->createComposer()));
	}

	/**
	 * Gets the composer factory.
	 *
	 * @return IHtmlComposerFactory
	 *	The composer factory.
	 */
	public final function getComposerFactory() : IHtmlComposerFactory
	{
		return ($this->composerFactory ?? ($this->composerFactory = new HtmlComposerFactory()));
	}

}
