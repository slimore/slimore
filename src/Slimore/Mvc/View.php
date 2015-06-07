<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/slimore/slimore
 * @license     MIT License https://github.com/slimore/slimore#license
 * @version     0.1.0
 * @package     Slimore\Mvc
 */

namespace Slimore\Mvc;

/**
 * Class View
 *
 * @author Pandao
 * @package Slimore\Mvc
 */

class View extends \Slim\View
{
    /**
     * @var string
     */
    public    $compileFileSuffix = '.php';

    /**
     * @var bool
     */
    public    $compileFileNameMd5 = true;

    /**
     * @var string
     */
    public    $compileDirectoryName = '.compiles';

    /**
     * @var string
     */
    public    $cacheDirectoryName = '.caches';

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $templateFile;

    /**
     * @var string
     */
    protected $compilePath;

    /**
     * @var bool
     */
    public $compileCached = true;

    /**
     * @var string
     */
    protected $templateCompileFile;

    /**
     * Global vars for insert view
     *
     * @var array
     */
    protected static $globals    = [];

    /**
     * @var array
     */
    protected static $properties = [];

    /**
     * Set/Create template compile directory
     *
     * @return void
     */

    public function setCompileDirectory()
    {
        $compilePath = $this->getTemplatesDirectory() . DIRECTORY_SEPARATOR . $this->compileDirectoryName . DIRECTORY_SEPARATOR;

        if (!file_exists($compilePath))
        {
            static::mkdir($compilePath);
        }

        $this->compilePath = $compilePath;
    }

    /**
     * Set template compile file path
     *
     * @param string $filename
     * @return void
     */

    public function setCompileFile($filename)
    {
        $filename = (($this->compileFileNameMd5) ? md5(md5($filename)) : $filename);
        $this->templateCompileFile = $this->compilePath . $filename . $this->compileFileSuffix;
    }

    public static function getProperties(array $array)
    {
        return static::$properties = $array;
    }

    /**
     * Template render
     *
     * @param string $template
     * @param array $data
     * @return mixed
     */

    public function render($template, $data = null)
    {
        $this->templateName = $template;
        $this->templateFile = $this->getTemplatePathname($template);

        if (!is_file($this->templateFile))
        {
            throw new \RuntimeException("Slimore\\Mvc\\View cannot render `$template` because the template does not exist");
        }

        $this->setCompileDirectory();
        $this->setCompileFile($template);

        ob_start();

        $data = array_merge($this->data->all(), (array) $data);
        $data = array_merge(static::$globals, $data);

        static::getProperties([
            'data'               => $data,
            'compileCached'      => $this->compileCached,
            'templatePath'       => $this->getTemplatesDirectory() . DIRECTORY_SEPARATOR,
            'compilePath'        => $this->compilePath,
            'compileFileNameMd5' => $this->compileFileNameMd5,
            'compileFileSuffix'  => $this->compileFileSuffix
        ]);

        extract($data);

        //echo "this->compileCached =>" . ($this->compileCached ? 'true' : 'false');

        if (!$this->compileCached)
        {
            $tpl = $this->read($this->templateFile);
            $tpl = static::parser($tpl);
            $this->write($this->templateCompileFile, $tpl);
        }
        else
        {
            if (!file_exists($this->templateCompileFile) ||
                (filemtime($this->templateFile) > filemtime($this->templateCompileFile))
            ) {
                $tpl = $this->read($this->templateFile);
                $tpl = static::parser($tpl);
                $this->write($this->templateCompileFile, $tpl);
            }
        }

        require $this->templateCompileFile;

        echo ob_get_clean();
    }

    /**
     * Create template compile directory
     *
     * @param string $dir
     * @return string
     */

    public static function mkdir($dir)
    {
        if ( file_exists($dir) )
        {
            return $dir;
        }

        mkdir($dir, 0777, true);
        chmod($dir, 0777);

        return $dir;
    }

    /**
     * Read template file
     *
     * @param string $filename
     * @return string
     */

