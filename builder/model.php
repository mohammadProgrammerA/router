<?php
    
    class model extends facade implements interfaceBuilder {
    
        protected $type ; 
        protected $query ;
        protected $sub_query ;
        protected $from;
    

        
        protected function all(){

            $query = " SELECT * ";
            $this -> type ="select";
            $this -> query = $query;
            return $this -> get();

        }

        public function create($data){
            
            $fileds = "(";
            $field_data = "(";
            $number = 0;
            foreach($data as $field => $data_filed){
                $number ++;
                $fileds .= $field;
                $field_data .= "'".$data_filed . "'";

                if($number < count($data)){
                        $fileds .= "," ;
                        $field_data .= "," ;
                }
            }
            $fileds .= ")";
            $field_data .= ")";
            $insertQuery="INSERT INTO {$this ->table}  $fileds VALUES $field_data";
            $this -> query = $insertQuery;
            return $this -> get();
            
        }

        protected function select(array $fields=["*"]){
            if(empty($fields)){
                $fields=["*"];
            }
            
            $select_field='';
            $number = 0;

            for($i = 0 ; $i <count($fields) ; $i ++){
                $number ++;
                $select_field .= $fields[$i];
                if($number < count($fields)){
                    $select_field .= "," ;
                }
            }
            
            $this -> query = "SELECT $select_field ";
            $this -> type = "select";

            return $this;
        }

        protected function from(){
            
            $from = " FROM {$this ->table} ";

            $this -> from = $from;
                return $this;

        }
        
        protected function with($table){
          
            if(!$this -> type =="select"){
                $this -> select();
            } 
            
            $this ->join($table[0]);

            $relatedTo = $this -> relateTo[$table[0]];

            $this ->where($relatedTo[0] , $table[0]  . "." .$relatedTo[1] ,"=",true);
            
            return $this;
            
        }

        protected function join($table){
            $this ->join = "LEFT JOIN {$table}";
        }



        protected function belongsTo($tableName , $fields){

            if($this -> type !="select"){
                $this -> select();
            }

            $query =  $this -> query;
            $subQuery ="";

            foreach($fields as $field){
                $field_name = $field;
                $alias = $tableName ."_" . $field_name;

                $model = factoryClass :: makeObject($tableName);

                $parent_field = $this -> relateTo[$tableName];
               
                $query =  $model  ->select([$field_name])-> where( $tableName  .".id" ,$this ->table .".".$parent_field[0],"=") ->render();
               
                $subQuery =" , ($query ) {$alias}";
                $this -> sub_query .= " $subQuery  "; 
            
            }
            
            return $this;
        }

        protected function count($data){

            if($this -> type !="select"){
                $this -> select();
            }

            $model = $model = factoryClass :: makeObject($data[0]);
            $subQuery = $model -> select(["count(*)"]) -> from($data[0])->where($data[1] ,$this -> table. ".id" ,"=" ,true) ->render() ;
            $this -> query .= ",(".$subQuery.")" ."  " . $data[0] ."_count";
            
            return $this;  

        }

        public function find($id){
           
            $this -> select();
            $this -> where("id",$id);
            
            return $this ->get();

        }





        public function delete($id){

            $deleteQ="DELETE FROM {$this ->table} WHERE id=".$id;
            $this -> query = $deleteQ;
            return $this -> get();

        }

        
        
        
        public function update($data){
            $idEdite=$data['idEdite'];
            $field_data = "";
            $number = 0;
            foreach($data as $field => $data_filed){
                $number ++;
                if($field != "idEdite"){
                    
                    $field_data  .= $field  .'=' . "'" . $data_filed ."'". "  ";
                    
                    if($number < count($data)){
                        $field_data .= "," ;
                    }
                }
            }
            
            
            $updateQuery="UPDATE {$this -> table} SET $field_data WHERE id=".$idEdite;
            
            $this -> query = $updateQuery;

           
            return $this ->get();

        }

        public function where($field, $data, $jabr = '=',$flag = false){

            if (!in_array($this->type, ['select', 'update', 'delete'])) {
                throw new Exception("not where");
            }
            $this -> where =[];     
            if(is_int($data) || $flag == true){

                $this->where[] = "$field $jabr $data";
            }else{
                $this->where[] = "$field $jabr '$data'";
            }
          
            return $this;

        }

        public function limit($offset,$limit){
            if (!in_array($this->type, ['select'])) {
                throw new Exception("not limit");
            }

            $this->limit = " LIMIT " . $offset . ", " . $limit;
            
            return $this;
        }

        public function pageInit($number){

            $limit = ($number -1) * 5 ;
            $query = " LIMIT $limit , 5 ";
            $this -> limit = $query;
            return $this;
          
            
        } 


        public function render(){
            $query = $this -> query;
        
            
            if (!empty($this->pageInit)) {
                $query .= $this->pageInit;
            }

    
            if($this -> type =="select"){
                $this -> from();
                
            }
            if(!empty($this->sub_query)){
        
                $query .= $this->sub_query;
            }
            
            if(!empty($this->from)){
                $query .= $this->from;
            }
            if(!empty($this->join)){
                $query .= $this->join;
            }
            
            if (!empty($this->where)) {
                if(!empty($this->join)){
                    $query .= " ON " . implode( $this -> where);
                }else{
                    $query .= " WHERE " . implode( $this -> where);
                }
            }
            if (isset($this ->limit)) {
                $query .= $this->limit;
            }
           
            return $query;
          
        }
        



        public function get(){
         
            $query = $this -> render();
            return mainDb::get($query);
        }




























        public static function sort($data){
            self::select();
            $className = static::class;
            $model = factoryClass :: makeObject($className);
            if($data["az"] < $data ["ta"] ){
                // $model -> limit($data["az"] -1 , $data ["ta"] - $data["az"] +1);
                for($i = $data["az"];$i<=$data["ta"];$i++){
                
                    $model -> limit($i -1 , 1 );
                    $sortData[] = $model -> get()->fetch_assoc();
                   
                }
                return $sortData;
            }

            if($data["ta"] < $data ["az"] ){
                $sortData=[];
                

                for($i = $data["az"];$i>=$data["ta"];$i--){
                    $model -> limit($i -1 , 1 );
                    $sortData[] = $model -> get() ->fetch_assoc();
                }
               
                return $sortData;
            }
        }



            }
?>