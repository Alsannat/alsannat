<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/01/2016
 * Time: 10:24
 */

namespace Magenest\GiftCard\Setup;

class GiftCardSetup extends \Magento\Quote\Setup\QuoteSetup
{
    /**
     * List of entities converted from EAV to flat data structure
     *
     * @var $_flatEntityTables array
     */
    protected $_flatEntityTables = [
        'quote' => 'quote',
        'quote_item' => 'quote_item',
        'quote_address' => 'quote_address',
        'quote_address_item' => 'quote_address_item',
        'quote_address_rate' => 'quote_shipping_rate',
        'quote_payment' => 'quote_payment',
        'order'=>'sales_order',
        'invoice'=>'sales_invoice',
        'creditmemo'=>'`sales_creditmemo`'
    ];
}
