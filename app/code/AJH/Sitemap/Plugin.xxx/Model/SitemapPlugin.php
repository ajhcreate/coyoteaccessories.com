<?php

namespace AJH\Sitemap\Plugin\Model;

class SitemapPlugin {
    private $logger;

    public function __construct(\Magento\Sitemap\Helper\Data $helper, \Psr\Log\LoggerInterface $logger) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function afterCollectSitemapItems(\Magento\Sitemap\Model\Sitemap $subject) {
        
         $this->logger->critical('SitemapPlugin:', 'SitemapPlugin');
         
        $storeId = $subject->getStoreId();
        $newRecords = [];
        $object = new \Magento\Framework\DataObject();
        $object->setId(['contact-us2-sample']);
        $object->setUrl('contact-us2-sample');
        $object->setUpdatedAt('2018-04-04 13:41:58');

        $newRecords['my_uniqukey_id'] = $object;

        $subject->addSitemapItem(new \Magento\Framework\DataObject(
                [
            'changefreq' => $this->helper->getPageChangefreq($storeId),
            'priority' => $this->helper->getPagePriority($storeId),
            'collection' => $newRecords,
                ]
        ));
    }

}
