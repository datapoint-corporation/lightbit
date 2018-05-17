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

namespace Lightbit\Runtime;

/**
 * RuntimeEnvironment.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class RuntimeEnvironment
{
	/**
	 * The instance.
	 *
	 * @var RuntimeEnvironment
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return RuntimeEnvironment
	 *	The runtime environment.
	 */
	public static final function getInstance() : RuntimeEnvironment
	{
		return (self::$instance ?? (self::$instance = new RuntimeEnvironment()));
	}

	/**
	 * Constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Gets the application path.
	 *
	 * Please note the application path may not match the directory containing
	 * the current entry-point script, as it can be explicitly set by
	 * defining the "LB_PATH_APPLICATION" constant.
	 *
	 * @return string
	 *	The application directory path.
	 */
	public final function getApplicationPath() : string
	{
		return LB_PATH_APPLICATION;
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 *	The name.
	 */
	public final function getName() : string
	{
		return LB_CONFIGURATION;
	}

	/**
	 * Gets the lightbit path.
	 *
	 * @return string
	 *	The lightbit path.
	 */
	public final function getLightbitPath() : string
	{
		return LB_PATH_LIGHTBIT;
	}

	/**
	 * Gets the script path.
	 *
	 * @return string
	 *	The script path.
	 */
	public final function getScriptPath() : string
	{
		return LB_PATH_SCRIPT;
	}

	/**
	 * Gets the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public final function isDebug() : bool
	{
		return LB_DEBUG;
	}
}
