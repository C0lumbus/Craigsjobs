<?php
namespace MyJob\Controller;

use MyJob\Model\CityTable;
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

		// grab the paginator from the AlbumTable

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

		/*
		$view = new ViewModel(
			array(
				'jobs' => $this->getJobTable()->fetchAll(),
				'cities' => $this->getJobTable()->getCities()
			)
		);
		*/

		//var_dump($paginator);

		return new ViewModel(array(
            'paginator' => $paginator,
            'text' => $text,
            'cities' => $this->getCityTable()->getCities(),
            'city' => $selectedCityId
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


		// grab the paginator from the AlbumTable
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
				'city' => $selectedCityId
			)
		);



		$view->setTemplate('my-job/job/index');

		return $view;
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

	public function pdfAction() {

	}
}
