<?php

namespace MySqlConnector {
    abstract class CompareableSet {
        protected $set = array();
        
        public function has($index) { return array_key_exists($index, $this->set); }

        public function get($index) { return $this->has($index) ? $this->set[$index] : null; }

        public function add(CompareableObject $object) { $this->set[$object->name()] = $object; }

        public function keys() { return array_keys($this->set); }
        
        public function items() { return $this->set; }

        public function count() { return count($this->set); }

        public function compare(CompareableSet $otherSet) {
            //First get all objects these two sets share
            $diff = $this->getSharedObjects($otherSet);
            //Then add the objects in the $otherSet, but not in this set
            return $this->addUnknownObjects($otherSet, $diff);
        }

        private function getSharedObjects(CompareableSet $otherSet) {
            $diff = array();
            //First pass comparing each column in this object (expected columns) to those in the passed object
            foreach ($this->set as $name=>$thisOne) {
                //First try fetching this column from $otherColumns
                $other = $otherSet->get($name);
                //Check if it exists other $otherColumns
                $exists = $other != null;
                //If this exists in the other set, see if it matches
                $matches = $exists ? $thisOne->matches($other) : false;
                //Define this column in the $columnDifferences
                $diff[$name] = SetDifference::getInThis($thisOne, $exists, $matches);
            }
            return $diff;
        }

        private function addUnknownObjects(CompareableSet $otherSet, $diff) {
            //Second pass to catch any columns in the other object that dont exist in this one
            foreach ($otherSet->set as $name=>$otherOne)
                //Only if this column isnt set in $columnDifferences already
                if (!isset($diff[$name]))
                    //Define this as an extra column in the $diff
                    $diff[$name] = SetDifference::getInOther($otherOne);
            return $diff;
        }
    }

    abstract class CompareableObject {
        public $name;
        public function name() { return $this->name; }
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

        public function item() { return $this->object; }
        public function exists() { return $this->exists; }
        public function matches() { return $this->matches; }
        public function extra() { return $this->extra; }
    }
}

?>