<?php

namespace Webengage\Event\Observer;

use Psr\Log\LoggerInterface;
use Webengage\Event\Helper\Data;

class Catalogsearchresetsearchresultwe implements \Magento\Framework\Event\ObserverInterface
{


    /**
     * Catalogsearchresetsearchresultwe constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!isset($_GET['uenc']) && isset($_GET['q'])) {

            if ( isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') {

                $prepareJson = array(
                        'event_name' => 'Searched',
                        'event_data' => array(
                            'searchKeyword' => $_REQUEST['q'],
                            'searchResult' => null,
                            'totalProducts' => null,
                        )
                );

                    /*Calling WE API*/
                    $this->helper->apiCallToWebengage($prepareJson);
                    /*Calling WE API*/

                    return $this;
            }
        }
    }
}
