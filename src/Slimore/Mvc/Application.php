<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Mvc
 */

namespace Slimore\Mvc;

use \Slimore\Database\Manager            as DB;
use \Illuminate\Container\Container      as Container;
use \Illuminate\Events\Dispatcher        as Dispatcher;
use \Slimore\Middleware\Exceptions       as ExceptionsMiddleware;

/**
 * Class Application
 *
 * @author Pandao
 * @package Slimore\Mvc
 */

class Application extends \Slim\Slim
{
    /**
     * @var string
     */
	public $version  = '0.1.0';

    /**
     * @var array $defaults
     */
	protected $defaults = [
							'path'               => '',
							'baseURL'            => '',
							'timezone'           => 'Asia/Shanghai',
    						'templates.path'     => '../app/views',
    						'cookies.encrypt'    => true,
    						'cookies.secret_key' => 'vadsfas',
							'modules'            => [],
							'defaultModule'      => '',
							'disabledModules'    => '',
							'template.suffix'    => '.php',
                            'x-framework-header' => true,
							'mvcDirs'            => [
								'controller'     => 'controllers',
								'model'          => 'models',
								'view'           => 'views'
							],
							'autoloads'          => [],
							'defaultAutoloads'   => [
								'../app/controllers',
								'../app/models',
							],
							'db'                 => []
						];

    /**
     * @var array $loadFiles
     */

	private $loadFiles = [];

    /**
     * @var string
     */
	public  $path;

    /**
     * @var string
     */
	public  $baseURL;

    /**
     * @var mixed
     */
	public  $db;

    /**
     * @var number
     */
    public  $startTime;

    /**
     * @var string
     */
    public  $moduleName = '';

    /**
     * @var string
     */
    public  $controllerName;

    /**
     * @var string
     */
    public  $actionName;

    /**
     * @var array
     */
    protected $routeInfo;

    /**
     * Constructor
     *
     * @param array $settings null
     */

	public function __construct(array $settings = null)
	{
        if (version_compare(phpversion(), '5.4.0', '<'))
        {
            throw new \RuntimeException('Slimore require PHP version >= 5.4.0');
        }

        require __DIR__ . "/../Helper/Functions.php";

		$settings = array_merge ($this->defaults, $settings);

        if ($settings['x-framework-header'])
        {
            header('X-Framework-By: Slimore/' . $this->version);
        }

		parent::__construct($settings);

		date_default_timezone_set($this->config('timezone'));

		$this->init();
	}

    /**
     * Application Initialization method
     *
     * @return void
     */

	public function init()
	{
		$this->path    = $this->config('path');
		$this->baseURL = $this->config('baseURL');

        $this->errorsHandle();
		$this->autoloads();
	}

    /**
     * Override Slim run method
     *
     * @return void
     */

    public function run()
    {
        set_error_handler(array('\Slim\Slim', 'handleErrors'));

        //Apply final outer middleware layers
        if ($this->config('debug')) {
            //Apply pretty exceptions only in debug to avoid accidental information leakage in production
            $this->add(new \Slimore\Middleware\Exceptions());
        }

        //Invoke middleware and application stack
        $this->middleware[0]->call();

        //Fetch status, header, and body
        list($status, $headers, $body) = $this->response->finalize();

        // Serialize cookies (with optional encryption)
        \Slim\Http\Util::serializeCookies($headers, $this->response->cookies, $this->settings);

        //Send headers
        if (headers_sent() === false) {
            //Send status
            if (strpos(PHP_SAPI, 'cgi') === 0) {
                header(sprintf('Status: %s', \Slim\Http\Response::getMessageForCode($status)));
            } else {
                header(sprintf('HTTP/%s %s', $this->config('http.version'), \Slim\Http\Response::getMessageForCode($status)));
            }

            //Send headers
            foreach ($headers as $name => $value) {
                $hValues = explode("\n", $value);
                foreach ($hValues as $hVal) {
                    header("$name: $hVal", false);
                }
            }
        }

        //Send body, but only if it isn't a HEAD request
        if (!$this->request->isHead()) {
            echo $body;
        }

        $this->applyHook('slim.after');

        restore_error_handler();
    }

    /**
     * Errors / Exceptions handle
     *
     * @return void
     */

