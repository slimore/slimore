<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Cache
 */

namespace Slimore\Cache;

use \Slimore\Cache\Exception\File as FileCacheException;

/**
 * Class File
 * @package Slimore\Cache
 */

class File
{
    /**
     * @var array
     */

    public static $keys = [];

    /**
     * @var int
     */
    public $expireTime = 3600;

    /**
     * @var string
     */
    protected $salt = 'slimore';
    /**
     * @var string
     */
    public $cachePath = '.';

    /**
     * @var string
     */
    public $cacheDirectory = '.caches';

    /**
     * @var string
     */
    public $fileExtension = '.php';

    /**
     * @var bool
     */
    public $base64Encode = true;

    /**
     * Constructor
     */

    public function __construct()
    {

    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Md5 encrypt cache filename
     *
     * @param string $key
     * @return string
     */

    protected function encrypt($key)
    {
        return md5(md5($key) . $this->salt);
    }

    /**
     * Get cache file path
     *
     * @return string
     */

    protected function getFilePath()
    {
        $path = $this->cachePath . DIRECTORY_SEPARATOR . $this->cacheDirectory . DIRECTORY_SEPARATOR;
        $path = str_replace(['\\\\', '//'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);

        return $path;
    }

    /**
     * Get cache filename
     *
     * @param string $key
     * @return string
     */

    protected function getFileName($key)
    {
        $file = $this->getFilePath() . $this->encrypt($key) . $this->fileExtension;

        return $file;
    }

    /**
     * Set cache key and write to cache file
     *
     * @param string $key
     * @param string $value
     * @param int $expireTime null
     * @return bool
     * @throws InvalidArgumentException
     * @throws FileCacheException
     */

    public function set($key, $value, $expireTime = null)
    {
        if ( $expireTime && !is_int($expireTime) )
        {
            throw new \InvalidArgumentException('cache expire time must be integer.');
        }

        $this->expireTime = ($expireTime) ? $expireTime : $this->expireTime;

        return $this->write($key, $value);
    }

    /**
     * Get cache key value
     *
     * @param $key
     * @return mixed|null
     */

    public function get($key)
    {
        return $this->read($key);
    }

    /**
     * Write to cache file
     *
     * @param string $file
     * @param string $value
     * @return bool
     * @throws FileCacheException
     */

    protected function write($file, $value)
    {
        $key  = $file;
        $path = $this->getFilePath();

        if ( !file_exists($dir) )
        {
            mkdir($path, 0777, true);
        }

        $file = $this->getFileName($key);

        $value = serialize($value);
        $value = ($this->base64Encode) ? base64_encode($value) : $value;

        if ( !file_put_contents($file, $value) )
        {
            throw new FileCacheException('File write failure.');
        }

        if ( !chmod($file, 0777) )
        {
            throw new FileCacheException('Failed to set file permissions.');
        }

        if ( !touch($file, time() + $this->expireTime) )
        {
            throw new FileCacheException('Failed to change file time.');
        }

        static::$keys[$key] = true;

        return true;
    }

    /**
     * Read cache file
     *
     * @param string $file
     * @return mixed|null
     */

    protected function read($file)
    {
        $data = null;
        $key  = $file;
        $file = $this->getFileName($file);

        if ( file_exists($file))
        {
            // if cache file not expire
            if (filemtime($file) >= time())
            {
                echo "read cache<br/>";
                $data = file_get_contents($file);
                $data = ($this->base64Encode) ? base64_decode($data) : $data;
                $data = unserialize($data);
                static::$keys[$key] = true;
            }
            else
            {
                unset(static::$keys[$key]);
                unlink($file);
            }
        }

        return $data;
    }

    /**
     * Setter
     *
     * @param string $key
     * @param string $value
     * @return bool
     */

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Getter
     *
     * @param $key
     * @return mixed|null
     */

    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Check has property key, when using isset() or empty()
     *
     * @param string $key
     * @return bool
     */

    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Check has cache key
     *
     * @param string $key
     * @return bool
     */

    public function has($key)
    {
        $file = $this->getFileName($key);

        if ( !file_exists($file) )
        {
            return false;
        }

        // if cache file expire
        if ( filemtime($file) >= time() )
        {
            static::$keys[$key] = true;

            return true;
        }
        else
        {
            unset(static::$keys[$key]);
            unlink($file);

            return false;
        }
    }

    /**
     * Delete property key, when using unset()
     *
     * @param string $key
     * @return bool
     */

    public function __unset($key)
    {
        return $this->remove($key);
    }

    /**
     * Remove cache key
     *
     * @param string $key
     * @return bool
     */

    public function remove($key)
    {
        $file = $this->getFileName($key);

        if ( !file_exists($file) )
        {
            return false;
        }

        unset(static::$keys[$key]);

        return (unlink($file))? true : false;
    }

    /**
     * Clear expire cache file
     *
     * @return void
     */

    public function clear()
    {
        echo $path  = $this->getFilePath();
        $files = glob($path . '*' . $this->fileExtension);

        foreach ($files as $file)
        {
            // if cache file expire, delete cache file
            if ( time() > filemtime($file) )
            {
                @unlink($file);
            }
        }
    }

    /**
     * Delete all cache files
     *
     * @return void
     */

    public function clearAll()
    {
        $path  = $this->getFilePath();
        $files = glob($path . '*' . $this->fileExtension);

        foreach ($files as $file)
        {
            @unlink($file);
        }

        static::$keys = [];
    }
}