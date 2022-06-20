<?php

namespace Amasty\Acart\Block\Email\Items;

use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;

class Related extends Link
{
    /**
     * @return ProductLinkFactory
     */
    protected function getLinkModel()
    {
        return $this->productLinkFactory->create()->useRelatedLinks();
    }
}
