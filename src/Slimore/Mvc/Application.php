<?php

namespace Slimore\Mvc;

class Application extends \Slim\Slim
{
	public $defaults = [
							'appPath'         => '',
							'modules'         => ['home'],
							'defaultModule'   => 'home',
							'disabledModules' => [],
							'mvcDirs'         => [
								'controller'  => 'controllers',
								'model'       => 'models',
								'view'        => 'views'
							],
							'autoloads'       => []
						];

	private $loadFiles = [];

	public function __construct(Array $settings = [])
	{
		parent::__construct(array_merge($this->defaults, $settings));

		$this->autoloads();
	}

	public function module($name, Callable $handle)
	{
	}

	public function addRoutes(Array $routes)
	{
	}

	private function __autoload($className)
	{
		$appPath       = $this->config('appPath');
		$autoloadDirs  = $this->config('autoloads');
		$modules       = array_merge([''], $this->config('modules'));
		$defaultModule = $this->config('defaultModule');

		foreach ($modules as $key => $module)
		{
			$module    = ($module == '') ? $module : $module . DIRECTORY_SEPARATOR;

			foreach ($this->config('mvcDirs') as $k => $mvc)
			{
				$tempFileName = $appPath . $className . '.php';
				$pathInfo     = pathinfo($tempFileName);
				$namespace    = str_replace($pathInfo['filename'], '', $className);
				$fileName     = $appPath . strtolower($namespace) . $pathInfo['basename'];
				//echo $fileName . "<br/>";

				if (file_exists($fileName))
				{
					$this->loadFiles[$className] = $fileName;

					if (!class_exists($className))
					{
						require $fileName;
					}
				}
			}
		}

		foreach ($autoloadDirs as $key => $dir)
		{
			$fileName = realpath($dir) . DIRECTORY_SEPARATOR . $className . '.php';
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);

			if (file_exists($fileName))
			{
				$this->loadFiles[$className] = $fileName;

				if (!class_exists($className))
				{
					require $fileName;
				}
			}
		}

		echo "<pre>";
		print_r($this->loadFiles);
		echo "</pre>";
	}

	private function autoloads()
	{
        spl_autoload_register(__NAMESPACE__ . "\\Application::__autoload");
	}
}
