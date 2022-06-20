<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
	private $eavSetupFactory;

	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}
	
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/**
		 * Add New custom attribute for product customscommoditycode
		 */	
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		 
		 if(version_compare($context->getVersion(), '1.0.0', '<'))
		 {
			if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'customscommoditycode'))
			{

				$eavSetup->addAttribute(
					\Magento\Catalog\Model\Product::ENTITY,
					'customscommoditycode',
					[
						'type' => 'text',
						'backend' => '',
						'frontend' => '',
						'label' => 'Customs Commodity Code',
						'input' => 'text',
						'class' => '',
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
						'used_in_product_listing' => true,
						'unique' => false,
						'apply_to' => ''
					]
				);		
	  
			}
		}	
		
	}
}