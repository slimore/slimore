<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore
 */

/**
 * Make the route handle string for autoload (short name)
 *
 * @param  string $controller index
 * @param  string $action index
 * @param  string $namespace
 * @return string
 */

function ctl($controller = 'index', $action = 'index', $namespace = '')
{
    if ($namespace != '')
    {
        $namespace = ucwords($namespace);
    }

    $ctl = $namespace . ucwords($controller . 'Controller') . ':' . $action;

    return $ctl;
}

/**
 * ctl() Alias
 *
 * @param  string $controller index
 * @param  string $action index
 * @param  string $namespace
 * @return string
 */

function controller($controller = 'index', $action = 'index', $namespace = '')
{
    return ctl($controller, $action, $namespace);
}

/**
 * Create password, Using double md5 encode
 *
 * @param string $str
 * @param string $salt
 * @return string
 */

function password($str, $salt)
{
    return md5(md5($str . $salt) . $salt);
}

/**
 * Encrypt code
 *
 * @param string|number $string
 * @param string|number key
 * @return string
 */

function encrypt($string, $key)
{
    $encrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(md5($key)), $string, MCRYPT_MODE_CBC, md5($key));

    return base64_encode($encrypt);
}

/**
 * Decrypt code
 *
 * @param string|number $string
 * @param string|number $key
 * @return string
 */

function decrypt($string, $key)
{
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(md5($key)), base64_decode($string), MCRYPT_MODE_CBC, md5($key));
}

/**
 * Create image data url from file
 *
 * @param string $file
 * @return string
 */

function imageDataUrl($file)
{
    if (!file_exists($file))
    {
        throw new \InvalidArgumentException('File ' . $file . ' not found.');
    }

    $dataUrl = 'data:' . mime_content_type($file) . ';base64,' . base64_encode(file_get_contents($file));

    return $dataUrl;
}

/**
 * Mask hidden phone number middle 5 chars
 *
 * @param string $phone
 * @param string $mask
 * @return string
 */

function phoneMaskCode($phone, $mask = '*****')
{
    $phone = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}' . $mask . '${2}', $phone);

    return $phone;
}

/**
 * Generate random characters
 *
 * Supported chinese characters
 *
 * @param int $length
 * @param string $characters null
 * @example
        echo randomCharacters(6);
        echo randomCharacters(10);
        echo randomCharacters(3, '中文随机生成的汉字');
 *
 * @return string
 */

function randomCharacters($length, $characters = null)
{
    if ( !is_int($length) )
    {
        throw new \InvalidArgumentException("random characters length must be integer.");
    }

    $characters = ($characters) ? $characters : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
    $random = '';

    if ( function_exists('mb_substr') )
    {
        $len = mb_strlen($characters, 'utf-8');

        for ($i = 0; $i < $length; $i++)
        {
            $random .= mb_substr($characters, mt_rand(0, $len - 1), 1, 'utf-8');
        }
    }

    return $random;
}

/**
 * File format size
 *
 * @param string|number $size
 * @param int $decimals
 * @param bool $blankSpace true
 * @return string
 */

function fileFormatSize($size, $decimals = 2, $blankSpace = true)
{
    $units = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    for($i = 0; $size >= 1024 && $i <= count($units); $i++)
    {
        $size = $size / 1024;
    }

    return round($size, $decimals) . (($blankSpace) ? ' ' : '') . $units[$i];
}

/**
 * Judge whether JSON data
 *
 * @param string $json
 * @return bool
 */

function isJson($json)
{
    return (json_decode($json)) ? true : false;
}

/**
 * Using DIRECTORY_SEPARATOR replace directory path
 *
 * @param string $dir
 * @return mixed
 */

function replaceDirSeparator($dir)
{
    $dir = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $dir);

    return $dir;
};

/**
 * Get directory all dirs & files
 *
 * @param $path
 * @param bool $onlyFiles false
 * @return array
 */

function getDirectoryItems($path, $onlyFiles = false)
{
    $items = glob($path . '/*');

    for ($i = 0, $count = count($items); $i < $count; $i++)
    {
        if ( is_dir($items[$i]) )
        {
            $push  = glob($items[$i] . '/*');
            $items = array_merge($items, $push);
        }
    }

    if ( $onlyFiles )
    {
        $files = [];

        for ($i = 0, $count = count($items); $i < $count; $i++)
        {
            if (!is_dir($items[$i]))
            {
                $files[] = realpath($items[$i]);
            }
        }

        return $files;
    }

    $items = array_map('realpath', $items);

    return $items;
}

/**
 * Get current full url
 *
 * @return string
 */

