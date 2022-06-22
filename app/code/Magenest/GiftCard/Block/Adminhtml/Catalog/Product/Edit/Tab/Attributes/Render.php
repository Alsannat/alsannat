<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 02/03/2016
 * Time: 16:01
 */
namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

use Magento\Backend\Block\Widget;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Render extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
{
    protected $_template = 'catalog/product/edit/tab/attribute/template.phtml';

    /**
     * @var \Magenest\GiftCard\Model\TemplateFactory
     */
    protected $_templateFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        array $data = []
    ) {
    
        parent::__construct($context, $groupRepository, $directoryHelper, $moduleManager, $registry, $groupManagement, $searchCriteriaBuilder, $localeCurrency, $data);
        $this->_templateFactory = $templateFactory;
    }

    public function getSelectedTemplate()
    {
        return $rawData = $this->getProduct()->getData('giftcard_template');
    }

    public function getAvailableTemplate()
    {
        return  $this->_templateFactory->create()->getCollection()->addFieldToFilter('status', 1);
    }
}
