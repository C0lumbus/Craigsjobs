<?php
namespace MyJob\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobController extends AbstractActionController {

	protected $jobTable;

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

	public function indexAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Jobs list');

		// grab the paginator from the AlbumTable
		$paginator = $this->getJobTable()->fetchAll(true);
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
			//'jobs' => $this->getJobTable()->fetchAll(),
			'cities' => $this->getJobTable()->getCities(),
			'paginator' => $paginator
		));
	}

	public function searchAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Search results');

		$text = (String) $this->params()->fromPost('text', '');
		$orderBy = (String) $this->params()->fromPost('order_by', '');
		$selectedCityId = (String) $this->params()->fromPost("city", "");

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
				'cities' => $this->getJobTable()->getCities(),
				'city' => $selectedCityId
			)
		);



		$view->setTemplate('my-job/job/index');

		return $view;
	}

	public function  viewAction() {
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
}
