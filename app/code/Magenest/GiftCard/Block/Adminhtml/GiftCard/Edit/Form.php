<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 09:01
 */
namespace Magenest\GiftCard\Block\Adminhtml\GiftCard\Edit;

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
        $giftCardData = $this->_coreRegistry->registry('current_giftcard_data');
        $giftcardFieldSet = $form->addFieldset('giftcard_fieldset', ['legend' => __('GiftCard Information'),'class'=>'general-fieldset']);

        $giftcardFieldSet->addField(
            'id',
            'hidden',
            ['name'=>'id',
                'label' => __('Id'),
                'title' => __('Id')
            ]
        );
        $giftcardFieldSet->addField(
            'code',
            'label',
            ['name'=>'code',
                'label' => __('Code'),
                'title' => __('Code')
            ]
        );

        $giftcardFieldSet->addField(
            'balance',
            'label',
            ['name'=>'balance',
                'label' => __('Balance'),
                'title' => __('Balance')
            ]
        );

        $giftcardFieldSet->addField(
            'status',
            'label',
            ['name'=>'status',
            'label' => __('Status'),
             'title' => __('Status')
            ]
        );

        $giftcardFieldSet->addField(
            'schedule_send_time',
            'date',
            [
                'name'=>'schedule_send_time',
                'disabled' => 'disabled',
                'format' =>'dd/mm/yy',
                'label' => __('Scheduled Sent Time'),
                'title' => __('Scheduled Sent Time')
            ]
        );

        $giftcardFieldSet->addField(
            'date_expired',
            'date',
            [
                'name'=>'date_expired',
                'disabled' => 'disabled',
                'format' =>'dd/mm/yy',
                'label' => __('Expiry Date'),
                'title' => __('Expiry Date')
            ]
        );

        $giftcardFieldSet->addField(
            'date_created',
            'label',
            [
                'name'=>'date_created',
                'disabled' => 'disabled',
                'format' =>'dd/mm/yy',
                'label' => __('Created Date'),
                'title' => __('Created Date')
            ]
        );

        /** @var  $orderFieldSet */
        $orderFieldSet = $form->addFieldset('order_fieldset', ['legend' => __('Order Information'),'class'=>'order-fieldset']);
        $orderFieldSet->addField(
            'product_name',
            'link',
            [
                'name'=>'product_name',
                'href'=>$giftCardData['product_link'],
                'label' => __('Product'),
                'title' => __('Product')
            ]
        );

        $orderFieldSet->addField(
            'order_id',
            'link',
            [
                'name'=>'order_id',
                'href' => $this->getOrderLink($giftCardData['order_id']) ,
                'label' => __('Order'),
                'title' => __('Order')
            ]
        );


        $recipientFieldSet = $form->addFieldset('recipient_fieldset', ['legend' => __('Recipient Information'),'class'=>'recipient-fieldset']);

        $recipientFieldSet->addField(
            'sender_name',
            'text',
            ['name'=>'sender_name' , 'label' => __('Sender Name'),'title' => __('Sender Name')]
        );

        $recipientFieldSet->addField(
            'sender_email',
            'text',
            ['name'=>'sender_email' , 'label' => __('Sender Email'),'title' => __('Sender Email')]
        );

        $recipientFieldSet->addField(
            'recipient_name',
            'text',
            ['name'=>'recipient_name' , 'label' => __('Recipient Name'),'title' => __('Recipient Name')]
        );

        $recipientFieldSet->addField(
            'recipient_email',
            'text',
            ['name'=>'recipient_email' , 'label' => __('Recipient Email'),'title' => __('Recipient Email')]
        );

        $recipientFieldSet->addField(
            'headline',
            'text',
            ['name'=>'headline' , 'label' => __('Headline'),'title' => __('Headline')]
        );

        $recipientFieldSet->addField(
            'message',
            'textarea',
            ['name'=>'message', 'label' => __('Message'),'title' => __('Message')]
        );

        //personaldesign
        $recipientFieldSet->addField(
            'personal_design',
            'image',
            ['name'=>'personal_design', 'label' => __('View Design'),'title' => __('View Design')]
        );

        if (!isset($giftCardData['personal_design'])) $giftCardData['personal_design'] ='';
        $regularExp = "/.*media\/(.*)/";
        preg_match($regularExp,$giftCardData['personal_design'],$matches);

        if (isset($matches[1])) {
            $giftCardData['personal_design'] = $matches[1];
        }
        //end of design

        //the pdf gift card
        $recipientFieldSet->addField(
            'giftcard_pdf',
            'Magenest\GiftCard\Block\Adminhtml\GiftCard\Fields\Pdf',
            ['name'=>'giftcard_pdf', 'label' => __('View Design with pdf format'),'title' => __('View Gift card pdf')]
        );

        $regularExp = "/.*media\/(.*)/";
        preg_match($regularExp,$giftCardData['giftcard_pdf'],$matches);

        if (isset($matches[1])) {
            $giftCardData['giftcard_pdf'] = $matches[1];
        }
        //end of pdf gift card field

        //display the balance currency symbol
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

        if (isset($giftCardData['store_id'])) {
            $currencyCode = $storeManager->getStore($giftCardData['store_id'])->getBaseCurrency()->getCurrencySymbol();
        } else {
            $currencyCode = $storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        }

       // $currency = $objectManager->create('Magento\Directory\Model\Currency')->loa‌​d($currencyCode);
        //$currencySymbol = $currency->getCurrencySymbol();
        if (isset($giftCardData['balance'])) $giftCardData['balance'] = $giftCardData['balance'].' '. $currencyCode;
        $this->setForm($form);
        $form->setValues($giftCardData);
        return parent::_prepareForm();
    }

    public function getOrderLink($orderId)
    {
        $url = $this->getUrl('sales/order/view/',['order_id' => $orderId]);
        return $url;
    }
}
