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

use \Lightbit\Base\Component;

use \Lightbit\Base\IContext;
use \Lightbit\Data\IModel;
use \Lightbit\Html\IHtmlAdapter;

/**
 * HtmlAdapter.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class HtmlAdapter extends Component implements IHtmlAdapter
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
	public function getActiveInputID(IModel $model, string $attribute) : string
	{
		static $activeInputsID = [];

		$className = get_class($model);

		if (!isset($activeInputsID[$className][$attribute]))
		{
			if (!isset($activeInputsID[$className]))
			{
				$activeInputsID[$className] = [];
			}

			$session = $this->getAction()->getContext()->getHttpSession();

			$hash = hash('md5', (__lightbit_version() . '/' . get_class($model) . '/' . $attribute));
			$id = 'lightbit.html.adapter.input.' . $hash;

			$result = $session->get('?string', $id);

			if (!$result)
			{
				$result = sprintf('%x', crc32(hash('md5', ($hash . '/' . $session->getClientID()))));
				$session->set($id, $result);
			}

			$activeInputsID[$className][$attribute] = $result;
		}

		return $activeInputsID[$className][$attribute];
	}

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
	public function getActiveInputName(IModel $model, string $attribute) : string
	{
		static $activeInputsName = [];

		$className = get_class($model);

		if (!isset($activeInputsName[$className][$attribute]))
		{
			if (!isset($activeInputsName[$className]))
			{
				$activeInputsName[$className] = [];
			}

			$session = $this->getAction()->getContext()->getHttpSession();

			$hash = hash('md5', (__lightbit_version() . '/' . get_class($model) . '/' . $attribute));
			$id = 'lightbit.html.adapter.input.' . $hash . '.name';

			$result = $session->get('?string', $id);

			if (!$result)
			{
				$result = sprintf('%x', crc32(hash('md5', ($hash . '/' . $session->getClientID() . '/id'))));
				$session->set($id, $result);
			}

			$activeInputsName[$className][$attribute] = $result;
		}

		return $activeInputsName[$className][$attribute];
	}

	/**
	 * Gets the document attributes.
	 *
	 * @return array
	 *	The document attributes.
	 */
	public function getDocumentAttributes() : array
	{
		$locale = $this->getLocale();

		return
		[
			'lang' => $locale->getLanguageCode()
		];
	}
}
