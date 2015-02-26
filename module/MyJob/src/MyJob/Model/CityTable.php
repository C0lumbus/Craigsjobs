<?php
namespace MyJob\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;


class CityTable {
    /**
     * @var Adapter
     */
    private $adapter;

    public function __construct($adapter) {
        $this->adapter = $adapter;
    }

	public function getCities() {
        $sql = new Sql($this->adapter);
        $select = new Select("cities");

		$select->columns(array("city" => "city", "state" => "state", "id" => "id"));

		$select->group("city");
		$select->order("state ASC");


        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();


        //var_dump($this->adapter);
		//$results = $this->adapter->query($select);

		$arrayObj = $this->toArray($results);

		$cities = array();
		foreach($arrayObj as $obj) {
			$cities[$obj['state']][$obj["id"]] = $obj['city'];
		}

		return $cities;
	}

	public function toArray($resultSet) {
		$array = array();
		foreach($resultSet as $key => $value) {
			$array["$key"] = $value;
		}

		return $array;
	}
}