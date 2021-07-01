<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Response;

use Laminas\Diactoros\Response as DiactorsResponse;
use Laminas\Diactoros\Response\InjectContentTypeTrait;
use Laminas\Diactoros\Stream;

/**
 * Class AttachmentResponse
 *
 * @package Bluz\Response
 * @link    http://www.marco-bunge.com/2016/09/01/file-downloads-with-Laminas-diactoros/
 */
class AttachmentResponse extends DiactorsResponse
{
    use InjectContentTypeTrait;

    /**
     * Create a file attachment response.
     *
     * Produces a text response with a Content-Type of given file mime type and a default
     * status of 200.
     *
     * @param string $file    Valid file path
     * @param int    $status  Integer status code for the response; 200 by default.
     * @param array  $headers Array of headers to use at initialization.
     *
     * @internal param StreamInterface|string $text String or stream for the message body.
     * @throws \InvalidArgumentException
     */
    public function __construct($file, $status = 200, array $headers = [])
    {
        $fileInfo = new \SplFileInfo($file);

        $headers = array_replace(
            $headers,
            [
                'content-length' => $fileInfo->getSize(),
                'content-disposition' => sprintf('attachment; filename=%s', $fileInfo->getFilename()),
            ]
        );

        parent::__construct(
            new Stream($fileInfo->getRealPath(), 'r'),
            $status,
            $this->injectContentType((new \finfo(FILEINFO_MIME_TYPE))->file($fileInfo->getRealPath()), $headers)
        );
    }
}
