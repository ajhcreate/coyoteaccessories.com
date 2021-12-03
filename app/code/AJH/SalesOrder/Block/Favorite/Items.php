<?php

namespace AJH\SalesOrder\Block\Favorite;

use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;

class Items extends \Magento\Framework\View\Element\Template {

    protected $_salesOrders, $_customer, $_categories;
    protected $_productRepository;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Magento\Customer\Model\Session $customer,
            \Magento\Sales\Model\Order\Config $salesConfig,
            OrderFactory $salesOrders, CategoryFactory $categories,
            ProductRepository $productRepository,
            StockItemRepository $stockItemRepository, array $data = []
    ) {

        parent::__construct($context, $data);

        $this->_customer = $customer->getCustomer();
        $this->_salesOrders = $salesOrders;
        $this->_categories = $categories;
        $this->_salesConfig = $salesConfig;
        $this->_productRepository = $productRepository;

        $this->_stockItemRepository = $stockItemRepository;
    }

    public function _construct() {
        $this->setTemplate('AJH_SalesOrder::favorite/items.phtml');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

    public function getOrders() {
        $orders = $this->_salesOrders->create()->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $this->_customer->getId())
                ->addFieldToFilter('status', array('in' => $this->_salesConfig->getVisibleOnFrontStatuses()))
                ->setOrder('created_at', 'desc');

        return $orders;
    }

    public function getProductCategories($catedories_id) {
        $collection = $this->_categories->create()->getCollection();
        $collection->addFieldToFilter('entity_id', array('eq' => $catedories_id))
                ->addAttributeToSelect('name');
//                ->getFirstItem();  

        return $collection;
    }

    public function getProductCategoriesList($catedories_id) {
        $categories_arr = [];
        $categories = $this->getProductCategories($catedories_id);

        foreach ($categories as $catedory) {
            array_push($categories_arr, $catedory->getName());
        }

        return $categories_arr;
    }

    public function getProductById($id) {
        return $this->_productRepository->getById($id);
    }

    public function getViewUrl($order) {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order) {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order) {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl() {
        return $this->getUrl('customer/account/');
    }

    public function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public function unique_multidimensional_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function getStockItem($productId) {
        return $this->_stockItemRepository->get($productId);
    }

}
