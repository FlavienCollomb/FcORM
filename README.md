FcORM
=====
*Fresh compilation for Object-Relational Mapping for manipulating MySQL Database*

Thanks `FcORM` and `FcORMSelection` you can enjoy a naive ORM system. For more details, read my [article](http://www.blog.flaviencollomb.fr/?p=34) (in french) about this subject on my blog.

##Dependencies
`FcPDO` (can be found [here](https://github.com/FlavienCollomb/fcPDO)) or another connector with similar public methods

##Usage
To use `FcORM` and `FcORMSelection` in your website, simply include fcorm.selection.php in your PHP scripts. That's it! Super!

```php
require_once "fcorm.selection.php";
```

##First Example
```php
/* Get FcPDO instance for database "database_test" */
$fcPDO = FcPDOController::get("database_test");
/* Create a first FcORM for "test" table */
$orm = new FcORM($fcPDO,"test");
/* Create a FcORMSelection instance for the FcORM instance */
$selection = new FcORMSelection($orm);
/* Add a where for future selection */
$selection->addWhere("lib","Code%","LIKE");
/* Get all lines in table "test" where lib like "Code% */
$result = $selection->get();
var_dump($result);
```

##Specialiation
You can create specialised FcORM for each of your table. For example with database_test.php

```php
Class ORMTestType extends FcORM{
    /**
     * @param FcPDO|mixed $connector
     */
    public function __construct($connector,$id="null"){
        parent::__construct($connector,"test_type","id",$id);
    }
}
```

And

```php
Class ORMTest extends FcORM{
    /**
     * @var ORMTestType
     */
    private $_test_type;
    /**
     * @param FcPDO|mixed $connector
     */
    public function __construct($connector,$id="null"){
        parent::__construct($connector,"test","id",$id);
    }
    public function getTestType(){
        if(isset($this->test_type_id)){
            if(!isset($this->_test_type)){
                $this->_test_type = new ORMTestType($this->getConnector(),$this->test_type_id);
            }
            return $this->_test_type;
        }
        else
            throw new ErrorLoadException();
    }
}
```

##Example with specialised FcORM
```php
/* Get FcPDO instance for database "database_test" */
$fcPDO = FcPDOController::get("database_test");
/* Create a specialized FcORM : ORMTest for table "test" */
$orm = new ORMTest($fcPDO);
/* Create a FcORMSelection instance for the ORMTest instance */
$selection = new FcORMSelection($orm);
/* Add a where for future selection */
$selection->addWhere("lib","Code%","LIKE");
/* Get all lines in table "test" where lib like "Code% */
$result = $selection->get();
var_dump($result);
for($i = 0; $i < sizeof($result); $i++){
   /* Get linked ORMTestType instance */
   var_dump($result[$i]->getTestType());
}
```

##Advantages
1. FcORM and FcORMSelection can be used in the raw
2. FcORM can be specialized for each table in your database. So, you can add getters and setters on other FcORM for your foreign key.
3. FcORM can be load with id in construct or with an array with public method load
4. Create selection query with FcORMSelection : where, order by and group by

## License
`FcORM` is licensed under the MIT license. (http://opensource.org/licenses/MIT)
