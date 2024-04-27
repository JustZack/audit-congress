<?php

namespace API {
    abstract class Route { 
        //All API Routes require specific parameters to run
        public abstract function canRunWithParams($parameters);
        //All API Routes fetch some sort of result
        public abstract function fetchResult($parameters = null);
    }
}

?>