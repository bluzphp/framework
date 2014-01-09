<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Request;

/**
 * HttpFile
 *
 * @category Bluz
 * @package  Request
 *
 * @author   Anton Shevchuk
 * @created  07.02.13 15:18
 */
class HttpFile
{
    /**
     * string
     */
    const TYPE_APPLICATION = 'application';
    const TYPE_AUDIO = 'audio';
    const TYPE_IMAGE = 'image';
    const TYPE_MESSAGE = 'message';
    const TYPE_MODEL = 'model';
    const TYPE_MULTIPART = 'multipart';
    const TYPE_TEXT = 'text';
    const TYPE_VIDEO = 'video';

    /**
     * @var string
     */
    protected $name;
    protected $ext;
    protected $type;
    protected $mimetype;
    protected $tmp;
    protected $error = UPLOAD_ERR_OK;
    protected $size = 0;

    /**
     * __construct
     */
    public function __construct($data = array())
    {
        if (!isset($data['name']) ||
            !isset($data['type']) ||
            !isset($data['tmp_name']) ||
            !isset($data['error'])
        ) {
            throw new RequestException("Invalid HTTP File Upload data");
        }

        if ($data['error'] != UPLOAD_ERR_OK) {
            $this->error = $data['error'];
            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $this->name = substr($data['name'], 0, strrpos($data['name'], '.'));
        $this->ext = substr($data['name'], strrpos($data['name'], '.') + 1);

        if ($this->ext) {
            $this->ext = strtolower($this->ext);
        }

        $this->mimetype = $finfo->file($data['tmp_name']);
        $this->type = substr($this->mimetype, 0, strpos($this->mimetype, '/'));
        $this->tmp = $data['tmp_name'];
        $this->size = $data['size'];
    }

    /**
     * set filename (w/o extension)
     *
     * @param string $name
     * @throws RequestException
     * @return string
     */
    public function setName($name)
    {
        if (empty($name)) {
            throw new RequestException("Rename error: wrong filename");
        }
        $this->name = $name;
        return $this;
    }

    /**
     * get original filename (w/o extension)
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get original filename (with extension)
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ($this->ext ? '.' . $this->ext : '');
    }

    /**
     * get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->ext;
    }

    /**
     * return type of file, one of:
     *   'application', 'audio', 'image', 'message',
     *   'model', 'multipart', 'text', 'video'
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * get mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimetype;
    }

    /**
     * getErrorCode
     *
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->error;
    }

    /**
     * get size
     *
     * @param string $exp
     * @return integer
     */
    public function getSize($exp = 'byte')
    {
        $exp = strtolower($exp);

        switch ($exp) {
            case 'tb':
            case 'terabyte':
            case 'terabytes':
                return round($this->size / (1024 * 1024 * 1024 * 1024));
                break;
            case 'gb':
            case 'gigabyte':
            case 'gigabytes':
                return round($this->size / (1024 * 1024 * 1024));
                break;
            case 'mb':
            case 'megabyte':
            case 'megabytes':
                return round($this->size / (1024 * 1024));
                break;
            case 'kb':
            case 'kilobyte':
            case 'kilobytes':
                return round($this->size / 1024);
                break;
            case 'b':
            case 'byte':
            case 'bytes':
            default:
                return $this->size;
        }
    }

    /**
     * move uploaded file to directory
     *
     * @param $path
     * @throws RequestException
     * @return string
     */
    public function moveTo($path)
    {
        if (!$this->tmp or
            !file_exists($this->tmp)
        ) {
            throw new RequestException("Temporary file is not exists, maybe you already moved it");
        }

        if (!is_uploaded_file($this->tmp)) {
            throw new RequestException("Temporary file is not uploaded by POST");
        }

        if (!is_dir($path)) {
            // try to create new folders
            @mkdir($path, 0755, true);
        }

        if (!is_writable($path)) {
            throw new RequestException("Target directory is not writable, I can't upload file");
        }

        $path = rtrim($path, '/');
        $path = $path . '/' . $this->getFullName();

        move_uploaded_file($this->tmp, $path);

        $this->tmp = null;
        return $path;
    }
}
