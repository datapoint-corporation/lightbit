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

namespace Lightbit\Security\Cryptography;

use \Lightbit\Base\Component;
use \Lightbit\Base\Context;
use \Lightbit\Security\Cryptography\IPasswordDigest;

/**
 * PasswordDigest.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
class PasswordDigest extends Component implements IPasswordDigest
{
	/**
	 * The cost.
	 *
	 * @type int
	 */
	private $cost;

	/**
	 * Constructor.
	 *
	 * @param IContext $context
	 *	The component context.
	 *
	 * @param string $id
	 *	The component identifier.
	 *
	 * @param array $configuration
	 *	The component configuration.
	 */
	public function __construct(IContext $context, string $id, array $configuration = null)
	{
		parent::__construct($context, $id);

		$this->cost = 7;

		if ($configuration)
		{
			$this->configure($configuration);
		}
	}

	/**
	 * Compares a password against a digest.
	 *
	 * @param string $password
	 *	The password.
	 *
	 * @param string $digest
	 *	The digest.
	 *
	 * @return bool
	 *	The result.
	 */
	public function compare(string $password, string $digest) : bool
	{
		return password_verify($password, $digest);
	}

	/**
	 * Digests the password.
	 *
	 * @param string $password
	 *	The password.
	 *
	 * @return string
	 *	The result.
	 */
	public function digest(string $password) : string
	{
		return password_hash($password, PASSWORD_BCRYPT, [ 'cost' => $this->cost ]);
	}

	/**
	 * Gets the cost.
	 *
	 * @return int
	 *	The cost.
	 */
	public final function getCost() : int
	{
		return $this->cost;
	}

	/**
	 * Sets the cost.
	 *
	 * @param int $cost
	 *	The cost.
	 */
	public final function setCost(int $cost) : void
	{
		if ($cost < 4)
		{
			$this->cost = 4;
		}

		else if ($cost > 31)
		{
			$this->cost = 31;
		}

		else
		{
			$this->cost = $cost;
		}
	}

	/**
	 * Validates a digest.
	 *
	 * If validation fails, the most likely reason is the digest algorithm has
	 * been hardened and, in that case, the digest should be refreshed through
	 * this component.
	 *
	 * @param string $digest
	 *	The digest.
	 *
	 * @return bool
	 *	The result.
	 */
	public function validate(string $digest) : bool
	{
		return password_needs_rehash($digest, PASSWORD_BCRYPT, [ 'cost' => $this->cost ]);
	}
}