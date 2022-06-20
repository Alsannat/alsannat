<?php
declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Blacklist extends Action
{
    const ADMIN_RESOURCE = 'Amasty_Acart::acart_blacklist';
}
