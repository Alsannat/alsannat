<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 09:01
 */
namespace Magenest\GiftCard\Block\Adminhtml\GiftCard\Add;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();


        $form->setHtmlIdPrefix('rule_');
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('giftcard/giftcard/save'));
        $model = $this->_coreRegistry->registry('current_giftcard');
        $giftcardFieldSet = $form->addFieldset('giftcard_fieldset', ['legend' => __('GiftCard Information')]);

        $giftcardFieldSet->addField(
            'code',
            'label',
            ['name'=>'type']
        );

        $giftcardFieldSet->addField(
            'balance',
            'label',
            ['name'=>'balance']
        );

        $giftcardFieldSet->addField(
            'status',
            'label',
            ['name'=>'status']
        );

        $giftcardFieldSet->addField(
            'date_expired',
            'date',
            ['name'=>'date_expired']
        );

        $giftcardFieldSet->addField(
            'date_created',
            'label',
            ['name'=>'date_created']
        );

        $orderFieldSet = $form->addFieldset('order_fieldset', ['legend' => __('Order Information')]);
        $orderFieldSet->addField(
            'product_id',
            'link',
            ['name'=>'product_id']
        );

        $orderFieldSet->addField(
            'order_id',
            'link',
            ['name'=>'order_id']
        );

        $orderFieldSet->addField(
            'customer_id',
            'link',
            ['name'=>'customer_id']
        );
        $this->setForm($form);
        $form->setValues(['type'=>'what', 'order_id' =>'freak']);
        return parent::_prepareForm();
    }
}
