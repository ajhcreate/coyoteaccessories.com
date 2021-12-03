<?php

namespace AJH\D2R\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class UpgradeData implements UpgradeDataInterface {

    private $customerSetupFactory;
    private $attributeSetFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory,
            AttributeSetFactory $attributeSetFactory) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup,
            ModuleContextInterface $context) {
        if (version_compare($context->getVersion(), '1.1.9', '<')) {
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(Customer::ENTITY, 'reset_tool_use', [
                'type' => 'varchar',
                'label' => 'Reset Tool Use',
                'input' => 'select',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 1001,
                'position' => 1001,
                'is_used_in_grid' => true,
                'system' => 0,
                'source' => 'AJH\D2R\Model\Entity\Attribute\Source\Tools',
                'adminhtml_only' => 1,
                'default' => 0
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'reset_tool_use')
                    ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer'],
            ]);

            $attribute->save();

            //////////////////////////////////

            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerAddressEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
            $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute('customer_address', 'latlong', [
                'type' => 'varchar',
                'label' => 'Retailer Address Coordinate (Latitude,Longitude)',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 200,
                'position' => 200,
                'system' => 0,
                'source' => '',
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'latlong')
                    ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer_address', 'customer_account_create', 'customer_account_edit', 'customer_address_edit', 'customer_register_address'],
            ]);

            $attribute->save();
            
            ////////////////////////////////
            
        }
    }

}
