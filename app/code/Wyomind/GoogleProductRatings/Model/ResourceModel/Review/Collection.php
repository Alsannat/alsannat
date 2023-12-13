<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Model\ResourceModel\Review;

class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Get storeviews reviews
     *
     * @param array $storeIds
     * @return array
     */
    public function getReviews($storeIds)
    {
        $storeList = implode(',', $storeIds);
        $connection = $this->getConnection();
        $mainTable = $this->getMainTable();
        $reviewStoreTable = $this->getReviewStoreTable();
        $reviewDetailTable = $this->getReviewDetailTable();
        $ratingOptionVoteTable = $this->_resource->getTable('rating_option_vote');
        
        $query = $connection->select()
                            ->distinct('review_id')
                            ->from(['r' => $mainTable])
                            ->joinLeft(['rs' => $reviewStoreTable], 'rs.review_id=r.review_id')
                            ->joinLeft(['rd' => $reviewDetailTable], 'rd.review_id=r.review_id')
                            ->joinLeft(['rov' => $ratingOptionVoteTable], 'rov.review_id=r.review_id', 'ROUND(AVG(rov.value),2) AS score')
                            ->where('status_id=1 AND entity_id=1 AND FIND_IN_SET(rs.store_id, "' . $storeList . '")')
                            ->group('r.review_id');
        
        $reviews = $connection->fetchAll($query);
        
        return $reviews;
    }
}
