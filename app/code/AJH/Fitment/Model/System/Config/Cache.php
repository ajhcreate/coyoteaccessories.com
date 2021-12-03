<?php

namespace AJH\Fitment\Model\System\Config;

use Magento\Framework\App\ResourceConnection;

class Cache extends \Magento\Framework\Model\AbstractModel {

    protected $_resource;
    protected $_tables = [];

    public function __construct(ResourceConnection $resource) {
        $this->_resource = $resource;
//        $this->_tables = ['api_fitment', 'api_fitment_makes', 'api_fitment_models', 'api_fitment_qualifiers', 'api_fitment_submodels', 'api_fitment_years'];
        $this->_tables = ['api_fitment', 'api_fitment_makes', 'api_fitment_models', 'api_fitment_qualifiers', 'api_fitment_submodels'];
    }

    public function clear() {
        $tables = $this->_tables;
        $resource = $this->_resource;
        $connW = $resource->getConnection('core_write');
        $connR = $resource->getConnection('core_read');
        
        $uncleared_tables = [];

        foreach($tables as $table){
            $query = "TRUNCATE `{$table}`;";
            $connW->query($query);
            
            $sql = "SELECT COUNT(*) FROM `{$table}`";
            $result = $connR->fetchOne($sql); 
            
            if(intval($result) > 0){
                array_push($uncleared_tables, $result);
            }
        }        
        
        
        return ["uncleared"=>count($uncleared_tables), "tables"=>$uncleared_tables];
    }

}
