<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class Attribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_includedAttributes = ['visibility', 'image', 'tax_class_id'];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @var \Magento\Framework\Validator\UniversalFactory
     */
    protected $_universalFactory = null;

    /**
     * @var Config|null
     */
    protected $_configHelper = null;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_universalFactory = $universalFactory;
        $this->_configHelper = $configHelper;
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isBool(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getSourceModel() == 'eav/entity_attribute_source_boolean'
            || $attribute->getFrontendInput() == 'boolean';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isDate(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getBackendType() == 'datetime';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isAttributeUsingOptions(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $model = !$attribute->getSourceModel() ?: $this->_universalFactory->create($attribute->getSourceModel());
        $backend = $attribute->getBackendType();

        return $attribute->usesSource()
            && ($backend == 'int' && $model instanceof \Magento\Eav\Model\Entity\Attribute\Source\Table)
            || ($backend == 'varchar' && $attribute->getFrontendInput() == 'multiselect');
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isDecimal(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getBackendType() == 'decimal'
            || $attribute->getFrontendClass() == 'validate-number';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isImage(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getFrontendInput() == 'media_image';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isInteger(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->usesSource() && $attribute->getBackendType() == 'int'
            || $attribute->getFrontendClass() == 'validate-digits';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isBaseImage(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        return $attribute->getFrontendInput() == 'media_base_image';
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    public function isAttributeIndexable(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $indexableAttributes = $this->_configHelper->getEntitySearchableAttributes("product");

        return (array_key_exists($attribute->getAttributeCode(), $indexableAttributes) && $indexableAttributes[$attribute->getAttributeCode()]['c'] == "1")
            || in_array($attribute->getAttributeCode(), $this->_includedAttributes);
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param $value
     * @param mixed $store
     * @return mixed
     */
    public function formatAttributeValue(\Magento\Eav\Model\Entity\Attribute $attribute, $value, $store = null)
    {
        $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\Standard::class;

        if ($this->isDecimal($attribute)) {
            $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\Decimal::class;
        } elseif ($this->isBool($attribute)) {
            $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\Boolean::class;
        } elseif ($this->isInteger($attribute)) {
            $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\Integer::class;
        } elseif ($this->isImage($attribute)) {
            $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\Image::class;
        } elseif ($this->isBaseImage($attribute)) {
            $instanceName = \Wyomind\ElasticsearchCore\Model\Formatter\BaseImage::class;
        }

        $formatter = $this->_objectManager->create($instanceName);

        return $formatter->format($value, $store);
    }
}