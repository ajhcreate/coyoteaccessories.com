<?php

namespace AJH\D2R\Model\ResourceModel\Distributor;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AJH\D2R\Model\ResourceModel\Distributor as ResourceModel;
use AJH\D2R\Model\Distributor as Model;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends AbstractCollection {

    protected $_storeManager;

//    public function __construct(StoreManagerInterface $storeManager) {
//        $this->_storeManager = $storeManager;
//    }

    /**
     * Define model & resource model
     */
    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

    protected function _beforeLoad() {

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Store\Model\StoreManagerInterface');        

        $websiteID = $manager->getStore()->getWebsiteId();

        //if orotek website
//        if (intval($websiteID) === 4) {
//            $companyID = 2;
//
//            //if pdq or others
//        } else {
            $companyID = 3;
//        }                        

        //if @ admin
        if (intval($websiteID) === 1) {
            $this->addFieldToFilter('CompanyID', ['in' => array(2, 3)]);
        } else {
            $this->addFieldToFilter('CompanyID', ['eq' => $companyID]);
        }

        return parent::_beforeLoad();
    }

    public function toOptionArray() {
        $res = [];

        $this->setOrder('Name', 'ASC');

        $res[0] = "Select Distributor";
        foreach ($this->getItems() as $item) {
            $res[$item['ID']] = sprintf('%s, %s,%s', $item['Name'], $item['City'], $item['State']);
        }
                

        return $res;
    }

}
