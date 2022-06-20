<?php
    /**
     * Webkul Software.
     *
     * @category  Webkul
     * @package   Webkul_GoogleMapPinAddress
     * @author    Webkul Software Private Limited
     * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
     * @license   https://store.webkul.com/license.html
     */

namespace Webkul\GoogleMapPinAddress\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * process
     *
     * @param array $result
     * @return array $result
     */
    
    public function process($result)
    {
        $moduleStatus =  $this->scopeConfig->getValue('googlemappinaddress/gmpa_settings/active');
        if ($moduleStatus) {
            $result = $this->getShippingFormFields($result);
            $result = $this->getBillingFormFields($result);
            return $result;
        } else {
            return $result;
        }
    }
    /**
     * Get Additional Fields
     *
     * @param string $addressType
     * @return array
     */

    public function getAdditionalFields($addressType = 'shipping')
    {
        if ($addressType=='shipping') {
            return ['latitude','longitude'];
        }
        return  ['latitude','longitude'];
    }

    /**
     * Get Shipping Form Fields
     *
     * @param array $result
     * @return array $result
     */

    public function getShippingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset'])
        ) {

            $shippingPostcodeFields = $this->getFields('shippingAddress.custom_attributes', 'shipping');
            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            if (isset($shippingFields['street'])) {
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }

            $shippingFields = array_replace_recursive($shippingFields, $shippingPostcodeFields);
            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;

        }

        return $result;
    }

    /**
     * Get Billing Form Fields
     *
     * @param array $result
     * @return array $result
     */

    public function getBillingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'])) {

            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {

                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }

                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getFields('billingAddress' . $paymentMethodCode .
                '.custom_attributes', 'billing');

                $billingFields = array_replace_recursive($billingFields, $billingPostcodeFields);

                $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']
                ['children']['form-fields']['children'] = $billingFields;
            }
        }

        return $result;
    }

    /**
     * Get Fields
     *
     * @return array $fields
     */

    public function getFields($scope, $addressType)
    {
        $fields = [];
         $i = 1000;
        foreach ($this->getAdditionalFields($addressType) as $field) {
            $fields[$field] = $this->getField($field, $scope, $i);
            $i++;
        }
        return $fields;
    }

    /**
     * Get Field
     *
     * @return array $field
     */

    public function getField($attributeCode, $scope, $i)
    {
        $field = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => $scope,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => $scope . '.'.$attributeCode,
            'label' => $attributeCode,
            'provider' => 'checkoutProvider',
            'sortOrder' => $i,
            'validation' => [
                 'required-entry' => true
                         ],
             'options' => [],
                 'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => ''
        ];

        return $field;
    }
}
