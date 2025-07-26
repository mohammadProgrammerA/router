<?php
    
    class model extends mainDb implements interfaceBuilder {
    
        protected $type ; 
        protected $query ;

        public static function select(array $fields=["*"]){
            
            $select_field='';
            $number = 0;

            for($i = 0 ; $i <count($fields) ; $i ++){
                $number ++;
                $select_field .= $fields[$i];
                if($number < count($fields)){
                    $select_field .= "," ;
                }
            }

            $className = static :: class;
            $model=factoryClass :: makeObject($className);
            $table = static :: $table;
            
            $model -> query = "SELECT $select_field FROM {$table}";
            $model -> type = "select";
            return $model;
        }

       
    
        public static function all(){
            $className = static::class;
            
            $model = factoryClass :: makeObject($className);
            $table = static :: $table;
            $query = " SELECT * FROM {$table}";
            // return $model->get();
            return $model->connection->query($query);
            

        }



        public static function find($id){

            $className = static::class;
            $table = static :: $table;
            $model=factoryClass :: makeObject($className);
            $findQ="SELECT * FROM {$table} WHERE id=".$id;
            return $model->connection->query($findQ);

        }


        public static function delete($id){

            $className = static::class;
            $model=factoryClass :: makeObject($className);
            $table = static :: $table;
            $deleteQ="DELETE FROM {$table} WHERE id=".$id;
            
            return $model->connection->query($deleteQ);

        }

        public static function create($data){
            
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
            $table = static :: $table;
            $className = static::class;
            $model=factoryClass :: makeObject($className);
            
            $insertQuery="INSERT INTO {$table}  $fileds VALUES $field_data";
            
            return $model->connection->query($insertQuery); 
        }
        
        public static function update($data){
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
            
            $table = static :: $table;
            $updateQuery="UPDATE {$table} SET $field_data WHERE id=".$idEdite;
            $className = static::class;
            $model = factoryClass :: makeObject($className);
            return $model->connection->query($updateQuery);

        }

        public function where($field, $data, $jabr = '='){

            if (!in_array($this->type, ['select', 'update', 'delete'])) {
                throw new Exception("not where");
            }

            $this->where[] = "$field $jabr '$data'";
          
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

            // $className = static::class;
            // $model = factoryClass :: makeObject($className);
            // $table = static :: $table;
            
            $limit = ($number -1) * 5 ;
            $query = " LIMIT $limit , 5 ";
            $this -> pageInit = $query;
            return $this;
            // return $model->connection->query($query);
            
        } 



        public function get(){
           
            $query = $this -> query;
           
            if (!empty($this->where)) {
                $query .= " WHERE " . implode(' AND ', $this -> where);
            }
            
            if (isset($this ->limit)) {
                $query .= $this->limit;
            }
            if (!empty($this->pageInit)) {
                $query .= $this->pageInit;
            }
            $className = static::class;
            return $this->connection->query($query);
            
            
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