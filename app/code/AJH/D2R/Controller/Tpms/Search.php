<?php
namespace AJH\D2R\Controller\Tpms;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Context;

class Search extends \Magento\Framework\App\Action\Action {

    CONST VWVEHICLESTPMS = 'vwvehiclestpms';

    protected $_resourceConnection;
    protected $_urlInterface;

    public function __construct(Context $context, ResourceConnection $resourceConnection, UrlInterface $urlInterface) {
        $this->_resourceConnection = $resourceConnection;
        $this->_urlInterface = $urlInterface;

        parent::__construct($context);
    }

    public function execute() {
        $relearnUrl = $this->getRelearnUrl();

        try {
            echo $this->_view->getLayout()->createBlock('AJH\D2R\Block\Tpms\Sensors')
                    ->setRelearnUrl($relearnUrl)
                    ->toHtml();
        } catch (\Exception $e) {            
            echo $e->getMessage();
        }
        die;
    }

    public function getRelearnUrl() {
        $yearId = (int) $this->getRequest()->getParam('year');
        $makeId = (int) $this->getRequest()->getParam('make');
        $modelId = (int) $this->getRequest()->getParam('model');
        $submodelId = (int) $this->getRequest()->getParam('submodel');

        if (!$yearId || !$makeId || !$modelId || !$submodelId) {
            return false;
        }

        $info = $this->getVehicleInfo($yearId, $makeId, $modelId, $submodelId);
        
        if (empty($info['TPMSResetProcedureID'])) {
            return false;
        }

        $res = $this->_urlInterface->getUrl('', [
            '_direct' => 'tpms-reset-procedure',
            '_query' => [
                'Id' => $info['TPMSResetProcedureID'],
                'Make' => $info['MakeName'],
                'Model' => $info['ModelName'],
                'SubModel' => $info['SubModelName'],
                'Year' => $info['YearID']
            ],
        ]);
        return $res;
    }

    public function getVehicleInfo($yearId, $makeId, $modelId, $submodelId) {
        $connection = $this->getDbConnection();
        $res = $connection->fetchAll(sprintf(
                        'SELECT *'
                        . ' FROM ' . self::VWVEHICLESTPMS
                        . ' WHERE YearId=%d AND MakeID=%d AND ModelID=%d AND SubModelID=%d'
                        . ' LIMIT 1', $yearId, $makeId, $modelId, $submodelId
        ));

        return isset($res[0]) ? $res[0] : false;
    }

    public function getDbConnection() {
        return $this->_resourceConnection->getConnection('revo');
    }

}
