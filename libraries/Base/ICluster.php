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

namespace Lightbit\Base;

use \Lightbit\Base\IController;

/**
 * ICluster.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
interface ICluster
{
	/**
	 * Gets a controller.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return IController
	 *	The controller.
	 */
	public function getController(string $id) : IController;

	/**
	 * Checks a controller availability.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasController(string $id) : bool;

	/**
	 * Sets a controller configuration.
	 *
	 * @param string $id
	 *	The controller identifier.
	 *
	 * @param array $configuration
	 *	The controller configuration.
	 *
	 * @param bool $merge
	 *	The controllers configuration merge flag.
	 */
	public function setControllerConfiguration(string $id, array $configuration, bool $merge = true) : void;

	/**
	 * Sets the controllers configuration.
	 *
	 * @param array $controllersConfiguration
	 *	The controllers configuration.
	 *
	 * @param bool $merge
	 *	The controllers configuration merge flag.
	 */
	public function setControllersConfiguration(array $controllersConfiguration, bool $merge = true) : void;
}