function currentUrl()
{
    $port = ($_SERVER["SERVER_PORT"] != '80') ? ':' . $_SERVER["SERVER_PORT"] : '';

    $url  = 'http' . ( !empty($_SERVER['HTTPS']) ? 's' : '') . '://';
    $url .= $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];

    return $url;
}

/**
 * Json object to array
 *
 * @param object $json
 * @return array
 */

function jsonToArray($json)
{
    $array = [];

    foreach ($json as $key => $val)
    {
        $array[$key] = (is_object($val)) ? jsonToArray($val): $val;
    }

    return $array;
}

/**
 * Array to Object
 *
 * @param array $array
 * @return ArrayObject
 */

function arrayToObject(array $array)
{
    // or json_decode(json_encode($array), FALSE);
    // or (object) $array;
    return new ArrayObject($array);
}

/**
 * Generate short url
 *
 * @param string $url
 * @return string
 */

function shortUrl($url)
{
    $code     = sprintf('%u', crc32($url));
    $shortUrl = '';

    while ($code)
    {
        $mod = $code % 62;

        if ($mod > 9 && $mod <= 35)
        {
            $mod = chr($mod + 55);
        }
        elseif ($mod > 35)
        {
            $mod = chr($mod + 61);
        }

        $shortUrl .= $mod;
        $code = floor($code / 62);
    }

    return $shortUrl;
}

/**
 * Generate HTML(5) template
 *
 * @param string $title
 * @param string|array $head
 * @param string|array $body
 * @param string $charset
 * @param string $lang
 * @return string
 */

function html($title, $head = '', $body = '', $charset = 'utf-8', $lang = 'zh')
{
    $html  = [
        '<!DOCTYPE html>',
        '<html lang="' . $lang . '">',
        '<head>',
        '<mate charset="' . $charset .'">',
        '<title>' . $title . '</title>',
        (is_array($head)) ? implode("\n", $head) : $head,
        '</head>',
        '<body>',
        (is_array($body)) ? implode("\n", $body) : $body,
        '</body>',
        '</html>'
    ];

    return implode("\n", $html);
}

/**
 * Detect mobile client
 *
 * @return int
 */

function isMobile()
{
    $regex = '/(android|symbian|mobile|smartphone|iemobile|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|midp|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos|ios|iphone|ipod|huawei|htc|haier|lenovo)/i';

    return preg_match($regex, strtolower($_SERVER["HTTP_USER_AGENT"]));
}

/**
 * Detect tablet client
 *
 * @return int
 */

function isTablet()
{
    $regex = '/(tablet|ipad|playbook|kindle)|(android(?!.*(mobi|opera mini)))/i';

    return preg_match($regex, strtolower($_SERVER['HTTP_USER_AGENT']) );
}

/**
 * Detect mobile device
 *
 * @param string $device iPhone,iPod,iPad,Android,webOS ...
 * @return int
 */

function detectDevice($device = 'iPhone')
{
    return (stripos($_SERVER['HTTP_USER_AGENT'], $device) !== false);
}

/**
 * Detect iPhone
 *
 * @return int
 */

function iPhone()
{
    return detectDevice('iPhone');
}

/**
 * Detect iPad
 *
 * @return int
 */

function iPad()
{
    return detectDevice('iPad');
}

/**
 * Detect iPod
 *
 * @return int
 */

function iPod()
{
    return detectDevice('iPod');
}

/**
 * Detect Android device
 *
 * @return int
 */

function isAndroid()
{
    return detectDevice('Android');
}

/**
 * Detect WebOS device
 *
 * @return int
 */

function isWebOS()
{
    return detectDevice('webOS');
}

/**
 * Detect Blackberry device
 *
 * @return int
 */

function isBlackberry()
{
    return detectDevice('Blackberry');
}

/**
 * Detect Windows
 *
 * @return bool
 */

function isWindows()
{
    return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

/**
 * Detect Mac OS X
 *
 * @return bool
 */
function isMacOSX()
{
    return (PHP_OS === 'Darwin');
}

/**
 * Detect Linux
 *
 * @return bool
 */

function isLinux()
{
    return (PHP_OS === 'Linux');
}

/**
 * Detect FreeBSD OS
 *
 * @return bool
 */

function isFreeBSD()
{
    return (PHP_OS === 'FreeBSD');
}

/**
 * Detect Unix
 *
 * @return bool
 */

function isUnix()
{
    return (PHP_OS === 'Unix');
}

/**
 * Detect Uinx / Linux / Mac OS ... Unix-likes
 *
 * @return bool
 */

function isUnixLike()
{
    return (DIRECTORY_SEPARATOR == '/');
}

/**
 * Detect Apache server
 *
 * @return bool
 */

function isApache()
{
    return (stripos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || function_exists('apache_get_version'));
}

/**
 * Detect Nginx server
 *
 * @return bool
 */

function isNginx()
{
    return (stripos($_SERVER["SERVER_SOFTWARE"], 'nginx') > -1);
}

/**
 * Detect IIS server
 *
 * @return bool
 */

function isIIS()
{
    return (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false);
}

/**
 * Check CLI env
 * @return string
 */

function isCli()
{
    return strtoupper(substr(PHP_SAPI_NAME(), 0, 3) === 'CLI');
}

/**
 * Detect IE browser
 *
 * @return bool
 */

function isIE()
{
    $isIE = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false );

    return ( $isIE && strpos($_SERVER['HTTP_USER_AGENT'], 'Win') !== false);
}

