<?php

namespace Vnecoms\PdfPro\Model\Api;

/**
 * Interface AttachmentContainerInterface.
 */
interface AttachmentContainerInterface
{
    /**
     * @return bool
     */
    public function hasAttachments();

    /**
     * @param \Magento\Framework\DataObject $attachment
     */
    public function addAttachment(\Magento\Framework\DataObject $attachment);

    /**
     * @return mixed
     */
    public function getAttachments();

    /**
     * @return mixed
     */
    public function resetAttachments();
}
