<?php

namespace Vnecoms\PdfProItem\Helper;
use Magento\Framework\App\Helper\Context as Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }
}