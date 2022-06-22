<?php
declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate\ProcessorPool;

use Magento\Framework\Mail\TemplateInterface;

interface LegacyTemplateProcessorInterface
{
    public function execute(TemplateInterface $template);
}
