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

namespace Lightbit\Hooking;

use \Closure;

use \Lightbit;
use \Lightbit\Hooking\Hook;
use \Lightbit\IO\AssetManagement\AssetProvider;

/**
 * HookManager.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class HookManager
{
	/**
	 * The instance.
	 *
	 * @var HookManager
	 */
	private static $instance;

	/**
	 * Gets the instance.
	 *
	 * @return HookManager
	 * 	The instance.
	 */
	public static final function getInstance() : HookManager
	{
		return (self::$instance ?? (self::$instance = new HookManager()));
	}

	/**
	 * The asset provider.
	 *
	 * @var IAssetProvider
	 */
	private $assetProvider;

	/**
	 * The hook directory paths.
	 *
	 * @var array
	 */
	private $hookDirectoryPaths;

	/**
	 * The hook directory script paths.
	 *
	 * @var array
	 */
	private $hookDirectoryScriptPaths;

	/**
	 * The hooks.
	 *
	 * @var array
	 */
	private $hooks;

	/**
	 * The lightbit.
	 *
	 * @var Lightbit
	 */
	private $lightbit;

	/**
	 * Construct.
	 */
	public function __construct()
	{
		$this->assetProvider = AssetProvider::getInstance();
		$this->hookDirectoryPaths = [];
		$this->hookDirectoryScriptPaths = [];
		$this->hooks = [];
		$this->lightbit = Lightbit::getInstance();
	}

	/**
	 * Creates and attaches a hook.
	 *
	 * @param string $name
	 * 	The hook name.
	 *
	 * @param Closure $closure
	 * 	The hook closure.
	 */
	public final function attach(string $name, Closure $closure) : void
	{
		$this->hooks[$name][] = new Hook($name, $closure);
	}

	/**
	 * Imports a hook script directory path.
	 *
	 * @param string $directory
	 *	The hook script directory asset identifier.
	 */
	public final function import(string $directory) : void
	{
		$asset = AssetProvider::getInstance()->getDirectoryAsset($directory);

		if ($asset->exists())
		{
			$this->hookDirectoryPaths[] = $asset->getPath();
		}
	}

	/**
	 * Triggers a hook.
	 *
	 * @param string $name
	 * 	The hook name.
	 *
	 * @param mixed $arguments
	 * 	The hook arguments.
	 */
	public final function trigger(string $name, ...$arguments) : void
	{
		if (!isset($this->hookDirectoryScriptPaths[$name]))
		{
			$this->hookDirectoryScriptPaths[$name] = [];

			foreach ($this->hookDirectoryPaths as $i => $basePath)
			{
				$filePath = $basePath . DIRECTORY_SEPARATOR . $name . '.php';

				if (file_exists($filePath) && is_file($filePath))
				{
					$closure = $this->lightbit->include($filePath);

					if (! ($closure instanceof Closure))
					{
						throw new HookManagerException($this, sprintf('Can not prepare script hook, it did not return closure: "%s"', $path));
					}

					$this->hooks[$name][] = new Hook($name, $closure);
					$this->hookDirectoryScriptPaths[$name][] = $filePath;
				}
			}
		}

		if (isset($this->hooks[$name]))
		{
			foreach ($this->hooks[$name] as $i => $hook)
			{
				($hook->getClosure())(...$arguments);
			}
		}
	}
}
