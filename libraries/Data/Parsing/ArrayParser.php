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

namespace Lightbit\Data\Parsing;

use \Lightbit\Data\Parsing\Parser;
use \Lightbit\Data\Parsing\ParserException;

/**
 * ArrayParser.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class ArrayParser extends Parser
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * Composes a subject.
	 *
	 * @param mixed $subject.
	 *	The subject.
	 *
	 * @return string
	 *	The result.
	 */
	public function compose($subject) : string
	{
		if (is_string($subject))
		{
			$subject = $this->parse($subject);
		}

		if (is_array($subject))
		{
			foreach ($subject as $i => $token)
			{
				// Since encoding json with non-scalar values can result in
				// unexpected issues, we'll throw an exception...
				if (!is_scalar($token))
				{
					throw new ParserException($this, sprintf('Can not compose array, element data type is not scalar: "%s"', gettype($token)));
				}
			}

			return base64_encode(json_encode($subject));
		}

		throw new ParserException($this, sprintf('Can not compose array, unsupported subject data type: "%s"', gettype($subject)));
	}

	/**
	 * Parses a subject.
	 *
	 * @param mixed $subject.
	 *	The subject.
	 *
	 * @return mixed
	 *	The result.
	 */
	public function parse($subject)
	{
		if (is_array($subject))
		{
			return $subject;
		}

		if (is_string($subject))
		{
			if (($json = base64_decode($subject)) && ($result = json_decode($json, true, 512, JSON_BIGINT_AS_STRING)) && is_array($result))
			{
				foreach ($result as $i => $token)
				{
					// Since decoding json with non-scalar values can result in
					// unexpected issues, we'll throw an exception...
					if (!is_scalar($token))
					{
						throw new ParserException($this, sprintf('Can not parse array, element data type is not scalar: "%s"', gettype($token)));
					}
				}

				return $result;
			}
		}

		throw new ParserException($this, sprintf('Can not parse boolean, unsupported subject data type: "%s"', $subject));
	}
}