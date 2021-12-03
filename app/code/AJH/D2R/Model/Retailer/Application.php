<?php

namespace AJH\D2R\Model\Retailer;

use Magento\Framework\App\Request\Http as HttpRequest;
use AJH\D2R\Helper\Data as D2RHelperData;

class Application extends \Magento\Framework\DataObject {

    protected $_request, $_d2rHelperData;

    public function __construct(HttpRequest $request, D2RHelperData $d2rHelper) {
        $this->_d2rHelperData = $d2rHelper;
        $this->_request = $request;

        parent::__construct();
    }

    public function init($request) {
        $applicationData = $request->getParams();

        unset($applicationData['_']);
        unset($applicationData['form_key']);
        unset($applicationData['customer_id']);
        unset($applicationData['address_id']);
        unset($applicationData['password']);
        unset($applicationData['confirmation']);

        $applicationData['date_sent'] = $this->_d2rHelperData->getDateFormatted(time(), 'MMMM d, YYYY HH:mm');
        $applicationData['auth_key'] = md5(rand(10000, 10000000));

        $this->addData($applicationData);

        return $this;
    }

    public function getData_() {
        if (!count($this->_data)) {
            $this->init($this->_request);
        }
        return parent::getData();
    }

}
