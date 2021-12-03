<?php

namespace AJH\PageWarmer\Block\Adminhtml\Queue;

use Magento\Backend\Block\Template;

/**
 * Class Logs
 *
 * @package AJH\PageWarmer\Block\Adminhtml\Queue\Logs
 */
class Logs extends Template {

    const LOG_PAGES = 'log_pages';

    protected $_template = 'AJH_PageWarmer::queue/logs.phtml';

    public function __construct(Template\Context $context, array $data = []) {        
        parent::__construct($context, $data);
    }
    
    public function getLogs(){
        return 'logs from DB';
    }

}
