<?php
namespace MyJob\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Update;
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
						"application_id",
						"favorite",
						"denied",
						"no_experience",
						"hidden",
						"archived",
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
            $where .= " AND (jobs.hidden IS NULL OR jobs.hidden <> 1)";
        }
        else {
            $where = "jobs.hidden IS NULL OR jobs.hidden <> 1";
        }

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
            array(
                "vacancy_id" => "id",
                "job_city" => "city",
                "text",
                "title",
                "url",
                "created",
                "created_original",
                "application_id",
                "favorite",
                "denied",
                "no_experience",
                "hidden",
                "archived",
                "no_h1b",
                "unqualified",));

		$select->where("id=$id");

		$rowset = $this->tableGateway->selectWith($select);

		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Couldn't find job $id");
		}

		return $row;
	}

    /**
     * @param Job $job
     * @return int
     * @throws \Exception
     */
    public function saveJob(Job $job)
    {
        $data = array(
            'title' => $job->title,
            'city' => $job->city,
            'text' => $job->text,
            'source' => $job->source,
            'url' => $job->url,
            'created' => $job->created,
            'created_original' => $job->created_original,
            'application_id' => $job->application_id,
            'favorite' => $job->favorite,
            'denied' => $job->denied,
            'no_experience' => $job->no_experience,
            'no_h1b' => $job->no_h1b,
            'unqualified' => $job->unqualified,
            'hidden' => $job->hidden,
            'archived' => $job->archived,
        );

        $id = (int) $job->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);

            return $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getJob($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Job id does not exist');
            }
        }
    }

	public function toArray(ResultSet $resultSet) {
		$array = array();
		foreach($resultSet as $key => $value) {
			$array["$key"] = $value;
		}

		return $array;
	}

    public function markJob($jobId, $mark, $value) {
        $update = new Update("jobs");

        $update->set(array($mark => $value));

        $update->where("id = $jobId");

        $this->tableGateway->updateWith($update);
    }
}