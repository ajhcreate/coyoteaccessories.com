<?php

namespace AJH\SalesOrder\Api;

interface OrdersRepositoryInterface {

    /**
     * GET for Post api
     * @param string $param
     * @return string
     */
    public function getOrders($param);
}
