<?php

namespace MySqlConnector {

    class QueryBuilder {
        
        static $allowedOperators = array("=", "like", "<", "<=", ">=", ">");
        static $allowedConditions = array("and", "or");

        public static function isAllowedOperator($operator) {
            return in_array($operator, self::$allowedOperators);
        }

        public static function isAllowedConditional($conditional) {
            return in_array($conditional, self::$allowedConditions);
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
        public static function getColumnEqualsValueSql($column, $value, $equalityOperator, $quotesArounditems = true) {
            $value = $value==false?"0":$value;

            if ($equalityOperator == "like") {
                if ($quotesArounditems) return sprintf("`%s` like '%s%s%s'", $column, "%", $value, "%");
                else                    return sprintf("%s like %s%s%s", $column, "%", $value, "%");
            }
            else {
                if ($quotesArounditems) return sprintf("`%s` %s '%s'", $column, $equalityOperator, $value);
                else                    return sprintf("%s %s %s", $column, $equalityOperator, $value);
                         
            }
        }
        //Return a SQL string containing the given logical operator
        public static function getLogicalOperatorSql($operator) {
            return sprintf(" %s ", $operator);
        }

        private static function isNotNullOrBlank($value) {
            if (!isset($value)) return false;
            else {
                if (is_string($value)) return strlen($value) > 0;
                else return true;
            }
        }
        //Get all columns and values with a non null value
        public static function getUseableColumnsAndValues($columns, $values) {
            $nonNullValues = array();
            $nonNullColumns = array();

            for ($i = 0;$i < count($columns);$i++) {
                if (self::isNotNullOrBlank($values[$i])) {
                    array_push($nonNullValues, $values[$i]);
                    array_push($nonNullColumns, $columns[$i]);
                }
            }

            return ["columns" => $nonNullColumns, "values" => $nonNullValues];
        }
        //Build the where condition for a query
        public static function buildWhereCondition($columns, $values, $equalityOperators, $booleanConditons) {
            $useable = self::getUseableColumnsAndValues($columns, $values);
            $values = self::escapeStrings($useable["values"]);
            $columns = $useable["columns"];

            $condition = "";
            $numColumns = count($columns);
            for ($i = 0;$i < $numColumns;$i++) {
                //Set column = value sql string
                $condition .= self::getColumnEqualsValueSql($columns[$i], $values[$i], $equalityOperators[$i]);
                if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                    $condition .= self::getLogicalOperatorSql($booleanConditons[$i]);
            }
            return $condition;
        }

        //Build the on condition for a join query
        public static function buildOnCondition($onTableColumns, $otherTableColumns) {
            $useable = self::getUseableColumnsAndValues($onTableColumns, $otherTableColumns);
            $onColumns = self::escapeStrings($useable["values"]);
            $otherColumns = $useable["columns"];

            $condition = "";
            $numColumns = count($onColumns);
            for ($i = 0;$i < $numColumns;$i++) {
                //Set column = value sql string
                $condition .= self::getColumnEqualsValueSql($onColumns[$i], $otherColumns[$i], "=", false);
                if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                    $condition .= self::getLogicalOperatorSql("AND");
            }
            return $condition;
        }

        //Build an insert statement for the columns and values
        public static function buildInsert($table, $columns, $values) {
            $sql = "INSERT INTO `$table` %s VALUES %s";
            if (!self::sameNumberOfColumnsAndValues($columns, $values))
                throw new SqlException("QueryBuilder Build Insert: columns and values length mismatch");
            $colList = self::buildItemList($columns, true, "`");
            $valList = self::buildItemList($values, true, "'");

            $sql = sprintf($sql, $colList, $valList);

            return $sql;
        }
    }
}

?>