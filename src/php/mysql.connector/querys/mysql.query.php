<?php

namespace MySqlConnector {

    class Query {
        public 
            $params = array(), 
            $sql_formated = "";
            static $allowedOperators = array("=", "like", "<", "<=", "=>", ">"),
            $allowedConditions = array("and", "or");
        public function __construct($sql_string = null, $params = null) {
            if ($sql_string != null) $this->appendQuery($sql_string, $params);
        }

        //Get a Show Tables query with the where condition
        public static function showTables($whereCondition = null) {
            $sql = "SHOW TABLES";
            if ($whereCondition != null) $sql .= " WHERE $whereCondition";
            return new Query($sql);
        }

        //Get a Describe query with the table name
        public static function describe($tableName) {
            return new Query("DESCRIBE $tableName");
        }

        //Append a query to this query
        public function appendQuery($sql_string, $params = null) {
            //Always put a semicolon at the end of a query
            $sql = $sql_string.";\n";
            if ($params != null) {
                $this->sql_formated .= sprintf($sql, ...$params);
                array_merge($this->params, $params);
            } else $this->sql_formated .= $sql;
        }
        //Tell the connection to use the given database
        public function useDatabase($database) {
            Connection::useDatabase($database);
        }

        //Throw the SQL error if the result failed
        private static function throwIfError($result) {
            if ($result->failure()) throw new \Exception(Connection::lastError());
            return $result;   
        }

        //Run this query
        public function execute() {
            $connection = Connection::getConnection();
            $result = new Result($connection->query($this->sql_formated), $this->sql_formated);
            return self::throwIfError($result);
        }
        //Run many queries that have already been appended
        public function executeMany() {
            $connection = Connection::getConnection();
            $result = new Result($connection->multi_query($this->sql_formated), $this->sql_formated);
            return self::throwIfError($result);
        }



        //For running queries that should return a result
        public static function getResult($sql, $params = null) {
            $query = new Query($sql, $params);
            return $query->execute();
        }
        //For running  returing true or false (success values)
        public static function runActionQuery($sql, $params = null) {
            return self::getResult($sql, $params)->success();
        }
        //For running queries that return rows
        public static function runQuery($sql, $params = null) {
            return self::getResult($sql, $params)->fetchAll();
        }
        //Build a formattable list with $numItems, like '(%s, %s, %s...)'
        public static function buildFormattableList($numItems, $withParens = true, $quoteChar = "'") {
            $itemFormat = "$quoteChar%s$quoteChar";
            $sql = $withParens ? "(" : "";
            for ($i = 0;$i < $numItems;$i++) $sql .= $i < $numItems - 1 ? "$itemFormat, " : $itemFormat;
            $sql .= $withParens ? ")" : "";
            return $sql;
        }

        //Escape any special MySql chars in the given set of strings
        public static function escapeStrings($strings) {
            $conn = Connection::getConnection();
            $escaped = array();
            foreach ($strings as $string) 
                array_push($escaped, $conn->real_escape_string($string));
            return $escaped;
        }

        //Build a list with the given items, parenthesis, and quote character
        public static function buildItemList($items, $withParens = true, $quoteChar = "'", $escapeItems = true) {
            $listFormat = self::buildFormattableList(count($items), $withParens, $quoteChar);
            if ($escapeItems) $items = self::escapeStrings($items);
            $sql = sprintf($listFormat, ...$items);
            return $sql;
        }
        //Build a list with the given items, parenthesis, and quote character
        public static function buildSetList($items, $withParens = false, $quoteChar = "") {
            return self::buildItemList($items, $withParens, $quoteChar, false);
        }


        //Check that the number of columns matches the number of values
        public static function sameNumberOfColumnsAndValues($columns, $values) {
            return count($columns) == count($values);
        }
        //Return a SQL string with the propper `column` = 'value' syntax
        public static function getColumnEqualsValueSql($column, $value, $equalityOperator) {
            if ($equalityOperator == "like") return sprintf("`%s` like '%s%s%s'", $column, "%", $value, "%");
            else         return sprintf("`%s` %s '%s'", $column, $equalityOperator, $value);
        }
        //Return a SQL string containing the given logical operator
        public static function getLogicalOperatorSql($operator) {
            return sprintf(" %s ", $operator);
        }
        //Get all columns and values with a non null value
        public static function getUseableColumnsAndValues($columns, $values) {
            $nonNullValues = array();
            $nonNullColumns = array();

            for ($i = 0;$i < count($columns);$i++) {
                if (!empty($values[$i])) {
                    array_push($nonNullValues, $values[$i]);
                    array_push($nonNullColumns, $columns[$i]);
                }
            }

            return ["columns" => $nonNullColumns, "values" => $nonNullValues];
        }
        //Build the where condition for a query
        public static function buildWhereCondition($columns, $values, $equalityOperator, $booleanConditon) {
            $useable = Query::getUseableColumnsAndValues($columns, $values);
            $values = Query::escapeStrings($useable["values"]);
            $columns = $useable["columns"];

            $condition = "";
            $numColumns = count($columns);
            for ($i = 0;$i < $numColumns;$i++) {
                //Set column = value sql string
                $condition .= Query::getColumnEqualsValueSql($columns[$i], $values[$i], $equalityOperator);
                if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                    $condition .= Query::getLogicalOperatorSql($booleanConditon);
            }
            return $condition;
        }
    }
}

?>