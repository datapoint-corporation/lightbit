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

use \Lightbit\Base\IApplication;
use \Lightbit\Base\IContext;
use \Lightbit\ClassPathException;
use \Lightbit\Exception;
use \Lightbit\Http\HttpStatus;
use \Lightbit\Http\HttpStatusException;
use \Lightbit\IO\FileSystem\FileNotFoundException;
use \Lightbit\IO\FileSystem\Asset;
use \Lightbit\NamespacePathException;
use \Lightbit\ObjectFactory;
use \Lightbit\Script;
use \Lightbit\ResourceException;
use \Lightbit\Routing\Action;
use \Lightbit\Runtime\RuntimeEnvironment;
use \Lightbit\Runtime\RuntimeException;

/**
 * Lightbit.
 *
 * @author Datapoint — Sistemas de Informação, Unipessoal, Lda.
 * @since 1.0.0
 */
final class Lightbit
{
	/**
	 * The framework version.
	 *
	 * @var string
	 */
	public const VERSION = '1.0.0';

	/**
	 * The framework version build number.
	 *
	 * @var string
	 */
	public const VERSION_BUILD = '201801092355';

	/**
	 * The framework version signature.
	 *
	 * @var string
	 */
	public const VERSION_SIGNATURE = 'Lightbit/1.0.0.201801092355';

	/**
	 * The lightbit instance.
	 *
	 * @var Lightbit
	 */
	private static $lightbit;

	/**
	 * Gets the lightbit instance.
	 *
	 * @return Lightbit
	 *	The lightbit instance.
	 */
	public static function getInstance() : Lightbit
	{
		return (self::$lightbit ?? (self::$lightbit = new Lightbit()));
	}

	/**
	 * The action.
	 *
	 * @var Action
	 */
	private $action;

	/**
	 * The application.
	 *
	 * @var IApplication
	 */
	private $application;

	/**
	 * The context.
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * The classes path.
	 *
	 * @var array
	 */
	private $classesPath;

	/**
	 * The debug flag.
	 *
	 * @var bool
	 */
	private $debug;

	/**
	 * The increment.
	 *
	 * @var int
	 */
	private $increment;

	/**
	 * The namespaces path.
	 *
	 * @var array
	 */
	private $namespacesPath;

	/**
	 * The prefixes path.
	 *
	 * @var array
	 */
	private $prefixesPath;

	/**
	 * The resources path.
	 *
	 * @var array
	 */
	private $resourcesPath;

	/**
	 * The strict flag.
	 *
	 * @var bool
	 */
	private $strict;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->classesPath = [];
		$this->debug = false;
		$this->increment = 0;
		$this->namespacesPath = [];
		$this->prefixesPath = [];
		$this->strict = true;

