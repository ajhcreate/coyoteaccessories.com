<?php

namespace AJH\PageWarmer\Block\Adminhtml\Index;

class Warming extends \Magento\Backend\Block\Widget\Grid\Container {

    /**
     * @return void
     */
    protected function _construct() {
        $this->_blockGroup = 'AJH_PageWarmer';
        $this->_controller = 'adminhtml_pagewarmer_index';
        $this->_headerText = __('Warming');
        $this->_addButtonLabel = __('OK');
        parent::_construct();
    }
    
    public function display(){
        
    }

}
