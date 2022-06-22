<?php

declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\BlacklistSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class BlacklistSearchResults extends SearchResults implements BlacklistSearchResultsInterface
{
}
