<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/03/2016
 * Time: 15:32
 */
namespace Magenest\GiftCard\Model\Mail;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $params
     */
    public function createAttachment($params)
    {
        if (isset($params['cat'])) {
            if ($params['cat'] == 'pdf') {

                $this->message->createAttachment(
                    $params['body'],
                    'application/pdf',
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $params['name']
                );

            } elseif ($params['cat'] == 'png') {
                $this->message->createAttachment(
                    $params['body'],
                    'image/png',
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $params['name']
                );
            }
        } else {
            $type = isset($params['type']) ? $params['type'] : \Zend_Mime::TYPE_OCTETSTREAM;
            $encoding = isset($params['encoding']) ? $params['encoding'] : \Zend_Mime::ENCODING_BASE64;

            $this->message->createAttachment(
                $params['body'],
                $type,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                $encoding,
                $params['name']
            );
        }
        return $this;
    }

    public function prepare()
    {
        return $this->prepareMessage();
    }
}
