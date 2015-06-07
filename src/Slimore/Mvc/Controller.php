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

use \Slimore\Debug           as Debug;
use \Slimore\Mvc\Application as Application;

/**
 * Class Controller
 *
 * @author  Pandao
 * @package Slimore\Mvc
 */

class Controller
{
    /**
     * @var \Slim\Slim
     */

	protected $app;

    /**
     * @var mixed
     */

    protected $db;

    /**
     * @var mixed
     */

    protected $log;

    /**
     * @var string
     */

    protected $viewPath   = '../app/views/';

    /**
     * @var string
     */

    protected $viewSuffix = '.php';

    /**
     * Constructor
     */

	public function __construct()
	{
		$this->app = Application::getInstance();
        $this->db  = $this->app->db;
        $this->log = $this->app->log;

		$this->setViewPath($this->app->path . $this->moduleName . DIRECTORY_SEPARATOR . 'views');

        $this->init();
	}

    /**
     * Initialization (supplementary) method
     *
     * @return void
     */

    protected function init()
    {
    }

    /**
     * @param string $name null
     * @return mixed
     */

    protected function dbConnection($name = null)
    {
        return $this->db->getConnection($name);
    }

    /**
     * @param array $array
     * @param bool $return false
     * @return string
     */

	public function json(array $array, $return = false)
	{
		$this->response->header('Content-Type', 'application/json');
		$this->response->status(200);

        $jsonpCallback = $this->app->request->get('callback', null);

		$json = ($jsonpCallback !== null) ? $jsonpCallback . '('.json_encode($array).')' : json_encode($array);

        if ($return) return $json;
        else         echo   $json;
	}

    /**
     * Using javascript
     *
     * @param string|array $script
     * @param bool $wrap false
     * @return string
     */

    public function js($script, $wrap = false)
    {
        Debug::js($script, $wrap);
    }

    /**
     * Alias js() method
     *
     * @param string|array $script
     * @param bool $wrap false
     * @return string
     */

    public function javascript($script, $wrap = false)
    {
        Debug::js($script, $wrap);
    }

    /**
     * Using pre format tag formatted printing array
     *
     * @param mixed $array
     * @return void
     */

    public function printr($array)
    {
        Debug::printr($array);
    }

    /**
     * Like/Using javascript console object
     *
     * @param string $message
     * @param string $type "log"
     * @return string
     */

    public function console($message, $type = "log")
    {
        Debug::console($message, $type);
    }

    /**
     * Using javascript location.href go to url
     *
     * @param string $url
     * @param bool $base true
     * @return string
     */

    public function gotoURL($url, $base = true)
    {
        if ($base) {
            $url = $this->app->baseURL . $url;
        }

        $this->js('location.href="'.$url.'";');
    }

    /**
     * Application (re)configure
     *
     * @param string $name
     * @return mixed
     */

    protected function config($name)
	{
		return $this->app->config($name);
	}

    /**
     * Redirect controller
     *
     * @param string $path
     * @return void
     */

    protected function redirect($path)
	{
		$path = str_replace('//', '/', $this->app->baseURL . $path);

		$this->app->redirect($path);
	}

    /**
     * Override \Slim\Slim::render method on controller
     *
     * @param string $tpl
     * @param array $data []
     * @param string $suffix
     * @return void
     */

	protected function render($tpl, array $data = [])
	{
		$suffix = $this->app->config('template.suffix');
        $suffix = (empty($suffix)) ? $this->viewSuffix : $suffix;
        $tpl   .= $suffix;

		$this->app->render($tpl, $data);
	}

    /**
     * Set view path
     *
     * @param string $path
     */

    protected function setViewPath($path)
    {
        $this->view->setTemplatesDirectory($path);
    }

    /**
     * Get view path
     *
     * @return string
     */

    protected function getViewPath()
    {
        return $this->view->getTemplatesDirectory();
    }

    /**
     * Getter
     *
     * @param $name
     * @return mixed
     */

	public function __get($name)
	{
		if ( property_exists ($this->app, $name ) || method_exists ($this->app, $name ))
		{
            return  $this->app->$name;
		}
	}

    /**
     * Member method overloading
     *
     * @param $method
     * @param $args
     * @return mixed
     */

    public function __call($method, $args)
    {
        if ( method_exists ($this->app, $method ))
        {
            return $this->app->$method($args);
        }
    }

    /**
     * Static member method overloading
     *
     * @param $method
     * @param $args
     * @return mixed
     */

    public static function __callStatic($method, $args)
    {
        $app = self::$app;

        if ( method_exists ($app, $method ))
        {
            return $app::$method($args);
        }
    }
}