<?php
namespace Alsannat\RatingToProduct\Observer;

class RatingToProduct implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mageplaza\BetterProductReviews\Block\Review\Summary $reviewSummary
    )
    {
        $this->productRepository = $productRepository;
        $this->reviewSummary = $reviewSummary;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $reviewObject = $observer->getEvent()->getObject();
        // $data = $object->getData();
        // print_r($data); die;

        if($reviewObject->getStatusId() == 1) // Approved case
        {
		$proId = (int)$reviewObject->getEntityPkValue();
            $proObj = $this->productRepository->getById($proId);

            $_ratingSummary = $this->reviewSummary->getRatingSummary($proObj);
            $rating = number_format((float)($_ratingSummary / 20), 1);
            
            $proObj->setFeedRating($rating);
	    $this->productRepository->save($proObj); 
        }
    }
}
