<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/slimore/slimore
 * @license     MIT License https://github.com/slimore/slimore#license
 * @version     0.1.0
 * @package     Slimore\Upload
 */

namespace Slimore\Upload;

/**
 * Class Uploader
 *
 * @author Pandao
 * @package Slimore\Upload
 */

class Uploader
{
    /**
     * $_FILES array
     *
     * @var array
     */
    public $files;

    /**
     * Read and write authority mode
     *
     * @var int
     */

    public $mode = 0755;

    /**
     * @var string
     */
    public $lang = 'EN';

    /**
     * Filename extension
     *
     * @var string
     */
    public $fileExit;

    /**
     * Saved filename
     *
     * @var string
     */
    public $saveName;

    /**
     * Url path Saved to database
     *
     * @var string
     */
    public $saveURL;

    /**
     * Saved the local file path
     *
     * @var string
     */

    public $savePath;

    /**
     * Output result message
     *
     * @var string
     */
    public $message;

    /**
     * Generate the length of the random file name
     *
     * when the date is the date() format
     *
     * @var string|int
     */

    public $randomLength   = 'Ymd';

    /**
     * Generate the random form
     *
     * NULL to retain the original file name
     * 1 generated random strings
     * 2 generate the date file name
     *
     * @var int
     */

    public $randomNameType = 1;

    /**
     * Timezone
     *
     * @var string
     */

    public $timezone = 'PRC';

    /**
     * Allow Upload file format
     *
     * @var array
     */

    public $formats = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp'];

    /**
     * Maximum upload file size, unit KB
     *
     * @var int
     */

    public $maxSize = 1024;

    /**
     * Whether to cover the same name file, true covered, false not covered
     * @var bool
     */
    public $cover = true;

    /**
     * Whether URL redirect
     *
     * @var bool
     */
    public $redirect = false;

    /**
     * Redirect url
     *
     * @var string
     */

    public $redirectURL = "";

    /**
     * Errors message
     *
     * @var array
     */
    public $errors = [
        'empty'      => 'The upload file can\'t be empty.',
        'format'     => 'The uploaded file format does not conform to the regulations.',
        'maxsize'    => 'The upload file size too large.',
        'unwritable' => 'Save the directory not to write, please change permissions.',
        'not_exist'  => 'Save the directory not exist.',
        'same_file'  => 'There are already the same file exist.'
    ];

    /**
     * Constructor
     *
     * @access public
     * @param array $configs
     * @return  viod
     */

    public function __construct(array $configs)
    {
        $this->config($configs);
    }

    /**
     * Set configs
     *
     * @access  public
     * @param   array $configs
     * @return  void
     */

