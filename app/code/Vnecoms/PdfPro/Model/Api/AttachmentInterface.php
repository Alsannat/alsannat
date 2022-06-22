<?php

namespace Vnecoms\PdfPro\Model\Api;

/**
 * Interface AttachmentInterface.
 */
interface AttachmentInterface
{
    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return string
     */
    public function getDisposition();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @return string
     */
    public function getContent();
}
