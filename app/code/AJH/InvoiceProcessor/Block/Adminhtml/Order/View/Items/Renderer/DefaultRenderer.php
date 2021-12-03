<?php

namespace AJH\InvoiceProcessor\Block\Adminhtml\Order\View\Items\Renderer;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer as MagentoDefaultRenderer;

class DefaultRenderer extends MagentoDefaultRenderer{

    protected $_resourceConnection;

    public function __construct(ResourceConnection $_resourceConnection) {
        $this->_resourceConnection = $_resourceConnection;
    }

//    public function afterGetColumns(MagentoDefaultRenderer $subject, $result) {
//        $result['myfield'] = 'col-myfield';
//
//        return $result;
//    }

    public function getBox($sku) {
        $connection = $this->_resourceConnection->getConnection('core_read');
        $sql = "SELECT `box_sku`, `price` FROM `product_box_sku_price` WHERE `product_sku`='$sku'";
        $box = $connection->fetchAll($sql);
        return $box;
    }

    public function getPaymentInfo() {
//        $payment = $this->getLayout()->createBlock('Ess_M2ePro_Block_Adminhtml_Magento_Payment_Info');

        return $payment;
    }

}
