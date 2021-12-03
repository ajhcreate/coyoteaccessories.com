<?php

namespace AJH\Fitment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Make extends Column {

    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory,
            array $components = [], array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $jsonManager = $objectManager->get('\Magento\Framework\Json\Decoder');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {                
                $make = $jsonManager->decode($items['make']);               
                
                $logo = trim($make['LOGO_URL'])!=''?"<img src=\"//{$make['LOGO_URL']}\" alt=\"{$make['MakeName']}\" style=\"display: block; margin: auto;\" height=\"100px\" />":"";
                $makeName = "<h2 style=\"text-align: center; margin-top: 5px;\"><strong>{$make['MakeName']}</strong></h2>";
                
                $items['make'] = $logo . $makeName;
            }
        }


        return $dataSource;
    }

}
