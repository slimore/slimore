<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Image
 */

namespace Slimore\Image;

/**
 * Class Gd
 *
 * @author  Pandao
 * @package Slimore\Image
 */

class Gd
{
    /**
     * Thumbnail modes
     */
    const     THUMB_EQUAL_RATIO   = 0;
    const     THUMB_CENTER_CENTER = 1;
    const     THUMB_LEFT_TOP      = 2;

    /**
     * Crop positions
     */
    const     CROP_TOP_LEFT      = 'tl';
    const     CROP_TOP_CENTER    = 'tc';
    const     CROP_TOP_RIGHT     = 'tr';
    const     CROP_CENTER_LEFT   = 'cl';
    const     CROP_CENTER_CENTER = 'center';
    const     CROP_CENTER_RIGHT  = 'cr';
    const     CROP_BOTTOM_LEFT   = 'bl';
    const     CROP_BOTTOM_CENTER = 'bc';
    const     CROP_BOTTOM_RIGHT  = 'br';

    /**
     * Watermark positions
     */
    const     POS_TOP_LEFT      = 'tl';
    const     POS_TOP_CENTER    = 'tc';
    const     POS_TOP_RIGHT     = 'tr';
    const     POS_CENTER_LEFT   = 'cl';
    const     POS_CENTER_CENTER = 'center';
    const     POS_CENTER_RIGHT  = 'cr';
    const     POS_BOTTOM_LEFT   = 'bl';
    const     POS_BOTTOM_CENTER = 'bc';
    const     POS_BOTTOM_RIGHT  = 'br';
    const     POS_RAND          = 0;
    const     POS_DEFAULT       = 0;

    /**
     * @var string
     */
    public    $source;

    /**
     * @var string
     */
    protected $fontFile;

    /**
     * @var resource
     */
    protected $newImage;

    /**
     * @var resource
     */
    protected $sourceImage;

    /**
     * @var array
     */
    protected $types = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp'];

    /**
     * Constructor
     *
     * @param string $src
     */

    public function __construct($src = '')
    {
        if (! extension_loaded ( 'gd' )) {
            throw new \RuntimeException('GD2 extension not loaded.');
        }

        if ( !empty($src) && file_exists($src))
        {
            $this->source($src);
        }
    }

    /**
     * Get GD information
     *
     * @param string $key null
     * @return array|string|bool
     */

    public function getGDInfo($key = null)
    {
        $gdInfo = gd_info();

        return ($key) ? $gdInfo[$key] : $gdInfo;
    }

    /**
     * Get image info array
     *
     * @param string $image
     * @return array
     */

    public function getImageInfo($image)
    {
        if ( empty($image) || !file_exists($image))
        {
            throw new \InvalidArgumentException('Image file not found.');
        }

        $pathInfo = pathinfo($image);
        $info     = getimagesize($image);

        if ( !in_array($pathInfo['extension'], $this->types) )
        {
            throw new \InvalidArgumentException('Unsupported image file extension.');
        }

        $info['width']    = $info[0];
        $info['height']   = $info[1];
        $info['ext']      = $info['type'] = $pathInfo['extension'];
        $info['size']     = filesize($image);
        $info['dir']      = $pathInfo['dirname'];
        $info['path']     = str_replace('/', DIRECTORY_SEPARATOR, $image);
        $info['fullname'] = $pathInfo['basename'];
        $info['filename'] = $pathInfo['filename'];
        $info['type']     = ($info['type'] == 'jpg') ? 'jpeg' : $info['type'];

        return $info;
    }

    /**
     * Get image file extension
     *
     * @return string
     */

    public function ext()
    {
        return $this->source['ext'];
    }

    /**
     * Get image width
     *
     * @return number
     */

    public function width()
    {
        return $this->source['width'];
    }

    /**
     * Get image height
     *
     * @return number
     */

    public function height()
    {
        return $this->source['height'];
    }

    /**
     * Get image file size
     *
     * @return number
     */

    public function fileSize()
    {
        return $this->source['size'];
    }

    /**
     * Get image type for imagecreatefromxxx
     *
     * @param string $type
     * @return string
     */

    public function getType($type)
    {
        return (!$type) ? $this->source['type'] : (($type === 'jpg') ? 'jpeg' : $type);
    }

    /**
     * Set local image source
     *
     * @param string $src
     * @return $this
     */