    protected function errorsHandle()
    {
        $debug = $this->config('debug');

        error_reporting(($debug) ? E_ALL : E_ERROR & ~E_NOTICE);

        set_error_handler(function ($type, $message, $file, $line) {
            throw new \ErrorException($message, 0, $type, $file, $line);
        });

        set_exception_handler(function (\Exception $e) {

            $exception = new ExceptionsMiddleware;

            $log = $this->getLog(); // Force Slim to append log to env if not already
            $env = $this->environment();
            $env['slim.log'] = $log;
            $env['slim.log']->error($e);

            $this->contentType('text/html');
            $this->response()->status(500);

            echo $this->response()->body($exception->renderBody($env, $e));
        });

        register_shutdown_function(function () {

            if ( !is_null($options = error_get_last()) )
            {
                $title = 'Slimore Application Error Shutdown';

                $html  = html($title, [
                    '<meta http-equiv="X-UA-Compatible" content="IE=edge" />',
                    '<meta name="renderer" content="webkit" />',
                    '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />',
                    style([
                        '*{margin:0;padding:0;}',
                        'html {font-size: 62.5%;}',
                        'html, body {max-width: 100%;width: 100%;}',
                        'body {font-size: 1.4rem;font-family: "Microsoft Yahei", Helvetica, Tahoma, STXihei, arial,verdana,sans-serif;background:#fff;color:#555;}',
                        'img {border: none;}',
                        'pre, pre code {font-family:Consolas, arial,verdana,sans-serif;}',
                        '#layout {padding: 6rem;}',
                        '#main {margin: 0 auto;line-height: 1.5;}',
                        '#main > h1 {font-size: 10rem;margin-bottom: 1rem;}',
                        '#main > h3 {margin-bottom: 2rem; font-size: 1.8rem;}',
                        '#main > h4 {margin-bottom: 1rem; font-size: 1.8rem;}',
                        '#main pre {margin: 1.5rem 0;white-space: pre-wrap;word-wrap: break-word;}',
                        '#main > p {white-space: pre-wrap;word-wrap: break-word;line-height: 1.3;margin-bottom: 0.6rem;}',
                        '#main > p > strong {width: 7rem;display:inline-block;}',
                        '.logo {text-align: left;border-top:1px solid #eee;margin-top: 5rem;padding: 3rem 0 0;color: #ccc;}',
                        '.logo > h1 {font-weight: normal;font-size: 5rem;}',
                        '.logo img {margin-left: -2rem;}',
                        '.trace-line {padding: 0.3rem 0.6rem;margin-left:-0.6rem;-webkit-transition: background-color 300ms ease-out;transition: background-color 300ms ease-out;}',
                        '.trace-line:hover {background:#fffccc;}'
                    ])
                ], [
                    '<div id="layout">',
                    '<div id="main">',
                    '<h1>:(</h1>',
                    '<h3>' . $title . '</h3>',
                    '<h4>Details ：</h4>',
                    '<p><strong>Type: </strong> ' . $options['type'] . '</p>',
                    '<p><strong>Message: </strong> ' . $options['message'] . '</p>',
                    '<p><strong>File: </strong> ' . $options['file'] . '</p>',
                    '<p><strong>Line: </strong> ' . $options['line'] . '</p>',
                    '<div class="logo">',
                    '<h1>Slimore</h1>',
                    '<p>The fully (H)MVC framework, based on the Slim PHP Framwork.</p>',
                    '</div>',
                    '</div>',
                    '</div>'
                ]);

                echo $html;

                exit;
            }

        });
    }

	/**
	 * Configure the database and boot Eloquent
	 *
     * @param array $configs null
     * @param string $name default
     * @param bool $enableQueryLog true
     * @return mixed
     */

	public function dbConnection(array $configs = null, $name = 'default', $enableQueryLog = true)
	{
		$db = new DB();

		$db->addConnection(($configs) ? $configs : $this->config('db'), $name);
        $db->setEventDispatcher(new Dispatcher(new Container));
		$db->setAsGlobal();
		$db->bootEloquent();

        if ($enableQueryLog)
        {
            DB::connection()->enableQueryLog();
        }

        $this->db = $db;

        return $db;
	}

    /**
     * Modules namespace route handle method
     *
     * @param string $namespace
     * @param callable $callback
     * @return void
     */

    public function moduleNamespace($namespace, callable $callback)
    {
        if ( is_callable($callback) )
        {
            $callback($namespace, $this);
        }
    }

