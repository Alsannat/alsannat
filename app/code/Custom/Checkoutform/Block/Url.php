<?php
namespace Custom\Checkoutform\Block;

/**
 * Customer login form block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Url extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }


    public function getLoginActionUrl()
    {
        return $this->getUrl('registration/index/loginpost', ['_secure' => true]);
    }

    public function getRegisterActionUrl()
    {
        return $this->getUrl('registration/index/createpost', ['_secure' => true]);
    }
}