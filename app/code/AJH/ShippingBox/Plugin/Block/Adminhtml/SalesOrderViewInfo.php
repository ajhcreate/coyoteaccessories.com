<?php

namespace AJH\ShippingBox\Plugin\Block\Adminhtml;

class SalesOrderViewInfo {

    protected $orderRepository;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            array $data = []
    ) {
        $this->orderRepository = $orderRepository;       
    }

    public function afterToHtml(\Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
            $result) {
        $customBlock = $subject->getLayout()->getBlock('custom_block');

        if ($customBlock !== false && $subject->getNameInLayout() == 'order_info') {

            $result = $result . $customBlock->toHtml();
        }

        return $result;
    }

    public function getOrder() {
        $orderId = $this->getRequest()->getParam('order_id');
        die($orderId);
        return $this->orderRepository->get($orderId);
    }

}
