<?php
namespace MyJob\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobController extends AbstractActionController {

	protected $jobTable;

	public function getJobTable() {
		if(!$this->jobTable) {
			$sm = $this->getServiceLocator();
			$this->jobTable = $sm->get('MyJob\Model\JobTable');
		}

		return $this->jobTable;
	}
	public function indexAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Jobs list');

		$view = new ViewModel(
			array(
				'jobs' => $this->getJobTable()->fetchAll()
			)
		);

		return $view;
	}

	public function searchAction() {
		$this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Search results');

		$text = (String) $this->params()->fromPost('text', '');
		$orderBy = (String) $this->params()->fromPost('order_by', '');

		$params = array(
			'text' => $text,
			'orderBy' => $orderBy
		);

		$view = new ViewModel(
			array(
				'jobs' => $this->getJobTable()->searchJob($params),
				'text' => $text
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
