<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


declare(strict_types=1);

namespace Amasty\Oaction\Model\Command;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Invoiceship extends Invoice
{
    /**
     * @param AbstractCollection $collection
     * @param int                $notifyCustomer
     * @param array              $oaction
     *
     * @return string
     */
    public function execute(AbstractCollection $collection, int $notifyCustomer, array $oaction): string
    {
        $success = parent::execute($collection, $notifyCustomer, $oaction);
        $command = $this->objectManager->create(Ship::class);
        $success .= '||' . $command->execute($collection, $notifyCustomer, $oaction);

        return $success;
    }
}
