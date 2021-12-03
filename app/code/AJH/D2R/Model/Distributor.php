<?php

namespace AJH\D2R\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\D2R\Model\ResourceModel\Distributor as ResourceModel;

class Distributor extends AbstractModel {

    /**
     * Define resource model
     */
    const FORMAT_DEFAULT = '%NAME%, %CITY%, %STATE%, %COUNTRY%';
    const FORMAT_FULL = '%NAME%, %ADDRESS1%, %ADDRESS2%, %CITY%, %STATE%, %ZIP%, %COUNTRY%, %PHONE%, %FAX%, %EMAIL%, %WEBSITE%';
    const FORMAT_FULL_MULTILINE = "%NAME%,\n%ADDRESS1%, %ADDRESS2%,\n%CITY%, %STATE%, %ZIP%, %COUNTRY%,\n%PHONE%, %FAX%,\n%EMAIL%,\n%WEBSITE%";

    protected $_eventPrefix = 'd2r_distributor';

    public function _construct() {
        $this->_init(ResourceModel::class);
    }

    public function format($format = self::FORMAT_DEFAULT) {
        if (!$this->getId()) {
            return '';
        }

        $res = $format;

        $res = str_replace('%NAME%', $this->getData('Name'), $res);
        $res = str_replace('%ADDRESS1%', $this->getData('Address1'), $res);
        $res = str_replace('%ADDRESS2%', $this->getData('Address2'), $res);
        $res = str_replace('%CITY%', $this->getData('City'), $res);
        $res = str_replace('%STATE%', $this->getData('State'), $res);
        $res = str_replace('%ZIP%', $this->getData('Zip'), $res);
        $res = str_replace('%COUNTRY%', $this->getData('Country'), $res);
        $res = str_replace('%PHONE%', $this->getData('Phone'), $res);
        $res = str_replace('%FAX%', $this->getData('Fax'), $res);
        $res = str_replace('%EMAIL%', $this->getData('Email'), $res);
        $res = str_replace('%WEBSITE%', $this->getData('WebsiteURL'), $res);
        $res = str_replace('%LOGO%', $this->getData('Logo'), $res);

        return $res;
    }

}
