<?php
namespace MyJob\Controller;

use MyJob\Model\ApplicationTable;
use MyJob\Model\CityTable;
use MyJob\Model\Job;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class JobController extends AbstractActionController {

	protected $jobTable;
	protected $cityTable;
    protected $adapter;

    /**
	 * @return \MyJob\Model\JobTable
	 */
	public function getJobTable() {
		if(!$this->jobTable) {
			$sm = $this->getServiceLocator();
			$this->jobTable = $sm->get('MyJob\Model\JobTable');
		}

		return $this->jobTable;
	}

	/**
	 * @return \MyJob\Model\CityTable
	 */
	public function getCityTable() {
        if (!$this->adapter) {
            $sm = $this->getServiceLocator();
            $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return new CityTable($this->adapter);
	}

    /**
	 * @return \MyJob\Model\ApplicationTable
	 */
	public function getApplicationTable() {
        if (!$this->adapter) {
            $sm = $this->getServiceLocator();
            $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return new ApplicationTable($this->adapter);
	}

	public function indexAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Jobs list');

        $session = new Container('search');

        $savedSearch = false;

        if($session->text != "" || $session->orderBy != "" || $session->selectedCityId != "") {
            $text = $session->text;
            $orderBy = $session->orderBy;
            $selectedCityId = $session->selectedCityId;

            $params = array(
                'text' => $text,
                'orderBy' => $orderBy,
                'city' => $selectedCityId
            );

            $savedSearch = true;
        }
        else {
            $text = "";
            $selectedCityId = null;
        }

		// grab the paginator from the JobTable

        if($savedSearch) {
            $paginator = $this->getJobTable()->searchJob($params, true);
        }
        else {
            $paginator = $this->getJobTable()->fetchAll(true);
        }

		// set the current page to what has been passed in query string, or to 1 if none set
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		// set the number of items per page to 10
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
            'paginator' => $paginator,
            'text' => $text,
            'cities' => $this->getCityTable()->getCities(),
            'city' => $selectedCityId,
            'totalJobs' => $paginator->getTotalItemCount()
		));
	}

	public function searchAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Search results');

        $session = new Container('search');

        $request = new Request();

        if($request->isPost()) {
            $text = (String) $this->params()->fromPost('text', '');
            $orderBy = (String) $this->params()->fromPost('order_by', '');
            $selectedCityId = (String) $this->params()->fromPost("city", "");

            $session->text = $text;
            $session->orderBy = $orderBy;
            $session->selectedCityId = $selectedCityId;
        }
        else {
            $text = $session->text;
            $orderBy = $session->orderBy;
            $selectedCityId = $session->selectedCityId;
        }

		$params = array(
			'text' => $text,
			'orderBy' => $orderBy,
			'city' => $selectedCityId
		);

        // grab the paginator from the JobTable
		$paginator = $this->getJobTable()->searchJob($params, true);
		// set the current page to what has been passed in query string, or to 1 if none set
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		// set the number of items per page to 10
		$paginator->setItemCountPerPage(10);

		$view = new ViewModel(
			array(
				'paginator' => $paginator,
				'text' => $text,
				'cities' => $this->getCityTable()->getCities(),
				'city' => $selectedCityId,
                'totalJobs' => $paginator->getTotalItemCount()
			)
		);

		$view->setTemplate('my-job/job/index');

		return $view;
	}

    public function markAction() {
        $jobId = $this->params()->fromRoute("id", 0);
        $as = $this->params()->fromQuery("as");

        $value = "1";
        switch($as) {
            case "applied":
                $as = "application_id";

                $value = $this->getApplicationTable()->saveApplication();

                break;

            case "denied":
                $as = "denied";

                break;

            case "favorite":
                $as = "favorite";

                break;

            case "no_experience":
                $as = "no_experience";

                break;

            case "hidden":
                $as = "hidden";

                break;

            case "no_h1b":
                $as = "no_h1b";

                break;

            case "unqualified":
                $as = "unqualified";

                break;

            case "archived":
                $as = "archived";

                break;
        }

        $this->getJobTable()->markJob($jobId, $as, $value);

        return $this->redirect()->toRoute('job', array("action" => "view", "id" => $jobId));
    }

	public function viewAction() {
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			$this->flashMessenger()->addErrorMessage('Job with id ' . $id .  ' doesn\'t set');
			return $this->redirect()->toRoute('job');
		}
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Job entity');

		$view = new ViewModel(
			array(
				'job' => $this->getJobTable()->getJob($id)
			)
		);

		return $view;
	}

    public function addAction() {
        $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Add job');
        $view = new ViewModel(
            array(
                'cities' => $this->getCityTable()->getCities()
            )
        );

        return $view;
    }

    public function saveAction() {
        $job = new Job();

        $job->id = 0;
        $job->title = $this->params()->fromPost("title");
        $job->city = $this->getCityTable()->getCityById($this->params()->fromPost("city"));
        $job->text = $this->params()->fromPost("text");
        $job->source = $this->params()->fromPost("source");
        $job->url = $this->params()->fromPost("url");
        $job->created = $this->params()->fromPost("created");
        $job->created_original = $this->params()->fromPost("created_original");

        if($this->params()->fromPost("applied") == "1") {
            $job->application_id = $this->getApplicationTable()->saveApplication();
        }
        else {
            $job->application_id = "0";
        }

        $job->favorite = (int)$this->params()->fromPost("favorite");
        $job->denied = (int)$this->params()->fromPost("denied");
        $job->no_experience = (int)$this->params()->fromPost("no_experience");
        $job->no_h1b = (int)$this->params()->fromPost("no_h1b");
        $job->unqualified = (int)$this->params()->fromPost("unqualified");
        $job->hidden = (int)$this->params()->fromPost("hidden");
        $job->archived = (int)$this->params()->fromPost("archived");

        $jobId = $this->getJobTable()->saveJob($job);

        return $this->redirect()->toRoute('job', array("action" => "view", "id" => $jobId));
    }

	public function pdfAction() {

	}
}
