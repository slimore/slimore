<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Log
 */

namespace Slimore\Log;

/**
 * Class Writer
 *
 * @author Pandao
 * @package Slimore\Log
 */

class Writer
{
    /**
     * @var int
     */
    protected $level;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var string
     */
    protected $messageOutput;

    /**
     * @var string
     */
    protected $label = 'DEBUG';

    /**
     * @var string
     */
    protected $path = '../app/logs';

    /**
     * @var string
     */

    protected $dateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $extension = 'log';

    /**
     * @var string
     */
    protected $messageFormat = '[%label%][#][%date%] %message%';

    /**
     * @var array
     */
    protected $messageFormatSearchs = ['%label%', '[#]', '%date%', '%message%'];

    /**
     * @var array
     */
    protected $messageFormatReplaces;

    /**
     * @var null|callable
     */
    public    $writeBeforeHandle = null;

    public    $customMessageFormatParser = false;

    /**
     * @var array
     */
    protected $levels = [
        1 => 'EMERGENCY',
        2 => 'ALERT',
        3 => 'CRITICAL',
        4 => 'ERROR',
        5 => 'WARNING',
        6 => 'NOTICE',
        7 => 'INFO',
        8 => 'DEBUG'
    ];

    /**
     * Constructor
     */

    public function __construct()
    {
    }

    /**
     * Settings
     *
     * @param string|array $key null
     * @param string $value null
     * @return void
     */

    public function set()
    {
        $args  = func_get_args();
        $count = func_num_args();

        if ( is_array($args[0]) )
        {
            foreach ($args[0] as $key => $value)
            {
                if ($key === 'path')
                {
                    $this->setPath($value);
                    continue;
                }

                if (property_exists($this, $key))
                {
                    $this->$key = $value;
                }
            }
        }
        elseif ($count === 2)
        {
            if ($args[0] === 'path')
            {
                $this->setPath($args[1]);
                return ;
            }

            if (property_exists($this, $args[0]))
            {
                $this[$args[0]] = $args[1];
            }
        }
    }

    /**
     * Setting logs path
     *
     * @param string $path
     *
     * @return void
     */

    public function setPath($path)
    {
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }

        $this->path = $path;
    }

    /**
     * Get log files saved path
     *
     * @return string
     */

    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get log level
     *
     * @return int
     */

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get log label
     *
     * @return string
     */

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get log message content
     *
     * @return mixed
     */

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get log message format
     *
     * @return string
     */

    public function getMessageFormat()
    {
        return $this->messageFormat;
    }

    /**
     * Set log message format
     *
     * @param string $format
     * @return void
     */

    public function setMessageFormat($format)
    {
        $this->messageFormat = (string) $format;
    }

    /**
     * Set log message format searchs
     *
     * @param array $searchs
     * @return void
     */

    public function setMessageFormatSearchs(array $searchs)
    {
        $this->messageFormatSearchs = $searchs;
    }

    /**
     * Get log message format searchs
     *
     * @return array
     * @return void
     */

    public function getMessageFormatSearchs()
    {
        return $this->messageFormatSearchs;
    }

    /**
     * Default log message format replaces
     *
     * @return void
     */

    protected function defaultMessageFormatReplaces()
    {
        $this->messageFormatReplaces = [
            $this->label,
            str_repeat(' ', 9 - strlen($this->label)),
            date('Y-m-d H:i:s e O'),
            $this->message
        ];
    }

    /**
     * Set log message format replaces
     *
     * @param array $replaces
     * @return void
     */

    public function setMessageFormatReplaces(array $replaces)
    {
        $this->messageFormatReplaces = $replaces;
    }

    /**
     * Get log message format repalces
     *
     * @return mixed
     */

    public function getMessageFormatReplaces()
    {
        return $this->messageFormatReplaces;
    }

    /**
     * Log message format parser
     *
     * @return void
     */

    protected function messageFormatParser()
    {
        $this->label = $this->levels[$this->level];

        $this->messageOutput = str_replace(
            $this->getMessageFormatSearchs(),
            $this->getMessageFormatReplaces(),
            $this->getMessageFormat()
        );
    }

    /**
     * Handle for writing log before
     *
     * @param callable $callback
     * @return void
     */

    protected function writeBefore(callable $callback)
    {
        $callback($this);
    }

    /**
     * Write to log file
     *
     * @param   mixed $content
     * @param   int   $level
     * @return  void
     */

    public function write($content, $level)
    {
        $this->message = (string) $content;
        $this->level   = $level;

        if ( is_callable($this->writeBeforeHandle) )
        {
            $this->writeBefore($this->writeBeforeHandle);

            if (!$this->customMessageFormatParser)
            {
                $this->defaultMessageFormatReplaces();
            }
        }
        else
        {
            $this->defaultMessageFormatReplaces();
        }

        $this->messageFormatParser();

        if ( !$this->resource )
        {
            $this->filename = date($this->dateFormat);

            if ( !empty($this->extension) )
            {
                $this->filename .= '.' . $this->extension;
            }

            $this->resource = fopen($this->path . DIRECTORY_SEPARATOR . $this->filename, 'a');
        }

        fwrite($this->resource, $this->messageOutput . PHP_EOL);
    }
}