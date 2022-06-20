<?php
namespace WeltPixel\GoogleTagManager\Observer;

use Magento\Framework\Event\ObserverInterface;

class CoreLayoutRenderElementObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper
     */
    public function __construct(\WeltPixel\GoogleTagManager\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\UrlInterface $urlInterface */
       /* $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $currentURl = $urlInterface->getCurrentUrl();
        if(strpos($currentURl, 'checkout#')!==false)
        {
        
            return $this;
        }
*/
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $elementName = $observer->getData('element_name');

        if ($elementName != 'weltpixel_gtm_head') {
            return $this;
        }

        $transport = $observer->getData('transport');
        $html = $transport->getOutput();

        $scriptContent = $this->helper->getDataLayerScript();
        $html = $scriptContent . PHP_EOL . $html;

        $transport->setOutput($html);

        return $this;
    }
}