    public function source($src)
    {
        $this->source = $this->getImageInfo($src);

        $type              = $this->source['type'];
        $createFrom        = 'ImageCreateFrom' . $type;
        $this->sourceImage = $createFrom($src);

        return $this;
    }

    /**
     * Create new canvas image
     * @param int $width
     * @param int $height
     * @param string $type null
     * @return $this
     */

    public function create($width, $height, $type = null)
    {
        if ( !is_numeric($width) ) {
            throw new \InvalidArgumentException('Image create failed, width must be numeric');
        }

        if ( !is_numeric($height) ) {
            throw new \InvalidArgumentException('Image create failed, height must be numeric');
        }

        $type = $this->getType($type);

        if ($type !== 'gif' && function_exists('imagecreatetruecolor'))
        {
            $newImage = imagecreatetruecolor($width, $height); // Unsupport gif file
        }
        else
        {
            $newImage = imagecreate($width, $height);
        }

        imagealphablending($newImage, true);

        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 0);

        imagefilledrectangle($newImage, 0, 0, imagesx($newImage), imagesy($newImage), $transparent);
        imagefill($newImage, 0, 0, $transparent);

        imagesavealpha($newImage, true);

        $this->newImage = $newImage;

        return $this;
    }

    /**
     * Create image from type
     *
     * @param string $image
     * @return mixed
     */

    public function createFrom($image)
    {
        $type           = pathinfo($image, PATHINFO_EXTENSION);
        $type           = ($type === 'jpg') ? 'jpeg' : $type;
        $createFrom     = 'ImageCreateFrom' . $type;

        return $createFrom($image);
    }

    /**
     * Crop image file
     *
     * @param int $width
     * @param int $height
     * @param string $mode tl
     * @return $this
     */

    public function crop($width, $height, $mode = 'tl')
    {
        if ( !is_numeric($width) ) {
            throw new \InvalidArgumentException('$width must be numeric');
        }

        if ( !is_numeric($height) ) {
            throw new \InvalidArgumentException('$height must be numeric');
        }

        if ( $this->newImage )
        {
            $this->sourceImage = $this->newImage;
        }

        $oldWidth   = ($this->newImage) ? imagesx($this->newImage) : $this->source['width'];
        $oldHeight  = ($this->newImage) ? imagesy($this->newImage) : $this->source['height'];

        $this->create($width, $height);

        $startX     = $startY = 0;
        $cropWidth  = $sourceWidth  = $width;
        $cropHeight = $sourceHeight = $height;

        if ( is_array($mode) )
        {
            $startX = $mode[0];
            $startY = $mode[1];
        }

        if ($mode === self::CROP_TOP_CENTER)
        {
            $startX = ($oldWidth - $cropWidth) / 2;
        }
        else if ($mode === self::CROP_TOP_RIGHT)
        {
            $startX = $oldWidth - $cropWidth;
        }
        else if ($mode === self::CROP_CENTER_LEFT)
        {
            $startY = ($oldHeight - $cropHeight) / 2;
        }
        else if ($mode === self::CROP_CENTER_CENTER)
        {
            $startX = ($oldWidth - $cropWidth)   / 2;
            $startY = ($oldHeight - $cropHeight) / 2;
        }
        else if ($mode === self::CROP_CENTER_RIGHT)
        {
            $startX = $oldWidth - $cropWidth;
            $startY = ($oldHeight - $cropHeight) / 2;
        }
        else if ($mode === self::CROP_BOTTOM_LEFT)
        {
            $startY = $oldHeight - $cropHeight;
        }
        else if ($mode === self::CROP_BOTTOM_CENTER)
        {
            $startX = ($oldWidth - $cropWidth)   / 2;
            $startY = $oldHeight - $cropHeight;
        }
        else if ($mode === self::CROP_BOTTOM_RIGHT)
        {
            $startX = $oldWidth - $cropWidth;
            $startY = $oldHeight - $cropHeight;
        }
        else
        {
        }

        imagecopyresampled(
            $this->newImage,
            $this->sourceImage,
            0, 0, $startX, $startY,
            $cropWidth, $cropHeight, $sourceWidth, $sourceHeight);

        return $this;
    }

    /**
     * Image thumbnail
     *
     * @param int $width
     * @param int $height
     * @param int $mode 0
     * @param bool $amplify false
     * @return $this|bool
     */

    public function thumb($width, $height, $mode = 0, $amplify = false)
    {
        if ( !is_numeric($width) ) {
            throw new \InvalidArgumentException('$width must be numeric');
        }

        if ( !is_numeric($height) ) {
            throw new \InvalidArgumentException('$height must be numeric');
        }

        if ( $this->newImage )
        {
            $this->sourceImage = $this->newImage;
        }

        $oldWidth   = ($this->newImage) ? imagesx($this->newImage) : $this->source['width'];
        $oldHeight  = ($this->newImage) ? imagesy($this->newImage) : $this->source['height'];

        $this->create($width, $height);

        if ($oldWidth < $width && $oldHeight < $height && !$amplify)
        {
            return false;
        }

        $thumbWidth  = $width;
        $thumbHeight = $height;
        $startX      = $startY = 0;

        if ($mode === self::THUMB_EQUAL_RATIO)
        {
            $scale        = min($width / $oldWidth, $height / $oldHeight);
            $thumbWidth   = (int) ($oldWidth  * $scale);
            $thumbHeight  = (int) ($oldHeight * $scale);
            $sourceWidth  = $oldWidth;
            $sourceHeight = $oldHeight;
        }
        else if ($mode === self::THUMB_CENTER_CENTER)
        {
            $scale1 = round($width    / $height,    2);
            $scale2 = round($oldWidth / $oldHeight, 2);

            if ($scale1 > $scale2)
            {
                $sourceWidth  = $oldWidth;
                $sourceHeight = round($oldWidth / $scale1, 2);
                $startY       = ($oldHeight - $sourceHeight) / 2;
            }
            else
            {
                $sourceWidth  = round($oldHeight * $scale1, 2);
                $sourceHeight = $oldHeight;
                $startX       = ($oldWidth - $sourceWidth) / 2;
            }
        }
        else if ($mode === self::THUMB_LEFT_TOP)
        {
            $scale1 = round($width    / $height,    2);
            $scale2 = round($oldWidth / $oldHeight, 2);

            if ($scale1 > $scale2)
            {
                $sourceHeight = round($oldWidth / $scale1, 2);
                $sourceWidth  = $oldWidth;
            }
            else
            {
                $sourceWidth  = round($oldHeight * $scale1, 2);
                $sourceHeight = $oldHeight;
            }
        }

        imagecopyresampled(
            $this->newImage,
            $this->sourceImage,
            0, 0, $startX, $startY,
            $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);

        return $this;
    }

    /**
     * Image resize
     *
     * @param int $width
     * @param int $height
     * @param int $x 0
     * @param int $y 0
     * @return $this
     */

    public function resize($width, $height, $x = 0, $y = 0)
    {
        if ( !$this->newImage )
        {
            $this->create($width, $height);
        }

        if ( !is_numeric($width) ) {
            throw new \InvalidArgumentException('$width must be numeric');
        }

        if ( !is_numeric($height) ) {
            throw new \InvalidArgumentException('$height must be numeric');
        }

        $type = $this->source['type'];

        imagecopyresampled(
            $this->newImage,
            $this->sourceImage,
            0, 0,
            $x, $y,
            $width, $height,
            $this->source['width'], $this->source['height']);

        return $this;
    }

    /**
     * Image resize by percent
     *
     * @param int $percent 50
     * @return $this
     */

    public function resizePercent($percent = 50)
    {
        if ( $percent < 1)
        {
            throw new \InvalidArgumentException('percent must be >= 1');
        }

        $this->resize($this->source['width'] * ($percent / 100), $this->source['height'] * ($percent / 100));

        return $this;
    }

    /**
     * Image watermark
     *
     * @param string $water
     * @param int $pos 0
     * @param bool $tile false
     * @return $this
     */

    public function watermark($water, $pos = 0, $tile = false)
    {
        $waterInfo = $this->getImageInfo($water);

        if ( empty($waterInfo['width']) || empty($waterInfo['height']) )
        {
            throw new \InvalidArgumentException('Get watermark file information is failed.');
        }

        $this->waterImage = $this->createFrom($water);

        if (!$this->newImage || !is_resource($this->newImage))
        {
            $this->newImage = $this->sourceImage;
            $sourceWidth    = $this->source['width'];
            $sourceHeight   = $this->source['height'];
        }
        else
        {
            $sourceWidth    = imagesx($this->newImage);
            $sourceHeight   = imagesy($this->newImage);
        }

        $waterWidth  = ($waterInfo['width']  > $sourceWidth)  ? $sourceWidth  : $waterInfo['width'];
        $waterHeight = ($waterInfo['height'] > $sourceHeight) ? $sourceHeight : $waterInfo['height'];

        if ($tile)
        {
            imagealphablending($this->waterImage, true);
            imagesettile($this->newImage, $this->waterImage);
            imagefilledrectangle($this->newImage, 0, 0, $sourceWidth, $sourceHeight, IMG_COLOR_TILED);
        }
        else
        {
            $position = $this->position($pos, $sourceWidth, $sourceHeight, $waterWidth, $waterHeight);

            imagecopy($this->newImage, $this->waterImage, $position['x'], $position['y'], 0, 0, $waterWidth, $waterHeight);
        }

        return $this;
    }

    /**
     * Fill watermark tile
     *
     * @param string $water
     * @param int|array $pos 0
     * @return $this
     */

    public function watermarkTile($water, $pos = 0)
    {
        return $this->watermark($water, $pos, true);
    }

    /**
     * Text watermark for image
     *
     * @param string $text
     * @param int|array $pos 0
     * @param int $fontSize 14
     * @param array $color null
     * @param string $font null
     * @param bool $shadow true
     * @return $this
     */

    public function watermarkText($text, $pos = 0, $fontSize = 14, array $color = null, $font = null, $shadow = true)
    {
        if (!$color)
        {
            $color = [255, 255, 255, 0, 0, 0];
        }

        $font = (!$font) ? $this->fontFile : $font;

        if (!$this->newImage || !is_resource($this->newImage))
        {
            $this->newImage = $this->sourceImage;
            $sourceWidth    = $this->source['width'];
            $sourceHeight   = $this->source['height'];
        }
        else
        {
            $sourceWidth    = imagesx($this->newImage);
            $sourceHeight   = imagesy($this->newImage);
        }

        $textImage   = imagecreatetruecolor($sourceWidth, $sourceHeight);
        $textColor   = imagecolorallocate($textImage, $color[0], $color[1], $color[2]);
        $shadowColor = imagecolorallocate($textImage, $color[3], $color[4], $color[5]);

        // get 8 corners coordinates of the text watermark
        $size        = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth   = $size[4];
        $textHeight  = abs($size[7]);

        $position    = $this->position($pos, $sourceWidth, $sourceHeight, $textWidth + 4, $textHeight, true, $fontSize);

        $posX = $position['x'];
        $posY = $position['y'];

        imagealphablending($textImage, true);
        imagesavealpha($textImage, true);

        imagecopymerge($textImage, $this->newImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, 100);

        if ($shadow)
        {
            imagettftext($textImage, $fontSize, 0, $posX + 1, $posY + 1, $shadowColor, $font, $text);
        }

        imagettftext($textImage, $fontSize, 0, $posX, $posY, $textColor, $font, $text);

        $this->newImage = $textImage;

        return $this;
    }

    /**
     * Watermark position
     *
     * @param int|string $pos
     * @param int $oldWidth
     * @param int $oldHeight
     * @param int $waterWidth
     * @param int $waterHeight
     * @param bool $isText
     * @param int $fontSize
     * @return array
     */

    private function position($pos, $oldWidth, $oldHeight, $waterWidth, $waterHeight, $isText = false, $fontSize = 14)
    {
        if ( is_array($pos) )
        {
            return [
                'x' => $pos[0],
                'y' => $pos[1]
            ];
        }

        if ($pos === self::POS_TOP_LEFT)
        {
            $posX = 0;
            $posY = ($isText) ? $waterHeight : 0;
        }
        elseif ($pos === self::POS_TOP_CENTER)
        {
            $posX = ($oldWidth - $waterWidth) / 2;
            $posY = ($isText) ? $waterHeight : 0;
        }
        elseif ($pos === self::POS_TOP_RIGHT)
        {
            $posX = $oldWidth - $waterWidth;
            $posY = ($isText) ? $waterHeight : 0;
        }
        elseif ($pos === self::POS_CENTER_LEFT)
        {
            $posX = 0;
            $posY = ($isText) ? (($oldHeight - $waterHeight) / 2) + $fontSize : ($oldHeight - $waterHeight) / 2;
        }
        elseif ($pos === self::POS_CENTER_CENTER)
        {
            $posX = ($oldWidth - $waterWidth) / 2;
            $posY = ($isText) ? (($oldHeight - $waterHeight) / 2) + $fontSize : ($oldHeight - $waterHeight) / 2;
        }
        elseif ($pos === self::POS_CENTER_RIGHT)
        {
            $posX = $oldWidth - $waterWidth;
            $posY = ($isText) ? (($oldHeight - $waterHeight) / 2) + $fontSize : ($oldHeight - $waterHeight) / 2;
        }
        elseif ($pos === self::POS_BOTTOM_LEFT)
        {
            $posX = 0;
            $posY = ($isText) ? ($oldHeight - $waterHeight) + $fontSize : $oldHeight - $waterHeight;
        }
        elseif ($pos === self::POS_BOTTOM_CENTER)
        {
            $posX = ($oldWidth - $waterWidth) / 2;
            $posY = ($isText) ? ($oldHeight - $waterHeight) + $fontSize : $oldHeight - $waterHeight;
        }
        elseif ($pos === self::POS_BOTTOM_RIGHT)
        {
            $posX = $oldWidth - $waterWidth;
            $posY = ($isText) ? ($oldHeight - $waterHeight) + $fontSize : $oldHeight - $waterHeight;
        }
        else
        {
            $posX = rand(0, ($oldWidth  - $waterWidth));
            $posY = rand(0, ($oldHeight - $waterHeight));
        }

        return [
            "x" => $posX,
            "y" => $posY
        ];
    }

    /**
     * Set font file (FreeType font)
     *
     * @param string $fontFile
     * @return $this
     * @throws \InvalidArgumentException
     */

    public function setFontFile($fontFile)
    {
        if (!file_exists($fontFile))
        {
            throw new \InvalidArgumentException('font file ' .$fontFile . ' not found.');
        }

        $this->fontFile = $fontFile;

        return $this;
    }

    /**
     * Display on browser
     *
     * @param string $type jpeg
     * @return $this
     */

    public function display($type = 'jpeg')
    {
        $type = $this->getType($type);

        header('Content-Type: image/' . $type);

        if ($type === 'jpeg')
        {
            imageinterlace($this->newImage, true);
        }

        $imageFunc = 'image' . $type;
        $imageFunc($this->newImage);

        $this->destroyAll();

        return $this;
    }

    /**
     * Saved image file
     *
     * @param string $saveName
     * @param int $quality 80
     * @return $this
     * @throws \ErrorException
     */

    public function save($saveName, $quality = 80)
    {
        $type         = $this->getType(pathinfo($saveName, PATHINFO_EXTENSION));
        $imageFunc    = 'image' . $type;
        $errorMessage = 'Image saved is failed! Check the directory is can write?';

        if ($type === 'jpeg')
        {
            imageinterlace($this->newImage, true);

            if ( !$imageFunc($this->newImage, $saveName, $quality) )
            {
                throw new \ErrorException($errorMessage);
            }
        }
        else
        {
            if (!$imageFunc($this->newImage, $saveName))
            {
                throw new \ErrorException($errorMessage);
            }
        }

        $this->destroyAll();

        return $this;
    }

    /**
     * Image to data url base64
     *
     * @param string $type jpeg
     * @return string
     */

    public function dataUrl($type = 'jpeg')
    {
        $type      = $this->getType($type);
        $imageFunc = 'image' . $type;

        ob_start();

        $imageFunc($this->newImage);

        $data = ob_get_contents();

        ob_end_clean();

        $this->destroyAll();

        $dataUrl = 'data:image/'. $type . ';base64,' . base64_encode($data);

        return $dataUrl;
    }

    /**
     * Destroy image resource
     *
     * @param resource $resource
     * @return void
     */

    public function destroy($resource)
    {
        if ( is_resource($resource) )
        {
            imagedestroy($resource);
        }
    }

    /**
     * Destroy all image resource
     *
     * @return void
     */

    public function destroyAll()
    {
        if ($this->newImage)
        {
            $this->destroy($this->newImage);
        }

        if ($this->sourceImage)
        {
            $this->destroy($this->sourceImage);
        }
    }

    /**
     * Destructor
     *
     * @return void
     */

    public function __destruct()
    {
        $this->destroyAll();
    }
}