<?php

namespace Custom\City\Controller\Index;

use Custom\City\Controller\Zip;

class Zips extends Zip
{
    /**
     * Get zip codes and return json
     * @return mixed
     */
    public function execute()
    {
        $city = $this->getRequest()->getParam('city');
        $stateId = $this->getRequest()->getParam('state');
        $countryId = $this->getRequest()->getParam('country_id');
        $result = $this->resultJsonFactory->create();
		$zip_codes_options = $this->getZipsByCity($city,$countryId,$stateId) ;
        $result->setData($zip_codes_options);
        return $result;
    }
}