<?php
/**
 * Created by PhpStorm.
 * User: Oleg Pavlin
 * Date: 16.07.14
 * Time: 11:03
 */
 
namespace MyJob;

use MyJob\Model\Job;
use MyJob\Model\JobTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module {
	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
						__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}


	public function getServiceConfig() {
		return array(
			'factories' => array(
				'MyJob\Model\JobTable' =>  function($sm) {
					$tableGateway = $sm->get('JobTableGateway');
					$table = new JobTable($tableGateway);
					return $table;
				},
				'JobTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Job());
					return new TableGateway('jobs', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}
}