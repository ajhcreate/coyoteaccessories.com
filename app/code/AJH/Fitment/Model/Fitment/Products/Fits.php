<?php

namespace AJH\Fitment\Model\Fitment\Products;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\App\Request\Http as RequestInterface;

/**
 * @class Fit
 * @description class that contain all Fitment Fits products
 * 
 */
class Fits extends \Magento\Framework\Model\AbstractModel {

    protected $_coreSession, $_request;

    public function __construct(Context $context, Registry $registry,
            CoreSession $coreSession, RequestInterface $request,
            array $data = []) {

        parent::__construct($context, $registry);

        $this->_request = $request;
        $this->_coreSession = $coreSession;
    }

    /**
     * @method currentlySelectedVehicle
     * @description get selected fitment vehicle from session
     * @return object
     */
    public function currentlySelectedVehicle() {
        $this->_coreSession->start();
        $vehicle = $this->_coreSession->getSelectedVehicle();

        return $vehicle;
    }

}
