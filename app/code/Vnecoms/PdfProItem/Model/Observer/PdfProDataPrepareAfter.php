<?php

namespace Vnecoms\PdfProItem\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
class PdfProDataPrepareAfter implements ObserverInterface {

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $type = $observer->getType();
        if($type=='item'){
            $itemData = $observer->getSource();
            $item = $observer->getModel();

            if($item instanceof \Magento\Sales\Model\Order\Item) {
                /*\Magento\Sales\Model\Order\Item*/

            }else if($item instanceof \Magento\Sales\Model\Order\Invoice\Item) {
                /*\Magento\Sales\Model\Order\Invoice\Item*/

            }else if($item instanceof \Magento\Sales\Model\Order\Shipment\Item) {
                /*\Magento\Sales\Model\Order\Shipment\Item*/

            }else if($item instanceof \Magento\Sales\Model\Order\Creditmemo\Item) {
                /*\Magento\Sales\Model\Order\Creditmemo\Item*/

            }

            /*
				$customVariable = new Varien_Object(array(
					'property1'	=> 'value1',
					'property2'	=> 'value2',
					'property3'	=> 'value3',
				));
				$itemData->setData('custom_variable',$customVariable);

				$customVariable1 = 'This is value of custom variable 1';
				$itemData->setData('custom_variable_1',$customVariable1);

				In your PDF template you can use

				{{var item.custom_variable.property1}}
				{{var item.custom_variable.property2}}
				{{var item.custom_variable.property3}}
				{{var item.custom_variable_1}}
			*/
        } else if($type == 'order') {
            $orderData 	= $observer->getSource();
            $order 		= $observer->getModel(); //Magento\Sales\Model\Order

            /*
				$customVariable = new Varien_Object(array(
					'property1'	=> 'value1',
					'property2'	=> 'value2',
					'property3'	=> 'value3',
				));
				$orderData->setData('custom_variable',$customVariable);

				$customVariable1 = 'This is value of custom variable 1';
				$orderData->setData('custom_variable_1',$customVariable1);

				In your Order PDF template you can use

				{{var order.custom_variable.property1}}
				{{var order.custom_variable.property2}}
				{{var order.custom_variable.property3}}
				{{var order.custom_variable_1}}
			*/
        }else if($type == 'invoice') {
            $invoiceData 	= $observer->getSource();
            $invoice 		= $observer->getModel();	/*Magento\Sales\Model\Order\Invoice*/
            /*
                $customVariable = new Varien_Object(array(
                    'property1'	=> 'value1',
                    'property2'	=> 'value2',
                    'property3'	=> 'value3',
                ));
                $invoiceData->setData('custom_variable',$customVariable);

                $customVariable1 = 'This is value of custom variable 1';
                $invoiceData->setData('custom_variable_1',$customVariable1);

                In your Invoice PDF template you can use

                {{var invoice.custom_variable.property1}}
                {{var invoice.custom_variable.property2}}
                {{var invoice.custom_variable.property3}}
                {{var invoice.custom_variable_1}}
            */
        }else if($type =='shipment') {
            $shipmentData 	= $observer->getSource();
            $shipment 		= $observer->getModel();	/*Magento\Sales\Model\Order\Shipment*/

            /*
                $customVariable = new Varien_Object(array(
                    'property1'	=> 'value1',
                    'property2'	=> 'value2',
                    'property3'	=> 'value3',
                ));
                $shipmentData->setData('custom_variable',$customVariable);

                $customVariable1 = 'This is value of custom variable 1';
                $shipmentData->setData('custom_variable_1',$customVariable1);

                In your Shipment PDF template you can use

                {{var shipment.custom_variable.property1}}
                {{var shipment.custom_variable.property2}}
                {{var shipment.custom_variable.property3}}
                {{var shipment.custom_variable_1}}
            */
        }else if($type == 'creditmemo') {
            $creditmemoData = $observer->getSource();
            $creditmemo 	= $observer->getModel();	/*Magento\Sales\Model\Order\Creditmemo*/

            /*
				$customVariable = new Varien_Object(array(
					'property1'	=> 'value1',
					'property2'	=> 'value2',
					'property3'	=> 'value3',
				));
				$creditmemoData->setData('custom_variable',$customVariable);

				$customVariable1 = 'This is value of custom variable 1';
				$creditmemoData->setData('custom_variable_1',$customVariable1);

				In your Creditmemo PDF template you can use

				{{var creditmemo.custom_variable.property1}}
				{{var creditmemo.custom_variable.property2}}
				{{var creditmemo.custom_variable.property3}}
				{{var creditmemo.custom_variable_1}}
			*/
        }else if($type == 'customer') {
            $customerData 	= $observer->getSource();
            $customer 		= $observer->getModel();	/*Magento\Customer\Model\Customer*/
        }
    }
}