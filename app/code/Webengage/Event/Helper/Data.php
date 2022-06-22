<?php

namespace Webengage\Event\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{
    protected $logger;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeMḁnager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param LoggerInterface                           $logger
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeMḁnager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\SessionFactory $customerSession,
        LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->session = $session;
        $this->storeMḁnager = $storeMḁnager;

        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param $jsonPayload
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apiCallToWebengage($jsonPayload)
    {
        $readConnection = $this->resourceConnection->getConnection();

        $checkRegion = 'SELECT * FROM `webengage_configuration` WHERE `wekey` = "we_region" limit 1';
        $getRegion = $readConnection->fetchAll($checkRegion);

        $apiUrl = !empty($getRegion) && $getRegion[0]['wevalue'] == 'india' ? 'https://c.in.webengage.com/m2.jpg' : 'https://c.webengage.com/m2.jpg';

        /*Adding Common Array Values For system Data*/
        $dateTime = '~t'.date('c');

        $customerEmail = isset($jsonPayload['cuid']) && !empty($jsonPayload['cuid']) ? $jsonPayload['cuid'] : null;
        $customerSession = $this->customerSession->create();
        if ($customerSession->isLoggedIn()) {
            $customerEmail = $customerSession->getCustomer()->getEmail();
        }

        $luid = $this->session->getLuid();
        $suid = $this->session->getSuid();
        $licenceCode = $this->getLicenceInfo(); // Fetched Licence Info

        $jsonPayloadDefault = array('license_code' => $licenceCode, 'cuid' => $customerEmail, 'luid' => $luid, 'suid' => $suid, 'category' => 'application', 'event_time' => $dateTime);
        foreach ($jsonPayloadDefault as $k => $v) {
            $jsonPayload[$k] = $v;
        }

        /*Adding Common Store Info*/
        $jsonPayload['event_data']['storeName'] = $this->storeMḁnager->getStore()->getName();
        $jsonPayload['event_data']['storeCode'] = $this->storeMḁnager->getStore()->getCode();
        /*Adding Common Store Info*/

        $jsonPayload['system_data'] = array('sdk_id' => 1, 'ip' => $this->get_client_ip(), 'user-agent' => isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        /*Adding Common Array Values For system Data*/

        $jsonPayload = array($jsonPayload);

        $checkDebugFlag = 'SELECT * FROM `webengage_configuration` WHERE `wekey` = "we_debug" limit 1';
        $getDebugFlag = $readConnection->fetchAll($checkDebugFlag);

        if (!empty($getDebugFlag)) {
            if (!defined('DEBUGFLAG')) {
                define('DEBUGFLAG', $getDebugFlag[0]['wevalue']);
            }
        }

        $jsonPayload = json_encode($jsonPayload);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/transit+json',
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (defined('DEBUGFLAG') && DEBUGFLAG == 'yes') {
            if (curl_error($curl)) {
                $this->logger->debug('cURL error - '.curl_error($curl));
            } else {
                $this->logger->debug("WebEngage Payload Data - $jsonPayload - HTTPRESP - $httpcode");
            }
            if ($response === false) {
                $this->logger->debug('$response is false - '.curl_error($curl));
            }
        }

        curl_close($curl);
    }

    /**
     * @return string
     */
    public function get_client_ip()
    {
        foreach (array('HTTP_CLIENT_IP',
                     'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED',
                     'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR',
                     'HTTP_FORWARDED',
                     'REMOTE_ADDR', ) as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $IPaddress) {
                    $IPaddress = trim($IPaddress); // Just to be safe
                    return $IPaddress;
                    if (filter_var($IPaddress,
                            FILTER_VALIDATE_IP,
                            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                        !== false) {
                        return $IPaddress;
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getLicenceInfo()
    {
        $readConnection = $this->resourceConnection->getConnection();
        $checkTableExists = 'Show tables like "webengage_configuration"';
        $exists = $readConnection->fetchAll($checkTableExists);
        $licenceCode = '';

        if ($exists) {
            $checkData = 'SELECT * FROM `webengage_configuration` WHERE `wekey` = "we_licence_code" limit 1';
            $getData = $readConnection->fetchAll($checkData);
            if (!empty($getData)) {
                $licenceCode = $getData[0]['wevalue'];
                $hasTilde = strpos($licenceCode, '~');
                if (!is_bool($hasTilde) && $hasTilde == 0) {
                    $licenceCode = '~'.$licenceCode;
                }
            }
        }

        return $licenceCode;
    }

    public function getProductCategories($product) {
        // Get IDs and check if categories are present
        $categoryIds = $product->getCategoryIds();
        if (!$categoryIds) {
            return "";
        }

        // Fetch collection
        $categoryCollection = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')
            ->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $categoryIds)
            ->addIsActiveFilter();

        // Prepare string
        $categories = array();
        foreach ($categoryCollection as $category) {
            array_push($categories, $category->getName());
        }

        return implode(",", $categories);
    }
}
