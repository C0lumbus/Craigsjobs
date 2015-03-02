<?php
namespace MyJob\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;


class ApplicationTable {
    /**
     * @var Adapter
     */
    private $adapter;

    public function __construct($adapter) {
        $this->adapter = $adapter;
    }

    public function saveApplication() {
        $sql = new Sql($this->adapter);
        $insert = new Insert("job_applications");
        $insert->columns(array("date"));

        $insert->values(array("date" => new Expression("NOW()")));

        return $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();
    }
}