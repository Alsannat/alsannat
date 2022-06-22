<?php
namespace MGS\Popup\Block;

class Popup extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;
    protected $_popupModel;
	protected $_request;
	protected $_date;
    protected $httpContext;
	protected $_url;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MGS\Popup\Model\Popup $popupModel,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\Url $url,
        array $data = []
    )
    {
		$this->_request = $request;
		$this->_fullActionName = $this->_request->getFullActionName();
        $this->_popupModel = $popupModel;
		$this->_date = $date;
		$this->_url = $url;
        $this->_coreRegistry = $registry;
		$this->_objectManager = $objectManager;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }
	
    public function getPopupCollection() {
		$collection = $this->_popupModel->getCollection()
				->addFieldToFilter('status', 1)
				->addStoreFilter($this->_storeManager->getStore());
				
		if(!$this->isHomepage()){
			$collection->addFieldToFilter('enable_on', 1);
		}
		
		return $collection;
    }
	
	public function getPopupForCustomer() {
		
		$popup = $this->getPopupCollection();
		
		$popup_required = array();
		
		if($this->isCustomerLogin()) {
			$customerInSession = $this->_objectManager->create('Magento\Customer\Model\Session');
			$customerGroupId = $customerInSession->getCustomer()->getGroupId();
			if(count($popup)) {
				foreach($popup as $_popup) {
					if($_popup->getCustomerGroup()) {
						$customerPopup = unserialize($_popup->getCustomerGroup());
						if(in_array($customerGroupId, $customerPopup)){
							$popup_required[] = $_popup;
						}
					}else {
						$popup_required[] = $_popup;
					}
				}
			}
		}else {
			if(count($popup)) {
				foreach($popup as $_popup) {
					if($_popup->getCustomerGroup()) {
						$customerPopup = unserialize($_popup->getCustomerGroup());
						if(in_array("0", $customerPopup)){
							$popup_required[] = $_popup;
						}
					}else {
						$popup_required[] = $_popup;
					}
				}
			}
		}
		return $popup_required;
	}
	
	public function getPopup()
	{
		$popup = $this->getPopupForCustomer();
		
		$bigguestPopup = false;
		$maxid = 0;
		
		$now = $this->getNowTime();
		$popup_required = array();
		if(count($popup)) {
			foreach($popup as $_popup) {
				
				$timeStart = $_popup->getTimeStart();
				$timeEnd = $_popup->getTimeEnd();
				
				if($timeStart == "") {
					$popup_required[] = $_popup;
				}else {
					$dateStart = date("Y-m-d H:i:s", strtotime($timeStart));
					if($timeEnd == null) {
						if($dateStart <= $now){
							$popup_required[] = $_popup;
						}
					}else {
						$dateEnd = date("Y-m-d H:i:s", strtotime($timeEnd));
						if($dateStart <= $now && $dateStart < $dateEnd){
							$popup_required[] = $_popup;
						}
					}
				}
			}
			
			foreach($popup_required as $_popup) {
				if($_popup->getPopupId() > $maxid) {
					$maxid = $_popup->getPopupId();
					$bigguestPopup = $_popup;
				}
			}	
		}
		
		return $bigguestPopup;
	}
	
	public function getNowTime() 
	{
		return $this->_date->gmtDate();
	}
	
	public function getPopupCss($popup) 
	{
		$cssPopup = 'style="';
		// Padding Top
		if($popup->getPaddingTop() && $popup->getPaddingTop() != 0){
			$cssPopup .= ' padding-top: '.$popup->getPaddingTop().'px;';
		}
		// Padding Bottom
		if($popup->getPaddingBottom() && $popup->getPaddingBottom() != 0){
			$cssPopup .= ' padding-bottom: '.$popup->getPaddingBottom().'px;';
		}
		// Background Color
		if($popup->getBackgroundColor()){
			$cssPopup .= ' background-color: '.$popup->getBackgroundColor().';';
		}
		// Background Image
		if($popup->getBackgroundImage()){
			$img_url = $this->getImageUrl($popup->getBackgroundImage());
			$cssPopup .= ' background-image: url('.$img_url.');';
		}
		// Background Repeat
		if($popup->getBackgroundRepeat() == 1){
			$cssPopup .= ' background-repeat: repeat;';
		}
		// Background Cover
		if($popup->getBackgroundCover() == 1){
			$cssPopup .= ' background-size: cover;';
		}
		// Background Position
		$backgroundPositionX = $popup->getBackgroundPositionX();
		$backgroundPositionY = $popup->getBackgroundPositionY();
		$cssPopup .= ' background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
		
		// Width
		if($popup->getPopupWidth() != 0){
			$cssPopup .= ' width: '.$popup->getPopupWidth().'px;';
		}else {
			$cssPopup .= ' width: 800px;';
		}
		// Height
		if($popup->getPopupHeight() != 0){
			$cssPopup .= ' height: '.$popup->getPopupHeight().'px;';
		}else {
			$cssPopup .= ' height: auto;';
		}
		
		$cssPopup .= '"';
		
		return $cssPopup;
	}
	
	public function getImageUrl($url)
	{
		
		$mediaUrl = $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
		
		$fullUrl = $mediaUrl . $url;
		
		return $fullUrl;
	}
	
	// Check if customer is logged in or not, if logged return true
	public function isCustomerLogin()
    {
		$customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
		if($customerSession->isLoggedIn()) {
		   return true;
		}
		return false;
    }
	
	// Check homepage, if is homepage return true
    public function isHomepage() 
	{
        if ($this->_fullActionName == 'cms_index_index') {
			return true;
		}
		return false;
    }
}
