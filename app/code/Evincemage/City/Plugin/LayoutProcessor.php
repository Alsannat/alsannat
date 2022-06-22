<?php

namespace Evincemage\City\Plugin;

class LayoutProcessor
{

    public function __construct
    (
        \Evincemage\City\Model\ResourceModel\Grid\CollectionFactory $courierModelFactory,
        \Evincemage\City\Model\ResourceModel\District\CollectionFactory $districtModelFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface
    )
    {
        $this->courierModelFactory = $courierModelFactory;
        $this->districtModelFactory = $districtModelFactory;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
    }
    /**
     * @param LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $result
    ) {

        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $this->getConfig();

        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['district']=[
            'component' => 'Evincemage_City/js/district',
            'config' => [
                'customScope' => 'shippingAddress.district',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'district',
            ],
            'dataScope' => 'shippingAddress.district',
            'label' => __('District / Region'),
            'provider' => 'checkoutProvider',
            'filterBy' => [
                'target' => '${ $.provider }:shippingAddress.city',
                'field' => 'city'
            ],
            'options' => $this->getDistrictList(),
            'visible' => false,
            'validation' => ['required-entry' => false],
            'sortOrder' => 40,
            'id' => 'district',
        ];



        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['district_text'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.district_text',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'district_text',
            ],
            'dataScope' => 'shippingAddress.district_text',
            'label' => __('District / Region'),
            'provider' => 'checkoutProvider',
            /*'filterBy' => [
                'target' => '${ $.provider }:shippingAddress.country_id',
                'field' => 'country_id'
            ],*/
            'options' => [],
            'visible' => true,
            'validation' => ['required-entry' => false],
            'sortOrder' => 41,
            'id' => 'district_text',
        ];

        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['notice'] = __('if you cannot find your city or district please select nearest city, and district from drop down list and write it in below field');

        return $result;
    }

    /**
     * @return $field
     */
    private function getConfig()
    {
        $field = [
            'component' => 'Evincemage_City/js/city',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'city'
            ],
            'label' => 'City',
            'value' => '',
            'dataScope' => 'shippingAddress.city',
            'provider' => 'checkoutProvider',
            'sortOrder' => 25,
            'customEntry' => null,
            'visible' => true,
            'options' => $this->getSaudiCityList(),
            'filterBy' => [
                'target' => '${ $.provider }:${ $.parentScope }.country_id',
                'field' => 'country_id'
            ],
            'validation' => [
                'required-entry' => true
            ],
            'id' => 'city',
            /*'imports' => [
                'initialOptions' => 'index = checkoutProvider:dictionaries.city',
                'setOptions' => 'index = checkoutProvider:dictionaries.city'
            ]*/
        ];


        return $field;
    }

    public function getSaudiCityList()
    {
        $param = "SA";
        $collection = $this->courierModelFactory->create();
        $collection->addFieldToFilter('country_code',  array('eq'=>$param));
        $collection->addFieldToFilter('store_ids',  array('in'=>[$this->_storeManager->getStore()->getId(),"0"]));
        $response =[];
        //$response[] = array('value'=> $this->_storeManager->getStore()->getId(), 'label' => $this->_storeManager->getStore()->getId(), 'country_id'=> $param);
        $response[] = array('value' => '', 'label' => __('City'), 'country_id'=> $param);
        $currentStoreLanguage = $this->_urlInterface->getCurrentUrl();
        if ($this->_storeManager->getStore()->getId()!="1")
        {
            foreach ($collection as $city)
            {
                $response[] = array('value'=>$city->getNaquelCity(),'label' => $city->getCityEn(), 'country_id'=> $param);     
            }
        }
        else
        {
            foreach ($collection as $city)
            {
                $response[] = array('value'=>$city->getNaquelCity(),'label' => $city->getCityAr(), 'country_id'=> $param);     
            }

        }
        return $response;

    }

    public function getDistrictList()
    {

        $collection = $this->districtModelFactory->create();
        //$collection->addFieldToFilter('naquel_city',  array('eq'=>$param));
        //$collection->addFieldToFilter('store_ids',  array('eq'=>$this->_storeManager->getStore()->getId()));
        $response =[];
        $response[] = array('value' => '', 'label' => __('District / Region'));
        $currentStoreLanguage = $this->_urlInterface->getCurrentUrl();
        if ($this->_storeManager->getStore()->getId()!="1")
        {
            foreach ($collection as $district)
            {
                //$response[] = array('value'=>$district->getNaquelDistCode(),'label' => $district->getEnDistrictName(),'city'=>$district->getNaquelCity());
                $response[] = array('value'=>$district->getEnDistrictName(),'label' => $district->getEnDistrictName(),'city'=>$district->getNaquelCity());     
            }
        }
        else
        {
            foreach ($collection as $district)
            {
                //$response[] = array('value'=>$district->getNaquelDistCode(),'label' => $district->getArDistrictName(),'city'=>$district->getNaquelCity());
                $response[] = array('value'=>$district->getEnDistrictName(),'label' => $district->getArDistrictName(),'city'=>$district->getNaquelCity());     
            }
        }

        return $response;
    }
}
