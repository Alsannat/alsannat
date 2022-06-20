<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 31/12/2015
 * Time: 15:19
 */

namespace Magenest\GiftCard\Setup;

use Magento\Framework\DB\Ddl\Table;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    private $giftcardSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, GiftCardSetupFactory $giftcardSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->giftcardSetupFactory = $giftcardSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var  $giftcardSetup \Magenest\GiftCard\Setup\GiftCardSetup  */
        $giftcardSetup = $this->giftcardSetupFactory->create(['setup' => $setup]);

        /**
         * Install eav entity types to the eav/entity_type table
         */
        $attributes = [
            'gift_cards' => ['type' => Table::TYPE_TEXT],
            'gift_cards_amount' => ['type' => Table::TYPE_TEXT],
            'base_gift_cards_amount' => ['type' => Table::TYPE_TEXT],

        ];
        foreach ($attributes as $attributeCode => $attributeParams) {
            $giftcardSetup->addAttribute('quote', $attributeCode, $attributeParams);

            $giftcardSetup->addAttribute('quote_address', $attributeCode, $attributeParams);

            $giftcardSetup->addAttribute('order', $attributeCode, $attributeParams);
            $giftcardSetup->addAttribute('invoice', $attributeCode, $attributeParams);
        }

        $giftcardSetup->addAttribute('quote', 'gift_cards_amount_used', ['type'=>Table::TYPE_DECIMAL]);

        $giftcardSetup->addAttribute('quote', 'base_grand_total_without_giftcard', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('quote', 'grand_total_without_giftcard', ['type'=>Table::TYPE_DECIMAL]);
        //base_gift_cards_amount_used
        $giftcardSetup->addAttribute('quote', 'base_gift_cards_amount_used', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('quote_address', 'used_gift_cards', ['type'=>Table::TYPE_DECIMAL]);

        $giftcardSetup->addAttribute('quote_address', 'base_grand_total_without_giftcard', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('quote_address', 'grand_total_without_giftcard', ['type'=>Table::TYPE_DECIMAL]);

        //base_gift_cards_invoiced
        $giftcardSetup->addAttribute('order', 'base_gift_cards_invoiced', ['type'=>Table::TYPE_DECIMAL]);
        //gift_cards_invoiced
        $giftcardSetup->addAttribute('order', 'gift_cards_invoiced', ['type'=>Table::TYPE_DECIMAL]);

        //base_gift_cards_refunded
        $giftcardSetup->addAttribute('order', 'base_gift_cards_refunded', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('order', 'gift_cards_refunded', ['type'=>Table::TYPE_DECIMAL]);

        $giftcardSetup->addAttribute('invoice', 'base_gift_cards_amount', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('invoice', 'gift_cards_amount', ['type'=>Table::TYPE_DECIMAL]);

        $giftcardSetup->addAttribute('creditmemo', 'base_gift_cards_amount', ['type'=>Table::TYPE_DECIMAL]);
        $giftcardSetup->addAttribute('creditmemo', 'gift_cards_amount', ['type'=>Table::TYPE_DECIMAL]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftcard_price_scheme',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 20,
                'label' => 'Giftcard price scheme',
                'input' => 'select',
                'additionalClasses' => 'giftcard_price_scheme',
                'class' => 'giftcard_price_scheme mandatory',
                'source' => 'Magenest\GiftCard\Model\Entity\Attribute\Source\Price\Scheme',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gc_fixed_price',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 90,
                'label' => 'Fixed Price',
                'input' => 'text',
                'additionalClasses' => 'giftcrd-input gc-fixed-price mandatory',
                'class' => 'giftcrd-input gc-fixed-price',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftcard_price_selector',
            [
                'group' => 'Gift Card',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 21,
                'label' => 'Giftcard price selector',
                'input' => 'text',
                'class' => 'giftcard_price_selector',
                'additionalClasses' => 'giftcard_price_selector mandatory',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftcard_allow_message',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'sort_order' => 40,
                'label' => 'Allow Message',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftcard_expired_after',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 50,
                'label' => 'Expires After x days',
                'input' => 'text',
                'class' => 'giftcard_expired_after',
                'additionalClasses' => 'giftcard_expired_after',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gc_is_allow_open_price',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'sort_order' => 60,
                'label' => 'Allow  open price',
                'input' => 'select',
                'class' => 'giftcard-input allow-open-price',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gc_min_price',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 70,
                'label' => 'Min Price',
                'input' => 'text',
                'class' => 'gc-min-price',
                'additionalClasses' => 'gc-min-price mandatory',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gc_max_price',
            [
                'group' => 'Gift Card',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 80,
                'label' => 'Max Price',
                'input' => 'text',
                'class' => 'giftcrd-input gc-max-price',
                'additionalClasses' => 'giftcrd-input gc-max-price mandatory',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftcard_template',
            [
                'group' => 'Gift Card',
                'type' => 'varchar',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'sort_order' => 80,
                'label' => 'Giftcard templates',
                'input' => 'multiselect',
                'class' => 'giftcard_template',
                'additionalClasses' => 'giftcard_template',
                'source' => 'Magenest\GiftCard\Model\Entity\Attribute\Source\Template',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'=>'giftcard'
            ]
        );
        $fieldList = [
            'price'
        ];

        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array('giftcard', $applyTo)) {
                $applyTo[] = 'giftcard';
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }

        $setup->endSetup();
    }
}
