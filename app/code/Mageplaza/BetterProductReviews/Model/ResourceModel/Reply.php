<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_BetterProductReviews
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterProductReviews\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\BetterProductReviews\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Reply
 *
 * @package Mageplaza\BetterProductReviews\Model\ResourceModel
 */
class Reply extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * Review Detail table
     *
     * @var string
     */
    protected $_reviewDetailTable;

    /**
     * @var Data
     */
    protected $_reviewHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * Reply constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param Data $_reviewHelper
     * @param CustomerRepositoryInterface $_customerRepository
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        Data $_reviewHelper,
        CustomerRepositoryInterface $_customerRepository,
        $connectionName = null
    ) {
        $this->_dateTime = $dateTime;
        $this->_reviewHelper = $_reviewHelper;
        $this->_customerRepository = $_customerRepository;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_betterproductreviews_review_reply', 'reply_id');
        $this->_reviewDetailTable = $this->getTable('review_detail');
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getReplyCreatedAt()) {
            $object->setReplyCreatedAt($this->_dateTime->date());
        }

        $object->setReplyUpdatedAt($this->_dateTime->date());

        return $this;
    }

    /**
     * @param string $reviewId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getReplyByReviewId($reviewId)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('review_id = :review_id');
        $binds   = ['review_id' => (int) $reviewId];

        return $adapter->fetchRow($select, $binds);
    }

    /**
     * @param string $reviewId
     * @param array $data
     */
    public function saveReviewExtraFields($reviewId, $data)
    {
        $flag = $this->_reviewHelper->isPurchaserGuest($data['entity_pk_value'],$data['mp_bpr_email']);
        $customer = $this->_customerRepository->get($data['mp_bpr_email']);
        $customerId = $customer->getId();

        $connection = $this->getConnection();
        $detail     = [];
        /**
         * save detail
         */
        if (isset($data['mp_bpr_helpful'])) {
            $detail['mp_bpr_helpful'] = $data['mp_bpr_helpful'];
        }
        if (isset($data['mp_bpr_helpful_no'])) {
            $detail['mp_bpr_helpful_no'] = $data['mp_bpr_helpful_no'];
        }
        if (isset($data['mp_bpr_images'])) {
            $detail['mp_bpr_images'] = $data['mp_bpr_images'];
        }
        if (isset($data['mp_bpr_recommended_product'])) {
            $detail['mp_bpr_recommended_product'] = $data['mp_bpr_recommended_product'];
        }
        if (isset($data['mp_bpr_verified_buyer'])) {
            $detail['mp_bpr_verified_buyer'] = $data['mp_bpr_verified_buyer'];
        }
        if (isset($data['mp_bpr_verified_buyer']) && $data['mp_bpr_verified_buyer'] == false && $flag == true) {
            $detail['mp_bpr_verified_buyer'] = $data['mp_bpr_verified_buyer'] = 1;
        }
        if($data['customer_id'] == null && $customerId != null){
            $detail['customer_id'] = $data['customer_id'] = $customerId;
        }
        if (isset($data['mp_bpr_email'])) {
            $detail['mp_bpr_email'] = $data['mp_bpr_email'];
        }
        if (isset($data['mp_bpr_location'])) {
            $detail['mp_bpr_location'] = $data['mp_bpr_location'];
        }

        $select   = $connection->select()
            ->from($this->_reviewDetailTable, 'detail_id')->where('review_id = :review_id');
        $detailId = $connection->fetchOne($select, [':review_id' => $reviewId]);
        if ($detailId && $detail) {
            $condition = ['detail_id = ?' => $detailId];
            $connection->update($this->_reviewDetailTable, $detail, $condition);
        }
    }
}