    public function config(array $configs)
    {
        foreach($configs as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * Execute upload
     *
     * @access  public
     * @param   string $name  fileInput's name
     * @return  bool
     */

    public function upload($name)
    {
        // When $_FILES[$name]['name'] empty

        if ( empty($_FILES[$name]['name']) )
        {
            $this->error($this->errors['empty'], 0, true);

            return false;
        }

        $this->files = $_FILES[$name];

        // When the directory is not exist.
        if( !file_exists($this->savePath) )
        {
            $this->error($this->errors['not_exist'], 0, true);

            return false;
        }

        // When the directory is not written
        if( !is_writable($this->savePath) )
        {
            $this->error($this->errors['unwritable'], 0, true);

            return false;
        }

        return $this->moveFile();
    }

    /**
     * Check and move the upload file
     *
     * @access  private
     * @return  bool
     */

    private function moveFile()
    {
        $this->setSeveName();

        $files = $this->files;

        if ($this->formats != "" && !in_array($this->fileExt, $this->formats))
        {
            $formats  = implode(',', $this->formats);
            $message  = "Your upload file " . $files["name"] . " is " . $this->fileExt;
            $message .= " format, The system is not allowed to upload, you can only upload " . $formats . " format's file.";

            $this->error($message, 0, true);

            return false;
        }

        if ($files["size"] / 1024 > $this->maxSize)
        {
            $message = "Your upload file " . $files["name"] . "  The file size exceeds of the system limit size " . $this->maxSize . " KB.";
            $this->error($message, 0, true);

            return false;
        }

        // When can't covered
        if (!$this->cover)
        {
            // The same file already exists
            if (file_exists($this->savePath . $this->saveName))
            {
                $this->error($this->saveName . $this->errors['same_file'], 0, true);

                return false;
            }
        }

        if ( !@move_uploaded_file( $files["tmp_name"], iconv("utf-8", "gbk", $this->savePath . $this->saveName) ) )
        {
            switch ($files["error"])
            {
                case '0':
                    $message = "File upload successfully.";
                    break;

                case '1':
                    $message = "The uploaded file exceeds the value of the upload_max_filesize option in php.ini.";
                    break;

                case '2':
                    $message = "The size of the upload file exceeds the value specified by the MAX_FILE_SIZE option in the HTML form.";
                    break;

                case '3':
                    $message = "Only part of the file is uploaded.";
                    break;

                case '4':
                    $message = "No file is uploaded.";
                    break;

                case '6':
                    $message = "Can't find upload temp directory.";
                    break;

                case '7':
                    $message = "Error writing file to hard drive";
                    break;

                case '8':
                    $message = "An extension has stopped the upload of the file.";
                    break;

                case '999':
                default:
                    $message = "Unknown error, please check the file is damaged, whether the oversized and other reasons.";
                    break;
            }

            $this->error($message, 0, true);

            return false;
        }

        @unlink($files["tmp_name"]); // Delete temporary file

        return true;
    }

    /**
     * Generate random file name
     *
     * @access  private
     * @return  string $fileName
     */

    private function randomFileName()
    {
        $fileName = '';

        // Generate the datetime format file name
        if ($this->randomNameType == 1)
        {
            date_default_timezone_set($this->timezone);

            $date     = date($this->randomLength);
            echo $dir      = $this->savePath . $date;

            if ( !file_exists($dir) ) {
                mkdir($dir, $this->mode, true);
            }

            $fileName = $date . '/' . time();
        }
        elseif ($this->randomNameType == 2)    // Generate random character file name
        {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max   = strlen($chars) - 1;
            mt_srand( (double) microtime() * 1000000);

            for ($i = 0; $i < $this->randomLength; $i++)
            {
                $fileName .= $chars[mt_rand(0, $max)];
            }
        }
        else
        {
        }

        $this->fileExt = $this->getFileExt($this->files["name"]);

        $fileName = $fileName . '.' . $this->fileExt;

        return $fileName;
    }

    /**
     * Set Saved filename for database
     *
     * @access  private
     * @return  void
     */

    private function setSeveName()
    {
        $this->saveName = $this->randomFileName();

        if ($this->saveName == '')
        {
            $this->saveName = $this->files['name'];
        }
    }

    /**
     * Get Saved filename for database
     *
     * @access  public
     * @return  string
     */

    public function getSeveName()
    {
        return $this->saveName;
    }

    /**
     * Get filename extension
     *
     * @access public
     * @param string $fileName
     * @return string
     */

    public function getFileExt($fileName)
    {
        return trim( strtolower( substr( strrchr($fileName, '.'), 1) ) );
    }

    /**
     * Redirect for Upload success, failure or error
     *
     * @access  public
     * @return  void
     */

    public function redirect()
    {
        header('location: ' . $this->redirectURL);
    }

    /**
     * Errors message handle
     *
     * @access public
     * @param string $message
     * @param int $success
     * @param bool $return false
     * @return array|string
     */

    public function message($message, $success = 0, $return = false)
    {
        $array = array(
            'success' => $success,
            'message' => $message
        );

        $url = $this->saveURL . $this->saveName;

        // Cross-domain redirect to callback url
        if ($this->redirect)
        {
            $this->redirectURL .= '&success=' . $success . '&message=' . $message;

            if ($success == 1)
            {
                $this->redirectURL .= '&url=' . $url;
            }

            $this->redirect();
        }
        else
        {
            echo "success =>" . $success;
            if ($success == 1)
            {
                $array['url'] = $url;
            }

            $this->message = $array = json_encode($array);

            if ($return)
            {
                return $array;
            }
            else
            {
                echo $array;
            }
        }
    }

    /**
     * Set JSON mime header
     *
     * @return void
     */

    public function jsonHeader()
    {
        header('Content-Type: application/json');
    }

    /**
     * Upload success message handle
     *
     * @param string $message Upload successfully.
     * @param bool $return false
     * @return array|string
     */

    public function success($message = "Upload successfully.", $return = false)
    {
        return $this->message($message, 1, $return);
    }

    /**
     * Upload failed or error message handle
     *
     * @param string $message Upload failed.
     * @param bool $return false
     * @return array|string
     */

    public function error($message = "Upload failed.", $return = false)
    {
        return $this->message($message, 0, $return);
    }

    /**
     * Error exit
     *
     * @return void
     */

    public function errorExit()
    {
         exit;
    }
}