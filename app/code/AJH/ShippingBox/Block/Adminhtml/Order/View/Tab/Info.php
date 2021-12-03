<?php

namespace AJH\ShippingBox\Block\Adminhtml\Order\View\Tab;

class Info extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    protected $_template = 'order/view/tab/info.phtml';

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry, array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getOrder() {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getOrderId() {
        return $this->getOrder()->getEntityId();
    }

    public function getOrderIncrementId() {
        return $this->getOrder()->getIncrementId();
    }

    public function getTabLabel() {
        return __('My Custom Tab');
    }

    public function getTabTitle() {
        return __('My Custom Tab');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}
