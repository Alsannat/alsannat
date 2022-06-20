<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


declare(strict_types=1);

namespace Amasty\Oaction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;

class Commands implements OptionSourceInterface
{
    const ORDERATTR_MODULE_NAME = 'Amasty_Orderattr';

    const COMPOSER_FILE = 'composer.json';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(
        Manager $moduleManager,
        Reader $moduleReader,
        File $filesystem,
        Json $jsonSerializer
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $types = [
            '' => '',
            'amasty_oaction_invoice' => __('Invoice'),
            'amasty_oaction_invoiceship' => __('Invoice > Ship'),
            'amasty_oaction_ship' => __('Ship'),
            'amasty_oaction_status' => __('Change Status'),
            'amasty_oaction_sendtrack' => __('Send Tracking Information')
        ];

        if ($this->moduleManager->isEnabled(self::ORDERATTR_MODULE_NAME)
            && $this->getVersion() > '2.1.6'
        ) {
            $types['amasty_oaction_orderattr'] = __('Update Order Attributes');
        }

        foreach ($types as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        $version = '';
        $info = $this->getModuleInfo();

        if (isset($info['version'])) {
            $version = $info['version'];
        }

        return $version;
    }

    /**
     * @return array
     */
    private function getModuleInfo(): array
    {
        $json = [];

        try {
            $dir = $this->moduleReader->getModuleDir('', self::ORDERATTR_MODULE_NAME);
            $file = $dir . '/' . self::COMPOSER_FILE;
            $string = $this->filesystem->fileGetContents($file);
            $json = $this->jsonSerializer->unserialize($string);
        } catch (FileSystemException $e) {
            null;
        }

        return $json;
    }
}
