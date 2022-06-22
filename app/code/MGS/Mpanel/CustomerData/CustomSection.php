<?php
namespace MGS\Mpanel\CustomerData;
use Magento\Customer\CustomerData\SectionSourceInterface;

class CustomSection implements SectionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helperwishlist =  $objectManager->get('Magento\Wishlist\Helper\Data');
        $wishcount = $helperwishlist->getItemCount();
        $helperguestwishlist =  $objectManager->get('MGS\Guestwishlist\Helper\Data');
          $guestcount = $helperguestwishlist->getItemCount();

        return [
            'wish' =>$wishcount ,
            'guest' => $guestcount,
        ];
    }
}