<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/03/2016
 * Time: 11:23
 */
namespace Magenest\GiftCard\Block\Plugin\Adminhtml\Product\Edit\Tab;

class Attributes
{
    protected function _prepareForm(\Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes $subject, \Closure $proceed)
    {
        /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
        $group = $this->getGroup();
        if ($group) {
            /** @var \Magento\Framework\Data\Form $form */
            $form = $this->_formFactory->create();
            $product = $this->_coreRegistry->registry('product');
            $isWrapped = $this->_coreRegistry->registry('use_wrapper');
            if (!isset($isWrapped)) {
                $isWrapped = true;
            }
            $isCollapsable = $isWrapped && $group->getAttributeGroupCode() == 'product-details';
            $legend = $isWrapped ? __($group->getAttributeGroupName()) : null;
            // Initialize product object as form property to use it during elements generation
            $form->setDataObject($product);

            $fieldset = $form->addFieldset(
                'group-fields-' . $group->getAttributeGroupCode(),
                ['class' => 'user-defined', 'legend' => $legend, 'collapsable' => $isCollapsable]
            );

            $attributes = $this->getGroupAttributes();

            $this->_setFieldset($attributes, $fieldset, ['gallery']);

            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Tier')
                );
            }


            $s = $form->getElement('giftcard_template');
            if ($s) {
                $s->setRenderer(
                    $this->getLayout()->createBlock('Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\Template')
                );
            }

            // Add new attribute controls if it is not an image tab
            if (!$form->getElement(
                'media_gallery'
            ) && $this->_authorization->isAllowed(
                'Magento_Catalog::attributes_attributes'
            ) && $isWrapped
            ) {
                $attributeCreate = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Create'
                );

                $attributeCreate->getConfig()->setAttributeGroupCode(
                    $group->getAttributeGroupCode()
                )->setTabId(
                    'group_' . $group->getId()
                )->setGroupId(
                    $group->getId()
                )->setStoreId(
                    $form->getDataObject()->getStoreId()
                )->setAttributeSetId(
                    $form->getDataObject()->getAttributeSetId()
                )->setTypeId(
                    $form->getDataObject()->getTypeId()
                )->setProductId(
                    $form->getDataObject()->getId()
                );

                $attributeSearch = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Search'
                )->setGroupId(
                    $group->getId()
                )->setGroupCode(
                    $group->getAttributeGroupCode()
                );

                $attributeSearch->setAttributeCreate($attributeCreate->toHtml());

                $fieldset->setHeaderBar($attributeSearch->toHtml());
            }

            $values = $product->getData();

            // Set default attribute values for new product or on attribute set change
            if (!$product->getId() || $product->dataHasChangedFor('attribute_set_id')) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if ($product->hasLockedAttributes()) {
                foreach ($product->getLockedAttributes() as $attribute) {
                    $element = $form->getElement($attribute);
                    if ($element) {
                        $element->setReadonly(true, true);
                        $element->lock();
                    }
                }
            }

            $form->addValues($values);
            $form->setFieldNameSuffix('product');

            $this->_eventManager->dispatch(
                'adminhtml_catalog_product_edit_prepare_form',
                ['form' => $form, 'layout' => $this->getLayout()]
            );

            $this->setForm($form);
        }
    }
}
