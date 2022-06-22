<?php
namespace Ibnab\NotificationBar\Block;
class NotificationBarBottom extends \Magento\Framework\View\Element\Template
{
    protected $dataHelper;
    const COOKIE_TNF = 'ibnab-notification-bar-cookie-bottom';
    protected $_cookieManager;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ibnab\NotificationBar\Helper\DataBottom $dataHelper ,
        \Magento\Cms\Model\Template\FilterProvider $filterProvide ,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager ,
        array $data = []
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_filterProvider = $filterProvide;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
    public function getAllowGlobal()
    {
        return $this->dataHelper->allowExtension() && 
               $this->_hasNotEmpty() &&
               (!$this->isClosedAndCleared() || !$this->dataHelper->getAllowClosed());
    }
    protected function _hasNotEmpty() {
        $content = $this->getContent();
        return !empty($content);
    }
    public function getContent()
    {
        $content = $this->dataHelper->getDefaultContent();
        $html = $this->_filterProvider->getBlockFilter()->filter($content);       
        return $html;
    }
    public function isClosedAndCleared() {
        //var_dump($_COOKIE);die();
        return $this->_cookieManager->getCookie(self::COOKIE_TNF);
    }
    public function getHelper() {
        return $this->dataHelper ;
    }
}