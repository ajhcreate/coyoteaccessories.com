<?php

namespace AJH\ProductVehicle\Block\Product\View;

/**
 * Magento\Framework\Registry (deprecated since Magento 2.3)
 */
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use AJH\ProductVehicle\Helper\Data as DataHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime as CoreDate;
use AJH\ProductVehicle\Model\ResourceModel\Partclass\Collection as PartclassCollection;
use AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms\Collection as VehiclestpmsCollection;
//use Inchoo\Search\Helper\Search as InchooSearchHelper;
use Magento\Framework\App\ResourceConnection\ConnectionFactory;

class Vehicle extends Template {

    protected $_helper, $_productRepository, $_vehiclestpmsCollection, $_customerSession, $_partclassCollection, $_date, $_registry, $_inchooSearchHelper;
    protected $_connectionFactory;

    /**
     * 
     * @param Context $context
     * @param DataHelper $helper
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param VehiclestpmsCollection $vehiclestpmsCollection
     * @param PartclassCollection $partclassCollection
     * @param CustomerSession $customerSession
     * @param CoreDate $date
     * @param ConnectionFactory $connectionFactory
     */
    public function __construct(Context $context, DataHelper $helper, Registry $registry, ProductRepositoryInterface $productRepository, VehiclestpmsCollection $vehiclestpmsCollection, PartclassCollection $partclassCollection, CustomerSession $customerSession, CoreDate $date, ConnectionFactory $connectionFactory) {
        parent::__construct($context);

        $collection = $this->getProductVehicles();
        $this->setCollection($collection);

        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;

        $this->_vehiclestpmsCollection = $vehiclestpmsCollection;
        $this->_customerSession = $customerSession;

        $this->_partclassCollection = $partclassCollection;

        $this->_date = $date;
//        $this->_inchooSearchHelper = $inchooSearchHelper;

        $this->_connectionFactory = $connectionFactory;
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('AJH\ProductVehicle\Pager', 'custom.pager');

        $pager->setTemplate('page/html/vehiclepager.phtml');
        $pager->setAvailableLimit(array(20 => 20));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     *
     */
    protected function _construct() {
        
        /**
         * forced true 
         */
        $isSearchEnabled = TRUE; //$this->_helper->isEnabled();
        if ($isSearchEnabled) {
            $this->setTemplate('AJH_ProductVehicle::catalog/product/view/vehicle/list.phtml');
        }
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags() {
        return array_merge(parent::getCacheTags(), $this->getProduct()->getCacheIdTags());
    }

    public function getProductId() {
        return $this->getRequest()->getParam('id');
    }

    /**
     * Retrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (!$this->_registry->registry('product') && $this->getProductId()) {
            $product = $this->_productRepository->load($this->getProductId());
            $this->_registry->register('product', $product);
        }
        return $this->_registry->registry('product');
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        } elseif ($productSku = $this->getRequest()->getParam('productsku')) {
            return $productSku;
        }
    }

    public function getDbConnection() {
//        $config = Mage::getConfig()->getResourceConnectionConfig('externaldb_database');
//        $dbConfig = array(
//            'host' => $config->host,
//            'username' => $config->username,
//            'password' => $config->password,
//            'dbname' => $config->dbname
//        );

        $dbConfig = $this->_connectionFactory->create(array(
            'host' => '69.27.38.57',
            'dbname' => 'rscadmin_revo51',
            'username' => 'magento',
            'password' => 'M@63nt0!#',
            'active' => '1'
        ));

        $read = \Zend_Db::factory('Pdo_Mysql', $dbConfig);
        return $read;
    }

    public function getVehicleLabels() {
        $params = $this->getRequest()->getParams();
        if (isset($params['make']) && isset($params['model']) && isset($params['submodel']) && isset($params['year'])) {
            $make = $params['make'];
            $model = $params['model'];
            $submodel = $params['submodel'];
            $year = $params['year'];
            $read = $this->getDbConnection();
            $makename = $read->fetchCol(sprintf('SELECT MakeName FROM vwvehiclestpms'
                            . ' WHERE YearID=%d AND MakeID=%d'
                            . ' GROUP BY MakeID', $year, $make
            ));
            $modelname = $read->fetchCol(sprintf('SELECT ModelName FROM vwvehiclestpms'
                            . ' WHERE YearID=%d AND MakeID=%d AND ModelID=%d'
                            . ' GROUP BY ModelID', $year, $make, $model
            ));
            $submodelname = $read->fetchCol(sprintf('SELECT SubModelName'
                            . ' FROM vwvehiclestpms'
                            . ' WHERE YearID=%d AND MakeID=%d AND ModelID=%d AND SubModelID=%d'
                            . ' GROUP BY SubModelID', $year, $make, $model, $submodel
            ));
            return array(
                'make' => $makename,
                'model' => $modelname,
                'submodel' => $submodelname
            );
        }
        return array();
    }

    public function getProductVehicles() {
        $sku = $this->getProductSku();
//        $productVehicles = Mage::getResourceModel('ajh_productvehicle/vehiclestpms_collection')
        $productVehicles = $this->_vehiclestpmsCollection->create()
                ->getVehicleInfoByPartNumberWithProtocol($sku);
        $params = $this->getRequest()->getParams();
        $productMyGarage = $this->_customerSession->getProductMyGarage();
        if ($productVehicles->getSize() > 0 && (isset($params['make']) && !empty($params['make'])) && (isset($params['model']) && !empty($params['model'])) && (isset($params['submodel']) && !empty($params['submodel'])) && (isset($params['year']) && !empty($params['year']))) {
            $make = $params['make'];
            $model = $params['model'];
            $submodel = $params['submodel'];
            $year = $params['year'];
            $vehicleLabels = $this->getVehicleLabels();
            $makeName = $vehicleLabels['make'][0];
            $submodelName = $vehicleLabels['submodel'][0];
            $modelName = $vehicleLabels['model'][0];
            $productMyGarage["year=$year&make=$make&model=$model&submodel=$submodel"] = "$year $makeName $modelName $submodelName";
            $productMyGarage = array_unique($productMyGarage);
            $this->_customerSession->setProductMyGarage($productMyGarage);
        }
        return $productVehicles;
    }

    public function getPartClassId() {
        $partClass = $this->getPartClassInfoByMagentoId();
        return $partClass->getData('ID');
    }

    public function getPartClassInfoByMagentoId() {
        $magentoPartClass = $this->getProduct()->getData('web_class');
        return $this->_partclassCollection->getPartClassByMagentoPartClassId($magentoPartClass);
    }

    public function getCurrentStoreDate() {
        return $this->_date->date('m/d/Y');
    }

    public function getFormatDate($date) {
        if ($date) {
            $dateTimestamp = $this->_date->timestamp(strtotime($date));
            return date('m/d/Y', $dateTimestamp);
        }
        return '';
    }

    public function getOutputByDate($date, $currentStoreDate) {
        if ($date) {
            if (strtotime($date) > strtotime($currentStoreDate)) {
                return '<td class="pw data"><span>' . $date . '</span></td>';
            } else {
                return '<td class="pw data success-msg"></td>';
            }
        }
        return '<td class="pw data">N/A</td>';
    }

}
