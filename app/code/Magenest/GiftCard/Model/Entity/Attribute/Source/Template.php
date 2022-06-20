<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 26/07/2016
 * Time: 14:14
 */
namespace Magenest\GiftCard\Model\Entity\Attribute\Source;

class Template extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magenest\GiftCard\Model\TemplateFactory
     */
    protected $templateFactory;
    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
    ) {
        $this->_eavAttrEntity = $eavAttrEntity;
        /**
         * @var \Magenest\GiftCard\Model\TemplateFactory
         */
         $this->templateFactory = $templateFactory;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $templates = $this->templateFactory->create()->getCollection()->addFieldToFilter('status', 1);
        if (is_object($templates) && $templates->getSize() > 0) {
            foreach ($templates as $giftcardTemplate) {
                $this->_options[] =['label' =>$giftcardTemplate->getName(), 'value'=> $giftcardTemplate->getId()];
            }
        }
        return $this->_options;
    }
}
