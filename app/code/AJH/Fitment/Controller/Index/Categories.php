<?php

namespace AJH\Fitment\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;

use AJH\Fitment\Helper\Data as HelperData;
use AJH\FitmentUrlRewrite\Model\UrlRewrite;

class Categories extends \Magento\Framework\App\Action\Action {

    protected $_request;
    protected $_urlRewrite;
    protected $_eventManager;
    protected $_resultPageFactory;
    protected $_resultJsonFactory;
    protected $_year, $_make, $_model, $_submodel, $_qualifier, $_qualifier2, $_criteria;
    protected $_helper;

    public function __construct(
    Context $context, PageFactory $pageFactory, RequestInterface $request,
            JsonFactory $resultJsonFactory, UrlRewrite $urlRewrite,
            EventManager $eventManager, HelperData $helper) {

        $this->_request = $request;
        $this->_eventManager = $eventManager;
        $this->_resultPageFactory = $pageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_urlRewrite = $urlRewrite;

        $this->_helper = $helper;

        return parent::__construct($context);
    }

    public function execute() {
        $resultJson = $this->_resultJsonFactory->create();

        /**
         * todo: change this and load params value from fitment model
         */
        $this->_year = $this->getRequest()->getParam('year');
        $this->_make = $this->getRequest()->getParam('make');
        $this->_model = $this->getRequest()->getParam('model');
        $this->_submodel = $this->getRequest()->getParam('submodel');
        $this->_criteria = $this->getRequest()->getParam('criteria');
        $this->_qualifier = $this->getRequest()->getParam('qualifiers');
        $this->_qualifier2 = $this->getRequest()->getParam('_qualifiers');

        $params = [
            'YearID' => $this->_year,
            'MakeID' => $this->_make,
            'ModelID' => $this->_model,
            'SubModelID' => $this->_submodel,
            'Qualifiers' => [
                $this->_qualifier,
                $this->_qualifier2
            ],
            'Criteria' => $this->_criteria
        ];

        /**
         *  AJH\Fitment\Observer\AddToGarage
         */
        $this->_eventManager->dispatch('add_to_garage', ['fitment' => $params]);
        $this->_eventManager->dispatch('fitment_caching', ['params' => ['fitmentToCache' => 'partnumbers']]);

        $this->_helper->fitmentLog($params);

        /**
         * if not ajax request
         */
        if (!$this->_request->isXmlHttpRequest()) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $sefUrl = $this->_urlRewrite->getFitmentSefUrl($params);
            $resultPage = $this->_resultPageFactory->create();
            $block = $resultPage->getLayout()
                    ->createBlock('AJH\Fitment\Block\Categories')
                    ->setTemplate('AJH_Fitment::categories.phtml')
                    ->toHtml();

            $parsed_url = parse_url($sefUrl);

            return $resultJson->setData([
                        "categories" => $block,
                        "url" => $sefUrl,
                        "url_path" => $parsed_url['path'],
                        "error" => false
            ]);
        }
    }

//    private function getSEFUrl() {
//
//        $collection = $this->_fitmentUrlRewriteFactory->create()->getCollection();
//        $collection->addFieldToFilter('year', ['eq' => $this->fitment_year]);
//        $collection->addFieldToFilter('makeID', ['eq' => $this->fitment_make]);
//        $collection->addFieldToFilter('modelID', ['eq' => $this->fitment_model]);
//        $collection->addFieldToFilter('submodelID', ['eq' => $this->fitment_submodel]);
//
//        if ($collection->count()) {
//            $url = $collection->getFirstItem();
//            if ($this->criteria) {
//                $sef_url = $url->getRequestPath() . "?criteria=" . $this->criteria;
//            } else {
//                $sef_url = $url->getRequestPath();
//            }
//        } else {
//            if ($this->criteria) {
//                $sef_url = 'fitment/index/categories/?year=' . $this->fitment_year . '&make=' . $this->fitment_make . '&model=' . $this->fitment_model . '&submodel=' . $this->fitment_submodel . '&qualifiers[]=' . $this->qualifiers . '&_qualifiers[]=' . $this->_qualifiers . "&criteria=" . $this->criteria;
//            } else {
//                $sef_url = 'fitment/index/categories/?year=' . $this->fitment_year . '&make=' . $this->fitment_make . '&model=' . $this->fitment_model . '&submodel=' . $this->fitment_submodel . '&qualifiers[]=' . $this->qualifiers . '&_qualifiers[]=' . $this->_qualifiers;
//            }
//        }
//
//        return $sef_url;
//    }

    /**
     * setGarageVehicle
     * 
     * Add customer selected vehicle to customer garage
     * 
     */
//    private function setGarageVehicle() {
//        $garage_vehicles = array();
//        $garage = [];
//
//        $fitment = $this->_fitmentApi;
//
//
//        if ($fitment->hasFitment()) {
//            $year = $this->getRequest()->getParam('year');
//            $make = $fitment->getFitmentMakeName();
//
//            $model = $fitment->getFitmentModelName();
//
//            $submodel = $fitment->getFitmentSubModelName();
//
//            $garage = $this->_coreSession->getFitmentGarage();
//
//            if (!is_null($garage) && count($garage)) {
//                foreach ($garage as $key => $vehicle) {
//                    $garage_vehicles[$key] = $vehicle;
//                }
//            }
//
//            $_year = $this->fitment_year;
//            $_make = $this->_fitmentApi->getFitmentMakeName();
//            $_model = $this->_fitmentApi->getFitmentModelName();
//            $_submodel = $this->_fitmentApi->getFitmentSubModelName();
//
////            $urlString = Mage::helper('core/url')->getCurrentUrl();
////            $url = Mage::getSingleton('core/url')->parseUrl($urlString);
//            $path = ''; //$url->getPath();
//
//            $vid = $this->fitment_year . $this->fitment_make . $this->fitment_model . $this->fitment_submodel;
//            $vname['id'] = $vid;
//            $vname['current'] = $vid;
//            $vname['year'] = $year;
//            $vname['model'] = $model;
//            $vname['make'] = $make;
//            $vname['submodel'] = $submodel;
//            $vname['name'] = $_year . ' ' . $_make['Name'] . ' ' . $_model['Name'] . ' ' . $_submodel['Name'];
//
//            if (is_array($this->qualifiers) && is_array($this->_qualifiers)) {
//                $vname['url'] = "{$path}?year={$this->fitment_year}&make={$this->fitment_make}&model={$this->fitment_model}&submodel={$this->fitment_submodel}&qualifiers[]=" . implode(",", $this->qualifiers) . "&_qualifiers[]=" . implode(",", $this->_qualifiers);
//            } else {
//                $vname['url'] = "{$path}?year={$this->fitment_year}&make={$this->fitment_make}&model={$this->fitment_model}&submodel={$this->fitment_submodel}&qualifiers[]=" . $this->qualifiers . "&_qualifiers[]=" . $this->_qualifiers;
//            }
//
//            $garage_vehicles[$vid] = $vname;
//            $this->_coreSession->setFitmentGarage($garage_vehicles);
//        }
//    }
}
