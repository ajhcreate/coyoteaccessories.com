<?php

namespace AJH\Fitment\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {

    protected $_template = 'fitment/lists.phtml';

    public function __construct() {
        $this->_controller = 'adminhtml_grid';
        $this->_blockGroup = 'AJH_Fitment';
        $this->_headerText = __('Fitment');
//        $this->_addButtonLabel = __('Create New Post');

        parent::__construct();
    }

}
