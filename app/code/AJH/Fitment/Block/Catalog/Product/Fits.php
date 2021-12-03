<?php

namespace AJH\Fitment\Block\Catalog\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Catalog\Model\ProductRepository;
use AJH\Fitment\Model\Fitment\Products\Fits as ProductFits;
use AJH\Fitment\Model\Fitment\VehiclePartsFactory;

class Fits extends Template {

    protected $_coreSession;
    protected $_productFits;
    protected $_vehicleParts;
    protected $_productRepository;

    public function __construct(Context $context, CoreSession $coreSession,
            VehiclePartsFactory $vehicleParts, HttpRequest $request,
            ProductRepository $productRepository, ProductFits $productFits) {
        $this->_coreSession = $coreSession;

        $this->_vehicleParts = $vehicleParts;
        $this->_productFits = $productFits;

        $this->_request = $request;

        $this->_productRepository = $productRepository;

        parent::__construct($context);
    }

    public function isPartFits() {
        $fits = [];
        $itemskus = $this->_request->getParam('itemskus');
        $item_ids = $this->_request->getParam('itemids');

        $_itemskus = json_decode($itemskus);
        $_items_ids = json_decode($item_ids);

        /**
         * if product skus are not supplied and only product ids are
         */
        if (!count($_itemskus) && count($_items_ids)) {
            $_itemskus = $this->getProductSkuById($_items_ids);
        }

        foreach ($_itemskus as $sku) {
            $parts = $this->getVehicleParts($sku);
            $fits[$sku] = count($parts);
        }

        return json_encode($fits);
    }

    private function getProductSkuById($ids) {
        $itemskus = [];

        if (is_array($ids) && count($ids)) {
            foreach ($ids as $id) {
                $_product = $this->_productRepository->getById($id);
                array_push($itemskus, $_product->getSku());
            }
        }

        if (!is_array($ids)) {
            $_product = $this->_productRepository->getById($ids);
            array_push($itemskus, $_product->getSku());
        }

        return $itemskus;
    }

    public function getVehicleParts($partnumber) {
        $vehicle_filter = $this->_productFits->currentlySelectedVehicle();

        if (is_array($vehicle_filter)) {
            $year = $vehicle_filter['YearID'];
            $make = $vehicle_filter['MakeID'];
            $model = $vehicle_filter['ModelID'];
            $submodel = $vehicle_filter['SubModelID'];

            $vehicleParts = $this->_vehicleParts->create();
            $collection = $vehicleParts->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->addFieldToFilter('MakeID', ['eq' => $make]);
            $collection->addFieldToFilter('ModelID', ['eq' => $model]);
            $collection->addFieldToFilter('SubModelID', ['eq' => $submodel]);
            $collection->addFieldToFilter('PartNumber', ['eq' => $partnumber]);

            return $collection;
        }

        return null;
    }

}
