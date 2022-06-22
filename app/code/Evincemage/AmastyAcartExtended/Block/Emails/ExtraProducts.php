<?php

namespace Evincemage\AmastyAcartExtended\Block\Emails;

use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;

class ExtraProducts extends LinkExtended
{
    /**
     * @return ProductLinkFactory
     */
    protected function getLinkModel()
    {
        return $this->productLinkFactory->create()->useCrossSellLinks();
    }
}
