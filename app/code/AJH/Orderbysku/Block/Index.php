<?php

namespace AJH\OrderBySku\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;
use AJH\Fitment\Model\Fitment\Categories as FitmentCategories;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\App\ResourceConnection;

class Index extends Template {
    
    public function __construct(Context $context) {

        parent::__construct($context);
    }

}