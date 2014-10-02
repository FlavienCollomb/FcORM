require_once dirname(__FILE__).'/fcorm.exception.php';
require_once dirname(__FILE__).'/fcorm.php';

class FcORMSelection {
    /**
     * Class FcORMSelectCollection
     * Create a simple select collection of FcORM. Il your select is complicated, privilegied a custom query
     * @dependencies : FcORM
     * @var array
     * @access private
     */
    private $_collection;
    /**
     * @var string
     */
    private $_where;
    /**
     * @var array
     */
    private $_param;
    /**
     * @var string
     */
    private $_groupBy;
    /**
     * @var string
     */
    private $_orderBy;
    /**
     * @var string
     */
    private $_query;
    /**
     * @var FcORM
     */
    private $_fcORM;
    /**
     * @param FcORM $fcORM
     * @throws InvalidFcORMException
     */
    public function __construct($fcORM){
        if($fcORM instanceof FcORM === 1)    throw new InvalidFcORMException();
        $this->_fcORM = $fcORM;
        $this->_collection  = array();
        $this->_query       = "";
        $this->_where       = "";
        $this->_groupBy     = "";
        $this->_orderBy     = "";
    }
    /**
     * @param string $field
     * @param mixed $value
     * @param string $operand
     * @param string $operation
     * @throws InvalidArgumentException
     */
    public function addWhere($field,$value,$operand="=",$operation="AND"){
        if(gettype($field)!="string")    throw new InvalidArgumentException();
        if(!isset($value))  throw new InvalidArgumentException();

        if($this->_where == "") $this->_where .=" WHERE";
        else                    $this->_where .=" " . $operation;

        $this->_where           .=" ". $field." ".$operand." :".$field;
        $this->_param[$field]   =$value;
    }
    /**
     * @param $field
     */
    public function addGroupBy($field){
        if(gettype($field)!="string")    throw new InvalidArgumentException();

        if($this->_groupBy == "")   $this->_groupBy .=" GROUP BY";
        else                        $this->_groupBy .= ",";

        $this->_groupBy .=" " . $field;
    }
    /**
     * @param string $field
     * @param string $way
     * @throws InvalidArgumentException
     */
    public function addOrderBy($field,$way="ASC"){
        if(gettype($field)!="string")    throw new InvalidArgumentException();
        if(gettype($way)!="string")   throw new InvalidArgumentException();

        if($this->_orderBy == "")   $this->_orderBy .=" ORDER BY";
        else                        $this->_orderBy .= ",";

        $this->_orderBy .=" ".$field." ".$way;
    }
    /**
     * @param bool $simpleArray
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    public function get($simpleArray=false){
        if(gettype($simpleArray)!="boolean")    throw new InvalidArgumentException();
        $this->_collection  = array();

        $this->_query="SELECT * FROM " . $this->_fcORM->getTable();
        $this->_query.=$this->_where;
        $this->_query.=$this->_groupBy;
        $this->_query.=$this->_orderBy;

        try{
            $tmp=$this->_fcORM->getConnector()->select($this->_query,$this->_param);
            if($simpleArray)
                $this->_collection = $tmp;
            else{
                for($i=0;$i<sizeof($tmp);$i++){
                    $orm = clone $this->_fcORM;
                    $orm->load($tmp[$i]);
                    array_push($this->_collection,$orm);
                }
            }
            return $this->_collection;
        }
        catch(PDOException $e){
            throw new PDOException($e);
        }
    }
    /**
     * @return string
     */
    public function getQuery(){
        return $this->_query;
    }
    /**
     * @return string
     */
    public function getWhere(){
        return $this->_where;
    }
    /**
     * @return string
     */
    public function getParam(){
        return $this->_param;
    }
    /**
     * @return string
     */
    public function getGroupBy(){
        return $this->_groupBy;
    }
    /**
     * @return string
     */
    public function getOrderBy(){
        return $this->_orderBy;
    }
    /**
     * @return string
     */
    public function getCollection(){
        return $this->_collection;
    }
    public function resetAll(){
        $this->_query   = "";
        $this->_where   = "";
        $this->_groupBy = "";
        $this->_orderBy = "";
    }
}
