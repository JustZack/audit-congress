<?php

namespace MySqlConnector {
    abstract class SqlRow {
        public function __construct($rowAssocArray = null) {
            $this->setFieldsFromObject($rowAssocArray);    
        }

        public abstract function getColumns();
        public abstract function getValues();
        function setFieldsFromObject($obj, $keyMustExist = true) {
            if ($obj == null) return;
            foreach ($obj as $key=>$value)
                if (($keyMustExist && property_exists($this, $key)) || !$keyMustExist) 
                    $this->{$key} = $value;
        }

        
        public static function rowsToObjects($rows) {
            if (count($rows) == 0) return null;

            $rowObjects = array();
            $objectType = static::class;
            foreach ($rows as $row) {
                array_push($rowObjects, new $objectType($row));
            }
            return $rowObjects;
        }
    }
}

?>