/**
 * Detect Firefox browser
 *
 * @return int
 */

function isFirefox()
{
    return detectDevice('Firefox');
}

/**
 * Detect Google Chrome browser
 * @return int
 */

function isChrome()
{
    return detectDevice('Chrome');
}

/**
 * Detect Safari browser
 *
 * @return int
 */

function isSafari()
{
    return detectDevice('Safari');
}

/**
 * Detect Opera browser
 *
 * @return int
 */

function isOpera()
{
    return detectDevice('Opera');
}

/**
 * Get device independent pixels (Dpi)
 *
 * @param int $width
 * @param int $height
 * @param number $inch
 * @return float
 */

function getDpi($width, $height, $inch)
{
    if (!is_int($width) || !is_int($height))
    {
        throw new \InvalidArgumentException('width && height must be integer');
    }

    if (!is_numeric($inch))
    {
        throw new \InvalidArgumentException('device inch must be number');
    }

    return ceil(sqrt(pow($width,2) + pow($height, 2)) / $inch);
}

/**
 * Using Javascript, Generate script tag
 *
 * @param string $script
 * @param bool $wrap false
 * @param string $type type="text/javascript"
 * @return string
 */

function js($script, $wrap = false, $type = ' type="text/javascript"')
{
    $wrap   = ($wrap) ? "\n" : '';
    $script = (is_array($script)) ? implode($wrap, $script) : $script;
    $script = $wrap . '<script' . $type . '>' . $wrap . $script . $wrap . '</script>' . $wrap;

    return $script;
}

/**
 * js() alias
 *
 * @param $script
 * @param bool $wrap
 * @return string
 */

function script($script, $wrap = false)
{
    return js($script, $wrap);
}

/**
 * Generate style tag
 *
 * @param string $style
 * @param bool $wrap
 * @return string
 */

function style($style, $wrap = false)
{
    $wrap  = ($wrap) ? "\n" : '';
    $style = ( (is_array($style)) ? implode($wrap, $style) : $style);
    $style = $wrap . '<style type="text/css">' . $wrap . $style . $wrap . '</style>' . $wrap;

    return $style;
}

/**
 * Like javascript console.xxx()
 *
 * @param string $message
 * @param string $type
 * @param bool $wrap
 * @return string
 */

function console($message, $type = 'log', $wrap = false)
{
    $wrap  = ($wrap) ? "\n" : '';

    return $wrap . '<script type="text/javascript">console.' . $type . '("' . $message . '");</script>' . $wrap;
}

/**
 * Generate link tag
 *
 * @param string $href
 * @param string $text
 * @param string $attrs
 * @param bool $wrap
 * @return string
 */

function linkTag($href, $text, $attrs = '', $wrap = false)
{
    $wrap  = ($wrap) ? "\n" : '';
    $attrs = ($attrs !== '') ? ' ' . $attrs : $attrs;
    $link  = $wrap . '<a href="'.$href.'"' . $attrs . '>' . $text . '</a>' . $wrap;

    return $link;
}

/**
 * Hex color To RGB color
 *
 * @param string $hex
 * @example
 *      hexToRGB('ff9900');
 *      hexToRGB('#ff9900');
 *
 * @return array
 */

function hexToRGB($hex)
{
    $hex       = str_replace('#', '', $hex);
    $shorthand = (strlen($hex) == 4);

    list($red, $green, $blue) = array_map('hexdec', str_split($hex, $shorthand ? 1 : 2));

    return [
        'red'   => $red,
        'green' => $green,
        'blue'  => $blue
    ];
}

/**
 * RGB to Hex
 *
 * @param array $rgb
 * @example
 *      rgbToHex([233,195,65]);
 *      rgbToHex([255, 0, 0]);
 *      rgbToHex([255, 255, 255]);
 * @return string
 */

function rgbToHex(array $rgb)
{
    $rgb = array_map(function($i) {
        $i = dechex($i);
        return (strlen($i) < 2) ? '0' . $i : $i;
    }, $rgb);

    $rgb = '#' . implode('', $rgb);

    return $rgb;
}