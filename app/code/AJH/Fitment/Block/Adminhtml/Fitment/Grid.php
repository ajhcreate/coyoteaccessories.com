<?php

namespace AJH\Fitment\Block\Adminhtml\Fitment;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {

    protected $moduleManager;
    protected $_testFactory;
    protected $_status;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \Magento\Backend\Helper\Data $backendHelper,
            \Magento\Framework\Module\Manager $moduleManager, array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('fitment_id');
        $this->setDefaultSort('fitment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('lists_filter');
    }

    protected function _prepareCollection() {
        $collection = $this->_testFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn(
                'fitment_id', [
            'header' => __('ID'),
            'type' => 'number',
            'index' => 'id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'name' => 'fitment_id'
                ]
        );
        $this->addColumn(
                'fitment_title', [
            'header' => __('Fitment'),
            'index' => 'fitment_title',
            'class' => 'fitment_title',
            'name' => 'title'
                ]
        );
        $this->addColumn(
                'fitment_link', [
            'header' => __('Offer Link'),
            'index' => 'fitment_link',
            'name' => 'fitment_link'
                ]
        );
        $this->addColumn(
                'fitment_img', [
            'header' => __('Offer Image'),
            'index' => 'fitment_img',
            'name' => 'fitment_img'
                ]
        );
        $this->addColumn(
                'from_date', [
            'header' => __('Offer From'),
            'index' => 'from_date',
            'name' => 'from_date'
                ]
        );
        $this->addColumn(
                'to_date', [
            'header' => __('Offer Valid to'),
            'index' => 'to_date',
            'name' => 'to_date'
                ]
        );
        $this->addColumn(
                'active', [
            'header' => __('Offer Status'),
            'index' => 'active',
            'name' => 'active'
                ]
        );
        $this->addColumn(
                'edit', [
            'header' => __('Edit'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => [
                        'base' => '*/*/edit'
                    ],
                    'field' => 'fitment_id'
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action'
                ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

}
