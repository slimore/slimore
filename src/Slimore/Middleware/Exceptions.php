<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Middleware
 */

namespace Slimore\Middleware;

/**
 * Class Exceptions
 *
 * @author Pandao
 * @package Slimore\Middleware
 */

class Exceptions extends \Slim\Middleware
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     * @param array $settings
     */

    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Call
     */

    public function call()
    {
        try
        {
            $this->next->call();
        }
        catch (\Exception $e)
        {
            $log = $this->app->getLog(); // Force Slim to append log to env if not already
            $env = $this->app->environment();
            $env['slim.log'] = $log;
            $env['slim.log']->error($e);
            $this->app->contentType('text/html');
            $this->app->response()->status(500);
            $this->app->response()->body($this->renderBody($env, $e));
        }
    }

    /**
     * Render response body
     *
     * @param  array      $env
     * @param  \Exception $exception
     * @return string
     */

    public function renderBody(&$env, $exception)
    {
        $title   = 'Slimore Application ErrorException';
        $code    = $exception->getCode();
        $message = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();
        $type    = get_class($exception);
        $trace   = str_replace(array('#', "\n"), array('<div class="trace-line">#', '</div>'), $exception->getTraceAsString());

        $html = html($title, [
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
                    '<h3>' . $type . '</h3>',
                    '<h4>Details ：</h4>',
                    '<p><strong>Type: </strong> ' . $type . '</p>',
                    '<p><strong>Message: </strong> ' . $message . '</p>',
                    '<p><strong>File: </strong> ' . $file . '</p>',
                    '<p><strong>Line: </strong> ' . $line . '</p>',
                    '<br/>',
                    '<h4>Trace：</h4>',
                    '<pre>' . $trace . '</pre>',
                    '<div class="logo">',
                        '<h1>Slimore</h1>',
                        '<p>The fully (H)MVC framework, based on the Slim PHP Framwork.</p>',
                    '</div>',
                '</div>',
            '</div>'
        ]);

        return $html;
    }
}