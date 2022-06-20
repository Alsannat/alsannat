<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/08/2016
 * Time: 09:42
 */
namespace Magenest\GiftCard\Ui\DataProvider\Product\Modifier\Data;

class Design
{
    protected $artFactory;

    protected $backgroundFactory;

    public function __construct(
        \Magenest\GiftCard\Model\ArtFactory $artFactory,
        \Magenest\GiftCard\Model\BackgroundFactory $backgroundFactoryFactory
    ) {
        $this->artFactory = $artFactory;
        $this->backgroundFactory = $backgroundFactoryFactory;
    }

    /**
     *
     */
    public function getBackgroundForGiftCard($productId)
    {
        $bg=[];
        $collection = $this->backgroundFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
        if ($collection->getSize() > 0) {
       /*     foreach ($collection as $background) {
                $bg[]= [
                    'title' =>$background->getTitle(),
                    'file' =>[
                        0=>[
                           'name' =>$background->getName(),
                            'size' =>$background->getSize(),
                            'status' =>'old',
                            'url'      =>$background->getFile()
                        ]
                    ]
                ];
            }*/
           /* foreach ($collection as $background) {
                $bg[]= [
                    'title' =>$background->getTitle(),
                    'file' =>[
                        0=>[
                           'name' =>$background->getName(),
                            'size' =>$background->getSize(),
                            'status' =>'old',
                            'url'      =>$background->getFile()
                        ]
                    ]
                ];
            } */
            foreach ($collection as $background) {
                $bg[]= [
                    'title' =>$background->getTitle(),
                    'file' =>[
                        0=>[
                           'name' =>$background->getName(),
                            'size' =>$background->getSize(),
                            'status' =>'old',
                            'file'      =>$background->getFile()
                        ]
                    ]
                ];
            }
        }

        return $bg;
    }

    /**
     * get the art for gift card to dispaly in product edit page
     */
    public function getArtForGiftCard($productId)
    {
        $bg=[];
        $collection = $this->artFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
        if ($collection->getSize() > 0) {
            foreach ($collection as $background) {
                $bg[]= [
                    'title' =>$background->getTitle(),
                    'file' =>[
                        0=>[
                            'name' =>$background->getName(),
                            'size' =>$background->getSize(),
                            'status' =>'old',
                            'file'      =>$background->getFile()
                        ]
                    ]
                ];
            }
        }

        return $bg;
    }
}
