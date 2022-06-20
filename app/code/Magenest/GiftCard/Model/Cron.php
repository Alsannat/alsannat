<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 19/06/2017
 * Time: 14:57
 */

namespace Magenest\GiftCard\Model;


class Cron
{
    protected $giftCardFactory;

    protected $logger;

    protected $helper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\GiftCard\Helper\Data $helper,
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
       $this->giftCardFactory = $giftCardFactory;
       $this->logger = $logger;
       $this->helper = $helper;
    }

    public function sendScheduledMail()
    {
        $collection = $this->giftCardFactory->create()->getCollection()->getCardNeedToBeSent();

        if ($collection->getSize() > 0) {
            foreach ($collection as $card) {
                $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                try {
                    $this->helper->sendEmail($card);

                    $card->addData([
                        'is_sent'=> 1,
                        'status'  => 1,
                        'available_date' => $now
                    ]);
                } catch (\Exception $e) {
                    $card->addData(['is_sent'=>0]);
                }
            }//end foreach
        }//end if
    }//end sendScheduledMail()

}