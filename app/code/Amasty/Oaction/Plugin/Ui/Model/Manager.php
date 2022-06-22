<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


declare(strict_types=1);

namespace Amasty\Oaction\Plugin\Ui\Model;

use Magento\Ui\Model\Manager as UiManager;

class Manager extends AbstractReader
{
    /**
     * @param UiManager $subject
     * @param array     $result
     *
     * @return array
     */
    public function afterGetData(UiManager $subject, array $result): array
    {
        return $this->updateMassactions($result);
    }
}
