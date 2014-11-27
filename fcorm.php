<?php 

require_once dirname(__FILE__).'/fcorm.exception.php';

class FcORM {
    /**
     * Class FcORM
     * Create a simple PHP ORM
     * Can be specialized for your specific tables
     * Works only on Table with simple auto-incremented ID !
     * @var array
     * @access private
     */
    private $_datas;
    /**
     * @var string
     */
    private $_pkey;
    /**
     * @var int
     */
    private $_id;
    /**
     * @var FcPDO (or connector with similar methods)
     */
    private $_connector;
    /**
     * @var string
     */
    private $_table;
    /**
     * @param FcPDO|mixed $connector
     * @param string $table
     * @param string $pkey
     * @param null $id
     * @throws InvalidConnectorException
     * @throws InvalidArgumentException
     * @throws InvalidIdException
     * @throws NotUniqueIdException
     * @throws PDOException
     */
    public function __construct($connector,$table,$pkey="id",$id=null){
        if(!isset($connector))          throw new InvalidConnectorException();
        if(gettype($table)!="string")   throw new InvalidArgumentException();

        $this->_datas = array();
        $this->_connector   =$connector;
        $this->_table       =$table;

        if($id!=null){
            if(gettype($pkey)!="string")    throw new InvalidArgumentException("Passed argument 'pkey' is not a valid string");
            $this->_pkey        =$pkey;
            $this->_id          =$id;

            try{$this->load();}
            catch(NotUniqueIdException $e){throw new NotUniqueIdException($e);}
            catch(PDOException $e){throw new PDOException($e);}
        }
    }
    /**
     * @param array $datas
     * @throws NotUniqueIdException
     * @throws InvalidArgumentException
     */
    public function load($datas=array()){
        if(gettype($datas)!="array")throw new InvalidArgumentException();
        if(sizeof($datas)==0){
            $tmp=$this->_connector->select("SELECT * FROM $this->_table WHERE $this->_pkey=:id",array("id"=>$this->_id),2);
            if(is_array($tmp)){
                if(sizeof($tmp)>1)  throw new NotUniqueIdException();
                if(sizeof($tmp)==1) $this->_datas=$tmp[0];
            }
        }
        else
            $this->_datas=$datas;
    }
    /**
     * @return integer
     * @throws ErrorInsertException
     */
    public function insert(){
        $datas=$this->_datas;
        $datas[$this->_pkey]=null;
        $query="INSERT INTO $this->_table";

        $fields = "(";
        $values = " VALUES(";
        $param=array();

        foreach($datas as $key=>$value){
            if($key!=$this->_pkey){
                $fields.=$key.",";
                $values.=":$key,";
                $param[$key]=$value;
            }
        }
        $fields=substr($fields,0,-1).")";
        $values=substr($values,0,-1).")";
        $query.=$fields.$values;

        try{return $this->_connector->exe($query,$param,true);}
        catch(PDOException $e){throw new ErrorInsertException($e);}
    }
    /**
     * @param array $fields
     */
    public function update($fields=array()){
        if(gettype($fields)!="array")throw new InvalidArgumentException("Passed argument 'fields' is not an array");
        $query="UPDATE $this->_table SET";
        $param=array("pkey"=>$this->_datas[$this->getPKey()]);
        foreach($this->_datas as $key=>$value){
            if(sizeof($fields)==0 || array_search($key,$fields)!==false ){
                $query.=" $key=:$key,";
                $param[$key]=$value;
            }
        }
        if(sizeof($param)==1)throw new InvalidArgumentException("Passed argument 'fields' can't create valid update query");

        $query=substr($query,0,-1);
        $query.=" WHERE $this->_pkey=:pkey";

        try{$this->_connector->exe($query,$param);}
        catch(PDOException $e){throw new ErrorUpdateException($e);}
        return true;
    }
    public function delete(){
        $query="DELETE FROM $this->_table WHERE $this->_pkey=:pkey";
        $param=array("pkey"=>$this->_datas[$this->getPKey()]);
        try{$this->_connector->exe($query,$param);}
        catch(PDOException $e){throw new ErrorDeleteException($e);}
        return true;
    }
    /**
     * @return array
     */
    public function getDatas(){
        return $this->_datas;
    }
    /**
     * @return FcPDO|mixed
     */
    public function getConnector(){
        return $this->_connector;
    }
    /**
     * @return string
     */
    public function getTable(){
        return $this->_table;
    }
    /**
     * @return string
     */
    public function getPKey(){
        return $this->_pkey;
    }
    /**
     * @param string $field
     * @return mixed
     */
    public function __get($field){
        if(!isset($this->_datas[$field]))
            throw new InvalidFieldException("This field is not exist in load. Maybe ORM don't load yet.");

        $functionSignature = 'get'.$field;
        if(method_exists($this,$functionSignature))
            return $this->$functionSignature($this->_datas[$field]);

        return $this->_datas[$field];
    }
    /**
     * @param string $field
     * @return mixed
     */
    public function __isset($field){
        if(!isset($this->_datas[$field]))
            throw new InvalidFieldException("This field is not exist in load. Maybe ORM don't load yet.");

        $functionSignature = 'get'.$field;
        if(method_exists($this,$functionSignature))
            return $this->$functionSignature($this->_datas[$field]);

        return $this->_datas[$field];
    }
    /**
     * @param string $field
     * @@param mixed $value
     */
    public function __set($field,$value){
        if(!array_key_exists($field, $this->_datas))
            throw new InvalidFieldException("This field is not exist in load. Maybe ORM don't load yet.");

        $functionSignature = 'set'.$field;
        if(method_exists($this,$functionSignature))
            $value = $this->$functionSignature($value);

        if($value!==$this->_datas[$field])
            $this->_datas[$field] = $value;
    }
}
