<?php

/**
 *
 */
namespace Custom\Checkoutform\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\Redirect;
/**
 *
 */
class CardHandlerService
{
    public $assetRepository;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository
    ) {
        $this->assetRepository = $assetRepository;
    }

    public function afterGetCardIcons(\CheckoutCom\Magento2\Model\Service\CardHandlerService $subject,$output)
    {
        

        $output[] = [
            'code' => "mada",
            'name' => __("mada"),
            'url' => $this->assetRepository
            ->getUrl(
                'CheckoutCom_Magento2::images/cc/mada.png'
            )
        ];


        return $output;
    }

}