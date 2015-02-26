<?php
namespace MyJob\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class JobTable {
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll($paginated = false) {
		$select = new Select('jobs');
		$select->columns(
					array("vacancy_id" => "id",
							"job_city" => "city",
							"text",
							"title",
							"url",
							"created",
							"created_original")
		);
		$select->order('created_original DESC');

		if($paginated) {
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Job());
			$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
			$paginator = new Paginator($paginatorAdapter);

			return $paginator;
		}

		$resultSet = $this->tableGateway->selectWith($select);

		return $resultSet;
	}

	public function searchJob($params, $paginated = false) {
		$select = new Select('jobs');
		$select->columns(
				array("vacancy_id" => "id",
						"job_city" => "city",
						"text",
						"title",
						"url",
						"created",
						"created_original",
						"applied",
						"denied",
						"no_experience",
						"hidden",
						"no_h1b",
						"unqualified",
                ));
		extract($params);
		$where = "";

		if($text != "") {
			$where = "(jobs.text LIKE '%$text%' OR jobs.title LIKE '%$text%') ";
		}

		if($orderBy != "") {
			$order[$orderBy] = "ASC";
		}

		if($city != "" && $city != "0") {
			$city = (string)$city;
			if($text != "") {
				$where .= "\n AND cities.id=$city";
			}
			else {
				$where .= "cities.id=$city";
			}
			$select->join("cities", "jobs.city = cities.city");
		}

		$order["created_original"] = "DESC";

		if($where != "") {
			$select->where($where);
		}

		$select->order($order);

		if($paginated) {
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Job());
			$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
			$paginator = new Paginator($paginatorAdapter);

			return $paginator;
		}

		$resultSet = $this->tableGateway->selectWith($select);

		return $resultSet;
	}

	public function getJob($id) {
		$id = (int)$id;

		$select = new Select('jobs');
		$select->columns(
				array("job_city" => "city",
						"text",
						"title",
						"url",
						"created",
						"created_original"));

		$select->where("id=$id");

		$rowset = $this->tableGateway->selectWith($select);

		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Couldn't find job $id");
		}

		return $row;
	}

	public function toArray(ResultSet $resultSet) {
		$array = array();
		foreach($resultSet as $key => $value) {
			$array["$key"] = $value;
		}

		return $array;
	}
}