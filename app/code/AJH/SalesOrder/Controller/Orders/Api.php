<?php

namespace AJH\SalesOrder\Controller\Orders;

class Api extends \Magento\Framework\App\Action\Action {

    public function __construct(
    \Magento\Framework\App\Action\Context $context
    ) {
        return parent::__construct($context);
    }

    public function execute() {
        $userData = array("username" => "cainventory", "password" => "Icandoit#1");
        $ch = curl_init("https://coyoteaccessories.com/index.php/rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);        
                
//https://m2.coyoteaccessories.com/index.php/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=status&searchCriteria[filter_groups][0][filters][0][value]=pending
//        $apiOrderUrl = 'https://m2.coyoteaccessories.com/salesorder/rest/V1/orders?searchCriteria[filterGroups][][filters][][field]=status&searchCriteria[filterGroups][0][filters][0][value]=processing&searchCriteria[pageSize]=5';
//        $apiOrderUrl = 'https://m2.coyoteaccessories.com/index/rest/V1/caorders/all';
//        $apiOrderUrl = 'https://www.coyoteaccessories.com/index.php/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=status&searchCriteria[filter_groups][0][filters][0][value]=pending&searchCriteria[sortOrders][0][field]=entity_id&searchCriteria[sortOrders][0][direction]=desc&searchCriteria[pageSize]=5';
        $apiOrderUrl = 'https://coyoteaccessories.com/rest/V1/order/58124/ship';
        $ch = curl_init($apiOrderUrl);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));        

        $result = curl_exec($ch);
        $results = json_decode($result, true); // all orders with status pending
        
        echo $result;
        die;

//        foreach ($results['items'] as $order) {
//            $commentData = array(
//                'id' => $order['entity_id'], //order_id
//                'statusHistory' => array(
//                    'comment' => 'Received order?',
//                    'entity_id' => null,
//                    'is_customer_notified' => '1',
//                    'created_at' => now(),
//                    'parent_id' => $order['entity_id'], //order_id
//                    'entity_name' => 'order',
//                    'status' => 'processing', //assign new status to order
//                    'is_visible_on_front' => '1'
//                )
//            );
//
//            $commentData = json_encode($commentData, true);
//            $orderStatusApiUrl = 'https://m2.coyoteaccessories.com/index.php/rest/V1/orders/' . $order['entity_id'] . '/comments';
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $orderStatusApiUrl);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $commentData);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
//            $response = curl_exec($ch); //true
//        }

//        $userData = array("username" => "cainventory", "password" => "Icandoit#1");
//        $ch = curl_init("https://m2.coyoteaccessories.com/index.php/rest/V1/integration/admin/token");
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
//
//        $token = curl_exec($ch);
//                
//
//        $ch = curl_init("https://m2.coyoteaccessories.com/index.php/rest/V1/orders/?searchCriteria=0");
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
//
//        $result = curl_exec($ch);
//        
//        echo $result;
//        die;
//        $result = json_decode($result, 1);
//        echo '<pre>';
//        print_r($result);
//        die;
    }

}
