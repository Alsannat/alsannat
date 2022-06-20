<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 22/01/2016
 * Time: 16:45
 */
namespace Magenest\GiftCard\Block\Product;

class GiftCard extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_template = 'catalog/product/giftcard.phtml';

    protected $_storeManager;
    protected $templateFactory;
    protected $productRepo;
    protected $productFactory;
    protected $currency;
    protected $helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Directory\Model\Currency $currency,
        \Magenest\GiftCard\Helper\Cart $cartHelper,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->templateFactory = $templateFactory;
        $this->productRepo = $productRepository;
        $this->productFactory = $productFactory;
        $this->helper = $cartHelper;
        $this->currency = $currency;
        parent::__construct($context, $data);
    }
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }

    public function getCurrentProductId()
    {
        $id = $this->_coreRegistry->registry('current_product')->getId();

        return $id;
    }

    public function getProduct()
    {
        $productId = $this->getCurrentProductId();
        $product = $this->productFactory->create()->load($productId);
       // $product =$this->productRepo->getById($productId);
        return $product;
    }

    public function getPriceScheme()
    {
        $product = $this->getProduct();
        $priceSchema = $product->getData('giftcard_price_scheme');
        return $priceSchema;
    }
    public function isFixedPrice()
    {
        $priceScheme = $this->getPriceScheme();
        if ($priceScheme == 0) {
            return true;
        }
        return false;
    }

    public function isFixedPriceScheme()
    {
        $priceScheme = $this->getPriceScheme();
        if ($priceScheme == 0) {
            return true;
        }
        return false;
    }

    public function isSelectedPriceScheme()
    {
        $priceScheme = $this->getPriceScheme();
        if ($priceScheme == 1) {
            return true;
        }
        return false;
    }

    public function isMixedPriceScheme()
    {
        $priceScheme = $this->getPriceScheme();
        if ($priceScheme == 2) {
            return true;
        }
        return false;
    }

    public function isAllowOpenPrice()
    {
        //gc_is_allow_open_price
        $product = $this->getProduct();
        $gc_is_allow_open_price = $product->getData('gc_is_allow_open_price');
        if ($gc_is_allow_open_price == 1) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getSelectorPrice()
    {
        $priceList = [];
        $product  = $this->getProduct();
        $selectedPrices = explode(';', $product->getData('giftcard_price_selector'));

        if (count($selectedPrices) > 0) {
            foreach ($selectedPrices as $price) {
                 $refinedPrice = floatval($price);

                if ($refinedPrice > 0 ) {
                    $priceList[] = $refinedPrice;
                }

            }

        }
        return $priceList;
    }

    /**
     * @return int
     */
    public function getMinPrice()
    {
        $product  = $this->getProduct();

        return $product->getData('gc_min_price');
    }

    public function getMaxPrice()
    {
        $product  = $this->getProduct();

        return $product->getData('gc_max_price');
    }

    public function getGiftCardTemplates()
    {
        $productId = $this->getCurrentProductId();
        $product =$this->productRepo->getById($productId);

        $rawGiftCardTemplates = $product->getData('giftcard_template');
        $templateIds = explode(',', $rawGiftCardTemplates);
        $giftcardTemplateCollection = $this->templateFactory->create()->getCollection()->addFieldToFilter('template_id', ['in'=>$templateIds]);
        return   $giftcardTemplateCollection;
    }

    public function getTemplateImage($template)
    {
        $main_Img = $template->getData('saved_image_selected');
        if ($main_Img) {
            $url = $this->getMediaUrl() .'giftcard/template/main'.$main_Img;
        } else {
            $url =  $this->getDefaultIconGiftCard();
        }

        return $url;
    }

    /**
     * @description get the default icon of Gift Card
     */
    public function getDefaultIconGiftCard()
    {
        return $this->getViewFileUrl('Magenest_GiftCard::images/bg1.png');
    }

    /**
     * @return array
     */
    public function getEditOption()
    {
        $options=[];
        $buyRequest = $this->helper->getBuyRequest();
        if ($buyRequest) {
            if (isset($buyRequest['giftcard'])) {
                $options =  $buyRequest['giftcard'];
            }
        }

        return $options;
    }

    /**
     * whether the amount is selected
     * @param $amount
     * @return boolean
     */
    public function isSelectedGiftCardAmount($amount)
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_amount'])) {
                if ($options['giftcard_amount'] == $amount) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * get edited gift card amount
     */
    public function getEditedGiftCardAmount()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_amount'])) {
                return (floatval($options['giftcard_amount']));
            }
        }
    }
    /**
     * get edited gift card recipient
     */
    public function getEditedGiftCardRecipient()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_recipient_name'])) {
                return ($options['giftcard_recipient_name']);
            }
        }
    }
    /**
     * get edited gift card recipient email
     */
    public function getEditedGiftCardRecipientEmail()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_recipient_email'])) {
                return ($options['giftcard_recipient_email']);
            }
        }
    }

    /**
     * get edited gift card's sender name
     */
    public function getEditedGiftCardSender()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_sender_name'])) {
                return ($options['giftcard_sender_name']);
            }
        }
    }

    /**
     * get edited gift card's sender name
     */
    public function getEditedGiftCardSenderEmail()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_sender_email'])) {
                return ($options['giftcard_sender_email']);
            }
        }
    }
    /**
     * get edited gift card's sender name
     */
    public function getEditedGiftCardHeadline()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_headline'])) {
                return ($options['giftcard_headline']);
            }
        }
    }

    /**
     * get edited gift card's sender name
     */
    public function getEditedGiftCardMessage()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_message'])) {
                return ($options['giftcard_message']);
            }
        }
    }
    /**
     * get edited gift card's sender name
     */
    public function getEditedGiftCardSentDay()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_schedule_send_time'])) {
                $date =  ($options['giftcard_schedule_send_time']);
                try {
                  $dateObj = new \DateTime($date);
                  $formatedDate = $dateObj->format('d/m/Y');
                  return $formatedDate;
                }catch ( \Exception $e) {

                }
            }
        }
    }

    public function getEditedGiftCardTemplate()
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_template'])) {
                return ($options['giftcard_template']);
            }
        }
    }

    public function isSelectedGiftCardTemplate($template)
    {
        $options = $this->getEditOption();

        if (!empty($options))
        {
            if (isset($options['giftcard_template'])) {
                if ($options['giftcard_template'] == $template) {
                    return true;
                }
            }
        }
        return false;
    }

}
