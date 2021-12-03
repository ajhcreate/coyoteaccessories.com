<?php

namespace AJH\D2R\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as SearchResult;

class CustomerDataProvider extends SearchResult {

    protected function _initSelect() {
        parent::_initSelect();
//        $this->getSelect()->joinLeft(
//                ['secondTable' => $this->getTable('custom_table')], 'main_table.entity_id = secondTable.customer_id', ['reset_tool_use']
//        );
        return $this;
    }

}


//namespace AJH\D2R\Ui\Component\Listing;
// 
//use Codextblog\Customercolumn\Model\PannumberFactory;
//use Magento\Framework\Api\SearchCriteriaBuilder;
//use Magento\Framework\View\Element\UiComponent\ContextInterface;
//use Magento\Framework\View\Element\UiComponentFactory;
//use Magento\Customer\Api\CustomerRepositoryInterface;
//use Magento\Ui\Component\Listing\Columns\Column;
// 
//class CustomerDataProvider extends Column
//{
//    protected $_customerRepository;
//    protected $_searchCriteria;
//    protected $_pannumberfactory;
// 
//    public function __construct(
//        ContextInterface $context,
//        UiComponentFactory $uiComponentFactory,
//        CustomerRepositoryInterface $customerRepository,
//        SearchCriteriaBuilder $criteria,
//        PannumberFactory $pannumberFactory,
//        array $components = [],
//        array $data = []
//    ) {
//        $this->_customerRepository = $customerRepository;
//        $this->_searchCriteria  = $criteria;
//        $this->_pannumberfactory = $pannumberFactory;
//        parent::__construct($context, $uiComponentFactory, $components, $data);
//    }
// 
//    public function prepareDataSource(array $dataSource)
//    {
//        if (isset($dataSource['data']['items'])) {
//            foreach ($dataSource['data']['items'] as & $item) {
//                $customer  = $this->_customerRepository->getById($item["entity_id"]);
// 
//                $customer_id = $customer->getId();
// 
//                $collection = $this->_pannumberfactory->create()->getCollection();
//                $collection->addFieldToFilter('customer_id', $customer_id);
// 
//                $data = $collection->getFirstItem();
// 
//                $item[$this->getData('name')] = $data->getPanNumber();
//            }
//        }
//        return $dataSource;
//    }
//}