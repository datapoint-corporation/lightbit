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

use \Lightbit\Base\Context;
use \Lightbit\Data\SlugManager;
use \Lightbit\Globalization\MessageSource;
use \Lightbit\Security\Cryptography\PasswordDigest;

use \Lightbit\Base\IApplication;
use \Lightbit\Base\IContext;
use \Lightbit\Base\IModule;

/**
 * Module.
 *
 * @author Datapoint – Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
abstract class Module extends Context implements IModule
{
	/**
	 * Constructor.
	 *
	 * @param Context $context
	 *	The module context.
	 *
	 * @param string $id
	 *	The module identifier.
	 *
	 * @param string $path
	 *	The module path.
	 *
	 * @param array $configuration
	 *	The module configuration.
	 */
	public final function __construct(IContext $context, string $id, string $path, array $configuration = null)
	{
		parent::__construct($context, $id, $path);

		$this->setComponentsConfiguration
		(
			[
				'data.slug.manager' => [ '@class' => SlugManager::class ],
				'globalization.message.source' => [ '@class' => MessageSource::class ]
			]
		);

		$this->onConstruct();

		if ($configuration)
		{
			$this->configure($configuration);
		}

		$this->onAfterConstruct();
	}

	/**
	 * Gets the application.
	 *
	 * @return Application
	 *	The application.
	 */
	public final function getApplication() : IApplication
	{
		return __application();
	}

	/**
	 * On After Construct.
	 *
	 * This method is invoked during the module construction procedure,
	 * after the dynamic configuration is applied.
	 */
	protected function onAfterConstruct() : void
	{
		$this->raise('lightbit.base.module.construct.after', $this);
	}

	/**
	 * On Construct.
	 *
	 * This method is invoked during the module construction procedure,
	 * before the dynamic configuration is applied.
	 */
	protected function onConstruct() : void
	{
		$this->raise('lightbit.base.module.construct', $this);
	}
}