		if (function_exists('apcu_fetch'))
		{
			if ($status = apcu_fetch([ 'lightbit.path.class', 'lightbit.path.namespace' ]))
			{
				$this->classesPath = $status['lightbit.path.class'];
				$this->namespacesPath = $status['lightbit.path.namespace'];
			}
		}
	}

	/**
	 * Autoload a class.
	 *
	 * If the class matches an available namespace, an attempt will be made
	 * to resolve the name to the absolute path of the matching file and, if
	 * it doesn't exist, an exception is thrown.
	 *
	 * @param string $class
	 *	The class name.
	 */
	public function autoload(string $class) : void
	{
		static $closure;

		if (!isset($closure))
		{
			$closure = 

			(
				function($__FILE__)
				{
					return require ($__FILE__);
				}
			)

			->bindTo(null, null);
		}

		try
		{
			$closure($this->getClassPath($class));
		}
		catch (ClassPathException $e)
		{

		}
	}

	/**
	 * Disposes.
	 *
	 * It begins by disposing the application, followed by the framework
	 * internal elements.
	 */
	public function dispose() : void
	{
		if (isset($this->application))
		{
			$this->application->dispose();
			unset($this->action);
			unset($this->context);
			unset($this->application);	
		}

		if (function_exists('apcu_store'))
		{
			apcu_store('lightbit.path.class', $this->classesPath);
			apcu_store('lightbit.path.namespace', $this->namespacesPath);
		}
	}

	/**
	 * Error handler.
	 *
	 * If a context is set, the error handling is delegated to it, its
	 * parents and, ultimately, the application.
	 *
	 * @param int $level
	 *	The error level.
	 *
	 * @param string $message
	 *	The error message.
	 *
	 * @param string $file
	 *	The error file path.
	 *
	 * @param int $line
	 *	The error line.
	 */
	public function error(int $level, string $message, string $file, int $line) : void
	{
		if ($this->strict || !in_array($level, [ E_STRICT, E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE ]))
		{
			throw new Exception(sprintf('%s in %s (:%d)', $message, $file, $line));
		}
	}

	/**
	 * Gets the next increment.
	 *
	 * @return int
	 *	The next increment.
	 */
	public function increment() : int
	{
		return ++$this->increment;
	}

	public function getAction() : Action
	{
		if (!isset($this->action))
		{
			throw new IllegalStateException(sprintf('Can not get action, not set'));
		}

		return $this->action;
	}

	/**
	 * Gets the application.
	 *
	 * @return IApplication
	 *	The application.
	 */
	public function getApplication() : IApplication
	{
		if (!isset($this->application))
		{
			throw new IllegalStateException(sprintf('Can not get application, not set'));
		}

		return $this->application;
	}

	/** 
	 * Gets an asset path.
	 *
	 * It is intended to avoid the use of real paths, absolute or relative,
	 * thus preventing remote file inclusion based exploits.
	 *
	 * @param string $context
	 *	The asset base path.
	 *
	 * @param string $asset
	 *	The asset identifier.
	 *
	 * @param string $extension
	 *	The asset file extension.
	 *
	 * @return string
	 *	The path.
	 */
	public function getAssetPath(?string $context, string $id, ?string $extension = 'php') : string
	{
		return (new Asset($context, $id, $extension))->getPath();
	}

	/**
	 * Gets the class path.
	 *
	 * @param string $class
	 *	The class name.
	 *
	 * @return string
	 *	The class path.
	 */
	public function getClassPath(string $class) : string
	{
		if (!isset($this->classesPath[$class]))
		{
			if ($i = strrpos($class, '\\'))
			{
				try
				{
					$this->classesPath[$class] = $this->getNamespacePath(substr($class, 0, $i)) . DIRECTORY_SEPARATOR . substr($class, $i + 1) . '.php';
				}
				catch (NamespacePathException $e)
				{
					throw new ClassPathException(sprintf('Can not get class path, namespace is not set: "%s"', $class), $e);
				}
			}
			else
			{
				throw new ClassPathException(sprintf('Can not get class path, unknown namespace: "%s"', $class));
			}
		}

		return $this->classesPath[$class];
	}

	/**
	 * Gets the context.
	 *
	 * @return IContext
	 *	The context.
	 */
	public function getContext() : IContext
	{
		if (!isset($this->context))
		{
			throw new IllegalStateException(sprintf('Can not get context, not set'));
		}

		return $this->context;
	}

	/**
	 * Gets a namespace path.
	 *
	 * @param string $namespace
	 *	The namespace name.
	 *
	 * @return string
	 *	The namespace path.
	 */
	public function getNamespacePath(string $namespace) : string
	{
		if (!isset($this->namespacesPath[$namespace]))
		{
			$i;
			$parent = $namespace;

			while (($i = strrpos($parent, '\\')) !== false)
			{
				$parent = substr($namespace, 0, $i);

				if (isset($this->namespacesPath[$parent]))
				{
					return $this->namespacesPath[$namespace]
						= $this->namespacesPath[$parent]
						. DIRECTORY_SEPARATOR
						. strtr(substr($namespace, $i + 1), [ '\\' => DIRECTORY_SEPARATOR ]);
				}
			}

			throw new NamespacePathException(sprintf('Can not get namespace path, not set: "%s"', $namespace));
		}

		return $this->namespacesPath[$namespace];
	}

	/**
	 * Gets the path.
	 *
	 * @return string
	 *	The path.
	 */
	public function getPath() : string
	{
		return __DIR__;
	}

	/**
	 * Gets a prefix path.
	 *
	 * @param string $prefix
	 *	The prefix name.
	 *
	 * @return string
	 *	The prefix path.
	 */
	public function getPrefixPath(string $prefix) : string
	{
		if (!isset($this->prefixesPath[$prefix]))
		{
			throw new PrefixException($prefix, sprintf('Can not get prefix path, not set: "%s"', $prefix));
		}

		return $this->prefixesPath[$prefix];
	}

	/**
	 * Checks for a namespace path.
	 *
	 * @param string $namespace
	 *	The namespace name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasNamespacePath(string $namespace) : bool
	{
		try
		{
			$this->getNamespacePath($namespace);
		}
		catch (NamespacePathException $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks a prefix path.
	 *
	 * @param string $prefix
	 *	The prefix name.
	 *
	 * @return bool
	 *	The result.
	 */
	public function hasPrefixPath(string $prefix) : bool
	{
		return isset($this->prefixesPath[$prefix]);
	}

	/**
	 * Checks the debug flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isDebug() : bool
	{
		return $this->debug;
	}

	/**
	 * Checks the strict flag.
	 *
	 * @return bool
	 *	The result.
	 */
	public function isStrict() : bool
	{
		return $this->strict;
	}

	/**
	 * Creates and registers an application along with the private and public
	 * virtual path prefixes, before it runs.
	 *
	 * @param string $class
	 *	The application class name.
	 *
	 * @param string $private
	 *	The application install directory path.
	 *
	 * @param string $public
	 *	The application public directory path.
	 *
	 * @param string $configuration
	 *	The application configuration virtual path, relative to the application
	 *	install directory.
	 *
	 * @return int
	 *	The exit status.
	 */
	public function run(string $class, string $private, string $public, string $configuration = null) : int
	{
		if (isset($this->application))
		{
			throw new IllegalStateException(sprintf('Can not register application, already set: "%s"', $class));
		}

		// Set the private and public prefixes.
		$this->prefixesPath['private'] = strtr($private, [ '/' => DIRECTORY_SEPARATOR ]);
		$this->prefixesPath['public'] = strtr($public, [ '/' => DIRECTORY_SEPARATOR ]);

		// Get the dynamic application configuration.
		$properties = null;

		if (isset($configuration))
		{
			$properties = (new Script((new Asset($this->prefixesPath['private'], $configuration, 'php'))->getPath()))->include();

			if (!is_array($properties))
			{
				throw new ResourceException($configuration, sprintf('Can not get application configuration, bad script return data type: "%s"', $configuration));
			}
		}

		// Set the context and application.
		$this->context = $this->application = (new ObjectFactory())->getObjectOfClass
		(
			IApplication::class,
			$class,
			$private,
			$properties
		);

		try
		{
			$status = $this->application->run();
			$this->dispose();
			return $status;
		}
		catch (Throwable $e)
		{
			$this->throwable($e);
			$this->dispose();
			return 1;
		}

		return 0;
	}

	/**
	 * Sets the action.
	 *
	 * @param Action $action
	 *	The action.
	 *
	 * @return Action
	 *	The previous.
	 */
	public function setAction(?Action $action) : ?Action
	{
		$previous = $this->action;
		$this->action = $action;
		return $previous;
	}

	/**
	 * Sets the context.
	 *
	 * @param IContext $context
	 *	The context.
	 *
	 * @return IContext
	 *	The previous.
	 */
	public function setContext(IContext $context) : ?IContext
	{
		$previous = $this->context;
		$this->context = $context;
		return $previous;
	}

	/**
	 * Sets the debug flag.
	 *
	 * @param bool $debug
	 *	The debug flag.
	 */
	public function setDebug(bool $debug) : void
	{
		$this->debug = $debug;
	}

	/**
	 * Sets the namespace path.
	 *
	 * @param string $namespace
	 *	The namespace name.
	 *
	 * @param string $path
	 *	The namespace directory path.
	 */
	public function setNamespacePath(string $namespace, string $path) : void
	{
		$this->namespacesPath[$namespace] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets a prefix path.
	 *
	 * @param string $prefix
	 *	The prefix name.
	 *
	 * @param string $path
	 *	The prefix directory path.
	 */
	public function setPrefixPath(string $prefix, string $path) : void
	{
		$this->prefixesPath[$prefix] = strtr($path, [ '/' => DIRECTORY_SEPARATOR ]);
	}

	/**
	 * Sets the strict flag.
	 *
	 * @param bool $strict
	 *	The strict flag.
	 */
	public function setStrict(bool $strict) : void
	{
		$this->strict = $strict;
	}

	/**
	 * Throwable handler.
	 *
	 * If an action is set, the throwable handling is delegated to its,
	 * its parent contexts, falling back to the basic implementation.
	 *
	 * @return bool
	 *	The result.
	 */
	public function throwable(Throwable $throwable) : bool
	{
		// We have to ensure all active output buffers are disposed of
		// to avoid leaking sensitive information about the throwable.
		if (ob_get_level() > 0)
		{
			while (ob_get_level() > 1)
			{
				ob_end_clean();
			}

			ob_clean();
		}

		if (isset($this->action) && $this->action->getController()->throwable($throwable))
		{
			return true;
		}

		if (isset($this->context) || isset($this->application))
		{
			$context = $this->context ?? $this->application;

			do
			{
				if ($context->throwable($throwable))
				{
					return true;
				}
			}
			while ($context = $context->getContext());
		}

		if (RuntimeEnvironment::getInstance()->isWeb())
		{
			(new Script($this->getPath() . strtr('/views/error-documents/default.php', [ '/' => DIRECTORY_SEPARATOR ])))->include
			(
				null, 
				[ 
					'status' => (($throwable instanceof HttpStatusException) ? $throwable->getStatus() : new HttpStatus(500)), 
					'throwable' => $throwable 
				]
			);
		}

		else do
		{
			echo PHP_EOL;
			echo PHP_EOL;
			echo $throwable->getMessage(), PHP_EOL;
			echo $throwable->getFile(), ' (:', $throwable->getLine(), ')', PHP_EOL;
			echo $throwable->getTraceAsString(), PHP_EOL;
		}

		while ($throwable = $throwable->getPrevious());
		return true;
	}
}

// Bootstrap
// -----------------------------------------------------------------------------
(
	function(Lightbit $lightbit)
	{
		// Lightbit autoloader
		spl_autoload_register([ $lightbit, 'autoload' ], true, true);

		// Lightbit namespace and prefix paths
		$lightbit->setNamespacePath('Lightbit', __DIR__ . '/libraries');
		$lightbit->setPrefixPath('lightbit', __DIR__);

		// Lightbit exception and error handlers
		set_error_handler([ $lightbit, 'error' ], E_ALL);
		set_exception_handler([ $lightbit, 'throwable' ]);

		// Lightbit output buffer
		for ($i = ob_get_level(); $i > 0; --$i)
		{
			if (!ob_end_clean())
			{
				trigger_error(E_USER_ERROR, 'Can not clean the lightbit output buffer.');
				exit;
			}
		}

		if (!ob_start())
		{
			trigger_error(E_USER_ERROR, 'Can not start the lightbit output buffer.');
			exit;
		}
	}
)
(Lightbit::getInstance());