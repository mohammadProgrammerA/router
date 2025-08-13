<?php
    class facade {
        public static function __callStatic($methodName, $arguments =null){

            $object = new (static :: class);
            return $object -> $methodName($arguments);
        }

        public function __call($methodName, $arguments = null){
            
            return $this -> $methodName($arguments);

        }
    }
?>