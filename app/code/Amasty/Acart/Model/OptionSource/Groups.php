<?php
declare(strict_types=1);

namespace Amasty\Acart\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

class Groups implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $customerGroupCollectionFactory;

    public function __construct(
        CollectionFactory $customerGroupCollectionFactory
    ) {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    public function toOptionArray(): array
    {
        return $this->customerGroupCollectionFactory->create()->toOptionArray();
    }
}
