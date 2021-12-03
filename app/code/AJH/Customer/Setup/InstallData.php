<?php

namespace AJH\Customer\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerAddressEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
        $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute('customer_address', 'customer_address_fulfillment', [
            'type'          => 'int',
            'label'         => 'Fulfillment',
            'input'         => 'select',
            'required'      =>  false,
            'visible'       =>  true,
            'user_defined'  =>  true,
            'sort_order'    =>  200,
            'position'      =>  200,
            'system'        =>  0,
            'source' => 'AJH\Customer\Model\Config\Source\Options',
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