<?php

namespace Custom\City\Controller\Index;
use Custom\City\Controller\City;
class Listcities extends City
{
    /*
     * Get Cities and return in json format
     */
    public function execute()
    {
        $stateId = $this->getRequest()->getParam('state');
        $countryId = $this->getRequest()->getParam('country_id');
        $cities = $this->getCitiesByState($countryId,$stateId);
        $result = $this->resultJsonFactory->create();
        $result->setData($cities);
        return $result;
    }
}