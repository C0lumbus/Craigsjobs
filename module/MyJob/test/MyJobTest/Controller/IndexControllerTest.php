<?php

namespace MyJobTest\Controller;

use MyJob\Controller;
use MyJob\Controller\JobController;
use MyJobTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class IndexControllerTest extends \PHPUnit_Framework_TestCase {
	protected $controller;
	protected $request;
	protected $response;
	protected $routeMatch;
	protected $event;

	protected function setUp() {
		$serviceManager = Bootstrap::getServiceManager();
		$this->controller = new JobController();
		$this->request = new Request();
		$this->routeMatch = new RouteMatch(array('controller' => 'index'));
		$this->event = new MvcEvent();
		$config = $serviceManager->get('Config');
		$routerConfig = isset($config['router']) ? $config['router'] : array();
		$router = HttpRouter::factory($routerConfig);

		$this->event->setRouter($router);
		$this->event->setRouteMatch($this->routeMatch);
		$this->controller->setEvent($this->event);
		$this->controller->setServiceLocator($serviceManager);
	}

	public function testIndexActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'index');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'search');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderById() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'id');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderByTitle() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'title');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderByCity() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'city');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderByCreated() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'created');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderByCreatedOriginal() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'created_original');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testSearchActionOrderByBar_WillFail() {
		$this->routeMatch->setParam('action', 'search');

		$this->request->getPost()->set('order_by', 'bar');

		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testJobIndexError404() {
		$this->routeMatch->setParam('action', 'action-non-exist');
		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(404, $response->getStatusCode());
	}

	public function testJobIndexError404_WillFail() {
		$this->routeMatch->setParam('action', 'action-non-exist');
		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();

		$this->assertEquals(403, $response->getStatusCode());
	}
}