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

namespace Lightbit\Helpers;

use \Lightbit\Base\Object;
use \Lightbit\Exception;

/**
 * ObjectHelper.
 *
 * Provides static utility methods for the most common procedures based
 * on objects.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class ObjectHelper
{

	/**
	 * Configures an object.
	 *
	 * @param Object $object
	 *	The object to configure.
	 *
	 * @param array $configuration
	 *	The object configuration.
	 */
	public static function configure(Object $object, array $configuration) : void
	{
		$class = new \ReflectionClass($object);

		foreach ($configuration as $property => $value)
		{
			if (!$property || !is_string($property) || $property[0] == '@')
			{
				continue;
			}

			// Basically, we're going to exploit the fact that PHP methods
			// can be accessed in a case insensitive manner and use it
			// to boost our performance, while keeping support for
			// dashes and underscores.
			$subject = strtr($property, [ '-' => '', '_' => '' ]);
			$setterName = 'set' . $subject;

			if ($class->hasMethod($setterName))
			{
				$setter = $class->getMethod($setterName);

				if ($setter->isPublic()
					&& !$setter->isStatic()
					&& $setter->getNumberOfParameters() > 0
					&& $setter->getNumberOfRequiredParameters() < 2)
				{
					$setter->invoke($object, $value);
					continue;
				}
			}

			throw new Exception(sprintf('Object property setter is not defined: "%s" ("%s")', $property, $class->getName()));
		}
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		trigger_error(sprintf('Class does not support construction: "%s"', __CLASS__), E_USER_ERROR);
		exit(1);
	}
}
