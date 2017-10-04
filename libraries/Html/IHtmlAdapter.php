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

namespace Lightbit\Html;

use \Lightbit\Base\IComponent;
use \Lightbit\Data\IModel;

/**
 * IHtmlAdapter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface IHtmlAdapter extends IComponent
{
	/**
	 * Gets an active input identifier.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input identifier.
	 */
	public function getActiveInputID(IModel $model, string $attribute) : string;

	/**
	 * Gets an active input name.
	 *
	 * @param IModel $model
	 *	The active input model.
	 *
	 * @param string $attribute
	 *	The active input attribute name.
	 *
	 * @return string
	 *	The active input name.
	 */
	public function getActiveInputName(IModel $model, string $attribute) : string;
}