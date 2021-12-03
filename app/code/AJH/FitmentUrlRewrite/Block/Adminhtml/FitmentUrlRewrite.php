<?php

namespace AJH\FitmentUrlRewrite\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Module\Manager;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;

use AJH\FitmentUrlRewrite\Model\UrlRewrite;
use AJH\FitmentUrlRewrite\Model\UrlRewriteFactory;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;

class FitmentUrlRewrite extends Template {

    protected $_sitemapCollection;
    
    protected $_urlRewriteFactory;
    
    protected $_resource;
    protected $_connection;

    public function __construct(
    Context $context, SitemapFactory $sitemapFactory, UrlRewrite $urlRewrite,
            UrlRewriteFactory $urlRewriteFactory, ResourceConnection $resource,
            array $data = []
    ) {        
        $this->_urlRewriteModel = $urlRewrite;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_sitemapCollection = $sitemapFactory;

        $this->_resource = $resource;
        $this->_connection = $resource->getConnection();

        parent::__construct($context, $data);
    }

    public function getUrlCollection() {

        $fitment = $this->_urlRewriteFactory->create();

        $collection = $fitment->getCollection();


        return $collection;
    }

    public function getYearsCollection() {

        $fitment = $this->_sitemapCollection->create();

        $collection = $fitment->getCollection()->addFieldToSelect('year');

        return $collection;
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl($controller = null) {
        if (!$controller) {
            return $this->getUrl('fitmentseo/ajax/index');
        } else {
            return $this->getUrl('fitmentseo/ajax/' . $controller);
        }
    }

}
