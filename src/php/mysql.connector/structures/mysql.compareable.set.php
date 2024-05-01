<?php

namespace MySqlConnector {
    abstract class CompareableSet {
        protected $set = array();
        
        public function has($index) { return array_key_exists($index, $this->set); }

        public function get($index) { return $this->has($index) ? $this->set[$index] : null; }

        public function set($key, $value) { $this->set[$key] = $value; }

        public function keys() { return array_keys($this->set); }
        
        public function items() { return $this->set; }

        public function count() { return count($this->set); }

        public function compare(CompareableSet $otherSet) {
            $diff = array();

            //First pass comparing each column in this object (expected columns) to those in the passed object
            foreach ($this->set as $name=>$thisOne) {
                //First try fetching this column from $otherColumns
                $other = $otherSet->get($name);
                //Check if it exists other $otherColumns
                $exists = $other != null;
                //Default to no match
                $matches = false;
                //But if it exists, see if it does match
                if ($exists) {
                    //Check if this column matches the $otherColumn
                    $matches = $thisOne->matches($other);
                }
                //Define this column in the $columnDifferences
                $diff[$name] = SetDifference::getInThis($thisOne, $exists, $matches);
            }

            //Second pass to catch any columns in the other object that dont exist in this one
            foreach ($otherSet->set as $name=>$otherOne)
                //Only if this column isnt set in $columnDifferences already
                if (!isset($diff[$name]))
                    //Define this as an extra column in the $columnDifferences
                    $diff[$name] = SetDifference::getInOther($otherOne);

            return $diff;
        }
    }

    abstract class CompareableObject {
        public abstract function matches($other);
    }

    class SetDifference {
        private
            $object,
            $exists,
            $matches,
            $extra;
        public function __construct(CompareableObject $object, $exists, $matches, $extra) {
            $this->object = $object;
            $this->exists = $exists;
            $this->matches = $matches;
            $this->extra = $extra;
        }

        public static function getInThis(CompareableObject $object, $exists, $matches) {
            return new SetDifference($object, $exists, $matches, false);
        }

        public static function getInOther(CompareableObject $object) {
            return new SetDifference($object, false, false, true);
        }

        public function item() : CompareableObject { return $this->object; }
        public function exists() { return $this->exists; }
        public function matches() { return $this->matches; }
        public function extra() { return $this->extra; }
    }
}

?>