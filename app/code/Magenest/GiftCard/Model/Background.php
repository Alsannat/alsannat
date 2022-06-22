<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:24
 */
namespace Magenest\GiftCard\Model;

class Background extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const XML_PATH_PATTERN ="giftcard/general/pattern";

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magenest\GiftCard\Model\History
     */
    protected $giftCardHistory;

    /** @var \Magenest\GiftCard\Model\HistoryFactory  */
    protected $giftCardHistoryFactory;

    /** @var $helper \Magenest\GiftCard\Helper\Data */
    protected $helper ;
    /**
     * constructor method
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\GiftCard\Model\ResourceModel\Background $resource = null,
        \Magenest\GiftCard\Model\ResourceModel\Background\Collection $resourceCollection = null,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\GiftCard\Model\HistoryFactory $historyFactory,
        \Magenest\GiftCard\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->checkoutSession = $checkoutSession;
        $this->giftCardHistoryFactory = $historyFactory;
        $this->helper = $helper;
    }

    public function _construct()
    {

        $this->_init('Magenest\GiftCard\Model\ResourceModel\Background');
    }
}
