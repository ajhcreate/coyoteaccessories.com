<?php 

namespace AJH\D2R\Setup; 

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactor
     */
    private $attributeSetFactory;    

    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }    

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
         /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'reset_tool_use', [
            'type' => 'int',
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
            'adminhtml_only'=>1,
            'default'=>0
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'reset_tool_use')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],
        ]);

        $attribute->save();
    }
}