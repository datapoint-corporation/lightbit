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

namespace Lightbit\Html\Rendering;

use \Lightbit\Data\Filtering\FilterFactoryException;
use \Lightbit\Data\Filtering\FilterProvider;
use \Lightbit\Data\Filtering\FilterException;
use \Lightbit\Html\HtmlDocumentProvider;
use \Lightbit\Html\Composition\HtmlComposerProvider;
use \Lightbit\Html\Rendering\HtmlViewException;

use \Lightbit\Html\IHtmlDocument;
use \Lightbit\Html\Composition\IHtmlComposer;
use \Lightbit\Html\Rendering\IHtmlView;

/**
 * HtmlViewScope.
 *
 * @author Datapoint - Sistemas de Informação, Unipessoal, Lda.
 * @since 2.0.0
 */
class HtmlViewScope
{
	/**
	 * The view.
	 *
	 * @var IHtmlView
	 */
	private $view;

	/**
	 * Constructor.
	 *
	 * @param IHtmlView $view
	 *	The scope view.
	 */
	public function __construct(IHtmlView $view)
	{
		$this->view = $view;
	}

	/**
	 * Gets the composer.
	 *
	 * @return IHtmlComposer
	 *	The composer.
	 */
	public final function getComposer() : IHtmlComposer
	{
		return HtmlComposerProvider::getInstance()->getComposer();
	}

	/**
	 * Gets the document.
	 *
	 * @return IHtmlDocument
	 *	The document.
	 */
	public final function getDocument() : IHtmlDocument
	{
		return HtmlDocumentProvider::getInstance()->getDocument();
	}

	/**
	 * Filters a variable.
	 *
	 * @throws FilterFactoryException
	 *	Thrown when the filter creation fails.
	 *
	 * @throws FilterParseException
	 *	Thrown when the subject is a string with an incompatible format which
	 *	can not be parsed by the filter.
	 *
	 * @throws FilterTransformException
	 *	Thrown when the subject is of an incompatible type which can not
	 *	be transformed by the filter.
	 *
	 * @param string $type
	 *	The variable type or filter name, optionally preffixed by a question
	 *	mark ("?") to allow for null or undefined variables.
	 *
	 * @param mixed $variable
	 *	The variable input and output reference.
	 *
	 * @param mixed $default
	 *	The variable default value.
	 */
	public final function filter(?string $type, &$variable, $default = null) : void
	{
		if (isset($variable))
		{
			if ($type)
			{
				if ($type[0] === '?')
				{
					$type = substr($type, 1);
				}

				$variable = FilterProvider::getInstance()->getFilter($type)->transform($variable);
			}
		}
		else if (isset($default))
		{
			$variable = $default;
		}
		else if ($type && $type[0] === '?')
		{
			$variable = null;
		}
		else
		{
			throw new HtmlViewException($this->view,sprintf(
				'Can not filter view variable, it is not set'
			));
		}
	}

	/**
	 * Sets the base view.
	 *
	 * @param string $baseView
	 *	The base view resource identifier.
	 */
	public final function inherit(string $baseView, array $baseViewVariableMap = null) : void
	{
		$this->view->setBaseView(
			HtmlViewProvider::getInstance()->getView($baseView),
			$baseViewVariableMap
		);
	}
}
