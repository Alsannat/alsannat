<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Model\System\Config\Source;

class CollectionMethod
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'unsolicited', 'label' => 'Unsolicited'],
            ['value' => 'post_fulfillment', 'label' => 'Post fulfillment']
        ];
        
        return $data;
    }
}
