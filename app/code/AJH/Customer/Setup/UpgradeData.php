<?php

namespace AJH\Customer\Setup;

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
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerAddressEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
            $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute('customer_address', 'customer_address_fulfillment', [
                'type' => 'varchar',
                'label' => 'Fulfillment',
                'input' => 'select',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 200,
                'position' => 200,
                'system' => 0,
                'source' => 'AJH\Customer\Model\Config\Source\FulfillmentOptions',
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'customer_address_fulfillment')
                    ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer_address'],
            ]);

            $attribute->save();
        }
    }

}
