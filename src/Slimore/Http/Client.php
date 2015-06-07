<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/slimore/slimore
 * @license     MIT License https://github.com/slimore/slimore#license
 * @version     0.1.0
 * @package     Slimore\Http
 */

namespace Slimore\Http;

/**
 * Class Client
 *
 * @author  Pandao
 * @package \Slimore\Http
 */

class Client
{
    /**
     * HTTP Method const
     */
    const HEAD      = 'HEAD';
    const GET       = 'GET';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const DELETE    = 'DELETE';
    const PATCH     = 'PATCH';
    const OPTIONS   = 'OPTIONS';
    const TRACE     = 'TRACE';

    /**
     * Curl Options
     */

    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $header = false;

    /**
     * @var int
     */
    public $timeout = 1200;

    /**
     * @var array
     */
    public $headers = ['X-Framework-By: Slimore/0.1.0'];

    /**
     * @var bool
     */
    public $fileTime = true;

    /**
     * @var bool
     */
    public $nosignal = true;

    /**
     * @var string
     */
    public $userAgent = 'Slimore Http client';

    /**
     * @var bool
     */
    public $freshConnect = false;

    /**
     * @var bool
     */
    public $sslVerifyPeer = false;

    /**
     * @var bool
     */
    public $sslVerifyHost = false;

    /**
     * @var int
     */
    public $connectTimeout = 1200;

    /**
     * @var bool
     */
    public $returnTransfer = true;

    /**
     * @var resource
     */
    private $curl;

    /**
     * @var array
     */
    public  $info;

    /**
     * @var array
     */
    public  $errors  = [];

    /**
     * @var array
     */
    public  $options = [];

    /**
     * @var mixed
     */
    public  $response;

    /**
     * Constructor
     */

    public function __construct()
    {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * cURL init
     *
     * @param string $url null
     * @return void
     */

    public function curl($url = null)
    {
        $this->curl = curl_init($url);
    }

    /**
     * Default cURL options
     *
     * @return void
     */

    public function setDefaultOptions()
    {
        curl_setopt_array($this->curl, [
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_NOSIGNAL       => $this->nosignal,
            CURLOPT_FILETIME       => $this->fileTime,
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_HEADER         => $this->header,
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_RETURNTRANSFER => $this->returnTransfer,
            CURLOPT_SSL_VERIFYPEER => $this->sslVerifyPeer,
            CURLOPT_SSL_VERIFYHOST => $this->sslVerifyHost,
            CURLOPT_FRESH_CONNECT  => $this->freshConnect
        ]);
    }

    /**
     * Set cURL option
     *
     * @param mixed $option
     * @param mixed $value
     * @return void
     */

    public function setOption($option, $value)
    {
        curl_setopt($this->curl, $option, $value);
    }

    /**
     * Execute cURL
     *
     * @return mixed
     */

    public function execute()
    {
        $this->response = curl_exec($this->curl);

        return $this->response;
    }

    /**
     * Set cURL method
     *
     * @param string $method
     * @return void
     */

    public function method($method)
    {
        $this->headers = array_merge($this->headers, array('X-HTTP-Method-Override: ' . $method));
        $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
        $this->setOption(CURLOPT_HTTPHEADER, $this->headers);
    }

    /**
     * Close cURL
     *
     * @return void
     */

    public function close()
    {
        curl_close($this->curl);
    }

    /**
     * Get cURL errors
     *
     * @return mixed
     */

    public function error()
    {
        $this->errors = [
            'code'    => curl_errno($this->curl),
            'message' => curl_error($this->curl)
        ];

        return $this->error();
    }

    /**
     * Get cURL info
     *
     * @return mixed
     */

    public function getInfo()
    {
        $this->info = curl_getinfo($this->curl);

        return $this->info;
    }

    /**
     * Send Http query
     *
     * @param string       $url
     * @param string       $method GET
     * @param string|array $data  null
     * @param callable     $callback null
     * @return mixed
     */

    public function send($url, $method = 'GET', $data = null, callable $callback = null)
    {
        $this->curl($url);
        $this->setDefaultOptions();

        if (is_array($this->options))
        {
            if (count($this->options) > 0)
            {
                foreach ($this->options as $key => $value)
                {
                    $this->setOption($key, $value);
                }
            }
        }

        if ($method !== self::GET)
        {
            $this->method($method);
        }

        if ($method === self::POST || $method === self::PUT || $method === self::DELETE)
        {
            $this->setOption(CURLOPT_POSTFIELDS, $data);
        }

        $response = $this->execute();

        if (!$response)
        {
            $this->error();
        }

        $this->getInfo();
        $this->close();

        if ( is_callable($callback) )
        {
            $callback($this->response, $this->info, $this->errors);
        }

        return $response;
    }

    /**
     * HTTP HEAD method
     *
     * @param $url
     * @param string $fields null
     * @param callable $callback null
     */

    public function head($url, $fields = null, callable $callback = null)
    {
        $this->send($url, self::HEAD, $fields, $callback);
    }

    /**
     * HTTP GET method
     *
     * @param string $url
     * @param array|string $queries null
     * @param callable $callback null
     * @return mixed
     */

    public function get($url, $queries = null, callable $callback = null)
    {
        if ( is_array($queries) )
        {
            $queries = '?' . http_build_query($queries);
        }

        return $this->send($url . $queries, self::GET, [], $callback);
    }

    /**
     * HTTP POST method
     *
     * @param string   $url
     * @param array    $fields []
     * @param callable $callback null
     * @return mixed
     */

    public function post($url, array $fields = [], callable $callback = null)
    {
        return $this->send($url, self::POST, $fields, $callback);
    }

    /**
     * HTTP PUT method
     *
     * @param string        $url
     * @param string|array $fields null
     * @param callable     $callback null
     * @return mixed
     */

    public function put($url, $fields = null, callable $callback = null)
    {
        $fields = (is_array($fields)) ? http_build_query($fields) : $fields;

        return $this->send($url, self::PUT, $fields, $callback);
    }

    /**
     * HTTP DELETE method
     *
     * @param string        $url
     * @param string|array $fields null
     * @param callable     $callback null
     * @return mixed
     */

    public function delete($url, $fields = null, callable $callback = null)
    {
        $fields = ( is_array($fields) ) ? http_build_query($fields) : $fields;

        return $this->send($url, self::DELETE, $fields, $callback);
    }

    /**
     * HTTP PATCH method
     *
     * @param $url
     * @param string $fields null
     * @param callable $callback null
     */

    public function patch($url, $fields = null, callable $callback = null)
    {
        $this->send($url, self::PATCH, $fields, $callback);
    }

    /**
     * HTTP OPTIONS method
     *
     * @param $url
     * @param null $fields null
     * @param callable $callback null
     */
    public function options($url, $fields = null, callable $callback = null)
    {
        $this->send($url, self::OPTIONS, $fields, $callback);
    }

    /**
     * HTTP TRACE method
     *
     * @param $url
     * @param null $fields null
     * @param callable $callback null
     */

    public function trace($url, $fields = null, callable $callback = null)
    {
        $this->send($url, self::TRACE, $fields, $callback);
    }
}