    protected function read($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Write template compile file
     *
     * @param string $filename
     * @param string $content
     */

    protected function write($filename, $content)
    {
        file_put_contents($filename, $content);
    }

    /**
     * Parse & require included template file
     *
     * @param string $file
     * @return string
     */

    public static function includeFile($file)
    {
        $data               = static::$properties['data'];
        $templatePath       = static::$properties['templatePath'];
        $compilePath        = static::$properties['compilePath'];
        $compileCached      = static::$properties['compileCached'];
        $compileFileNameMd5 = static::$properties['compileFileNameMd5'];
        $compileFileSuffix  = static::$properties['compileFileSuffix'];
        $templateFile       = $templatePath . $file;

        if ( !file_exists($templateFile) )
        {
            throw new \InvalidArgumentException('included template file ' . $file . ' not found.');
        }

        $pathInfo = pathinfo($file);
        $filename = $pathInfo['filename'];

        if (!$compileFileNameMd5 && !file_exists($compilePath . $pathInfo['dirname']))
        {
            static::mkdir($compilePath . $pathInfo['dirname']);
        }

        $filenameMd5 = (($compileFileNameMd5) ? md5(md5($filename)) : $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $filename);
        $compileFile = $compilePath . $filenameMd5 . $compileFileSuffix;

        if ($compileCached)
        {
            if (!file_exists($compileFile) || (filemtime($templateFile) > filemtime($compileFile)))
            {
                $tpl = file_get_contents($templateFile);
                $tpl = static::parser($tpl);
                file_put_contents($compileFile, $tpl);
            }
        }
        else
        {
            $tpl = file_get_contents($templateFile);
            $tpl = static::parser($tpl);
            file_put_contents($compileFile, $tpl);
        }

        return $compileFile;
    }

    /**
     * Template parser
     *
     * @param string $tpl
     * @return string
     */

    public static function parser($tpl)
    {
        $viewClass = '\Slimore\Mvc\View::';

        // Parse for in
        $tpl = preg_replace_callback('/\<\!\-\-\s*\{for\s+(\S+)\s+in\s+(\S+)\}\s*\-\-\>/is', function($matchs) {
            //print_r($matchs);
            $output = '<?php if (isset(' . $matchs[2] . ') && is_array(' . $matchs[2] . ')) { ?>';
            $output .= "\r";
            $output .= '<?php foreach (' . $matchs[2] . ' as $key => ' . $matchs[1] . ') { ?>';
            $output .= "\r";

            return $output;
        }, $tpl);

        // Parse foreach
        $tpl = preg_replace_callback('/\<\!\-\-\s*\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}\s*\-\-\>/is', function($matchs) {
            print_r($matchs);
            $output = '<?php if (isset(' . $matchs[1] . ') && is_array(' . $matchs[1] . ')) { ?>';
            $output .= "\r";
            $output .= '<?php foreach (' . $matchs[1] . ' as ' . $matchs[2] . ' => ' . $matchs[3] . ') { ?>';
            $output .= "\r";

            return $output;
        }, $tpl);

        $regexs = [
            '/\<\!\-\-\s*\{if\s+(\S+)\}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{elseif\s+(\S+)\}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{else}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{\/if}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{\/for}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{\/foreach}\s*\-\-\>/is',
            '/\<\!\-\-\s*\{(.+?)\}\s*\-\-\>/is',
            '/\{include\s+(.+?)\}/is',
            '/\{([A-Z][A-Z0-9_]*)\}/s',
            '/\{(\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/is'
        ];

        $replacements = [
            '<?php if (\\1) { ?>',
            '<?php } elseif (\\1) { ?>',
            '<?php } else { ?>',
            '<?php } ?>',
            '<?php } } ?>',
            '<?php } } ?>',
            '{\\1}',
            '<?php include ' . $viewClass . 'includeFile(\\1); ?>',
            '<?php if ( defined(\'\\1\') ) { echo \\1; } ?>',
            '<?php if ( isset(\\1) && !is_array(\\1)) { echo \\1; } ?>'
        ];

        $tpl = preg_replace($regexs, $replacements, $tpl);

        // Parse functions
        $tpl = preg_replace('/\{(([@&\\\$a-zA-Z0-9_]+)\(([^\}]*)\))?\}/is', '<?php echo \\1; ?>', $tpl);

        // Parse ternary
        $tpl = preg_replace_callback('/\{(([^\}]*)\s+\?\s+([^\}]*)\s+:\s+([^\}]*))?\}/is', function($matchs) {
            //print_r($matchs);
            $output = '<?php echo ' . $matchs[1] . '; ?>';

            return str_replace(';; ?>', '; ?>', $output);
        }, $tpl);

        // Parse Objects
        $tpl = preg_replace_callback('/\{(([\$\\\w+]+)([:-\>]*)([^\}]*))?\}/is', function($matchs) {
            //print_r($matchs);
            $output = '<?php echo ' . $matchs[1] . '; ?>';

            return str_replace(';; ?>', '; ?>', $output);
        }, $tpl);

        // Parse line comment
        $tpl = preg_replace('/\{(\/\/([^\}]*))?\}/is', '<?php \\1; ?>', $tpl);

        return $tpl;
    }

    /**
     * Insert global variables
     *
     * @param array $array
     * @return void
     */

    public static function addGlobals(array $array)
    {
        static::$globals = array_merge($array, static::$globals);
    }
}