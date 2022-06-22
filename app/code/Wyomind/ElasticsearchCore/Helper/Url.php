<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MANAGE_INDEXES = 'elasticsearchcore/indexes/index';
    const RUN_URL = 'elasticsearchcore/indexes/run';
    const FLUSH_URL = 'elasticsearchcore/indexes/flush';
    const RAW_DATA_URL = 'elasticsearchcore/browse/raw';
    const TEST_CALLBACK_URL = 'elasticsearchcore/servers/test';
}