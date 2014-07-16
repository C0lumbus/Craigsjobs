<?php
namespace MyJob\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

 
class JobTable {
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll() {
		$select = new Select('jobs');

		$select->limit(50);
		$select->order('created_original DESC');

		$resultSet = $this->tableGateway->selectWith($select);

		return $resultSet;
	}

	public function searchJob($params) {
		$select = new Select('jobs');

		extract($params);

		if($text != "") {
			$select->where("text LIKE '%$text%' OR title LIKE '%$text%'");
		}

		if($orderBy != "") {
			$select->order("$orderBy ASC");
		}
		else {
			$select->order('created_original DESC');
		}

		$select->limit(50);


		$resultSet = $this->tableGateway->selectWith($select);

		return $resultSet;
	}

	public function getJob($id) {
		$id = (int)$id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Couldn't find job $id");
		}

		return $row;
	}
}