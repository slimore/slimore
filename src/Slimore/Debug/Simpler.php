<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/slimore/slimore
 * @license     MIT License https://github.com/slimore/slimore#license
 * @version     0.1.0
 * @package     Slimore\Debug
 */

namespace Slimore\Debug;

/**
 * Class Simpler
 *
 * @author  Pandao
 * @package Slimore\Debug
 */

class Simpler
{
    /**
     * Construction method
     */
    public function __construct()
    {

    }

    /**
     * Using javascript
     *
     * @param string|array $script
     * @param bool $wrap false
     * @return string
     */

    public static function js($script, $wrap = false)
    {
        echo js($script, $wrap);
    }

    /**
     * Alias js() method
     *
     * @param string|array $script
     * @param bool $wrap false
     * @return string
     */

    public static function javascript($script, $wrap = false)
    {
        echo js($script, $wrap);
    }

    /**
     * Using pre format tag and var_dump() formatted printing array/object
     *
     * @param array|mixed $array
     * @return void
     */

    public static function varDump($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    /**
     * Using pre format tag and print_r() formatted printing array/object
     *
     * @param array|mixed $array
     * @return void
     */

	public static function printr($array)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}

    /**
     * Array to json
     *
     * @param array $array
     * @param bool $return false
     * @return string
     */

    public static function json(array $array, $return = false)
    {
        header('Content-Type: application/json');

        $json = json_encode($array);

        if ($return) return $json;
        else         echo   $json;
    }

    /**
     * Like/Using javascript console object
     *
     * @param $message
     * @param string $type log
     * @return string
     */

    public static function console($message, $type = 'log')
    {
        echo console($message, $type);
    }
}