    /**
     * Route controller handle
     *
     * @param string $controller
     * @param callable $callback
     * @param string $namespace
     */

    public function controller($controller, callable $callback, $namespace = '')
    {
        if ( is_callable($callback) )
        {
            $callback(ucwords($controller) . 'Controller', $this, $namespace);
        }
    }

	/**
	 * Auto router method
	 *
     * @param bool $stop false
	 * @return void
	 */

	public function autoRoute($stop = false)
	{
        if ($stop)
        {
            return $this;
        }

		$app = self::getInstance();

        $app->get('(/)', 'IndexController:index');

		$app->get('/:action', function($action = 'index') use ($app) {

			if ( !class_exists('IndexController') )
			{
				$app->notFound();
				return ;
			}

            $this->controllerName = 'index';
            $this->actionName     = $action;

			$controller = new \IndexController;

			if ( !method_exists($controller, $action) )
			{
				$app->notFound();
				return ;
			}

			$controller->$action();
		});

		$app->get('/:controller/:action', function($controller = 'index', $action = 'index') use ($app) {

            $this->controllerName = $controller;
            $this->actionName     = $action;

			$controller  = ucwords($controller) . 'Controller';

			if ( !class_exists($controller) )
			{
				$app->notFound();
				return ;
			}

			$controller = new $controller();

			if ( !method_exists($controller, $action) )
			{
				$app->notFound();
				return ;
			}

			$controller->$action();
		});

		$app->get('/:module/:controller/:action', function($module, $controller = 'index', $action = 'index') use ($app) {
            $this->moduleName     = $module;
            $this->controllerName = $controller;
            $this->actionName     = $action;

			$controller = ucwords($module) . '\Controllers\\' . ucwords($controller) . 'Controller';

			if ( !class_exists($controller) )
			{
				$app->notFound();
				return ;
			}

			$controller = new $controller();

			if ( !method_exists($controller, $action) )
			{
				$app->notFound();
				return ;
			}

			$controller->$action();
		});
	}

	/**
	 * Autoload callable method
	 *
	 * @param string $className
	 * @return void
	 */

	protected function __autoload($className)
	{
		// Single modules autoload

		foreach ($this->config('defaultAutoloads') as $key => $dir)
		{
			$fileName = $dir . DIRECTORY_SEPARATOR . $className . '.php';
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);

			if (file_exists($fileName) && !class_exists($className))
			{
				$this->loadFiles[$className] = $fileName;

				require $fileName;
			}
		}

		// Multi-Modules autoload

		$modules = array_merge([], $this->config('modules'));

		foreach ($modules as $key => $module)
		{
			$module = ($module == '') ? $module : $module . DIRECTORY_SEPARATOR;

			foreach ($this->config('mvcDirs') as $k => $mvc)
			{
                $phpFile  = explode("\\", $className);
                $phpFile  = $phpFile[count($phpFile) - 1];
				$fileName = $this->path . str_replace('\\', DIRECTORY_SEPARATOR, strtolower(str_replace($phpFile, '', $className))). $phpFile . '.php';

				if (file_exists($fileName) && !class_exists($className))
				{
					$this->loadFiles[$className] = $fileName;

					require $fileName;
				}
			}
		}

		// User custom autoloads

		foreach ($this->config('autoloads') as $key => $dir)
		{
			$fileName = realpath($dir) . DIRECTORY_SEPARATOR . $className . '.php';
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);

			if (file_exists($fileName) && !class_exists($className))
			{
				$this->loadFiles[$className] = $fileName;

				require $fileName;
			}
		}
	}

	/**
	 * SPL autoload register method
	 *
	 * @return void
	 */

	protected function autoloads()
	{
        spl_autoload_register(__CLASS__ . "::__autoload");
	}

    /**
     * Run time start
     *
     * @return number
     */

    public function timeStart()
    {
        $now = explode(" ", microtime());
        $this->startTime = $now[1] + $now[0];

        return $this->startTime;
    }

    /**
     * Run end time
     *
     * @param int $decimal 6
     * @return number
     */

    public function timeEnd($decimal = 6)
    {
        $now   = explode(" ", microtime());
        $end   = $now[1] + $now[0];
        $total = ($end - $this->startTime);

        return number_format($total, $decimal);
    }
}
