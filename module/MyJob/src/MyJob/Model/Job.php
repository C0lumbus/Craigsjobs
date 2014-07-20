<?php
namespace MyJob\Model;

class Job {
	public $id;
	public $url;
	public $title;
	public $city;
	public $created_original;
	public $created;
	public $text;

	public function exchangeArray($data) {
		$this->id     = (isset($data['vacancy_id'])) ? $data['vacancy_id'] : null;
		$this->city = (isset($data['city'])) ? $data['city'] : null;
		$this->title  = (isset($data['title'])) ? $data['title'] : null;
		$this->url  = (isset($data['url'])) ? $data['url'] : null;
		$this->created_original  = (isset($data['created_original'])) ? $data['created_original'] : null;
		$this->created  = (isset($data['created'])) ? $data['created'] : null;
		$this->text  = (isset($data['text'])) ? $data['text'] : null;
		$this->city  = (isset($data['job_city'])) ? $data['job_city'] : null;
	}
}
 