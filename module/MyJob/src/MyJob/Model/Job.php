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
    public $application_id;
    public $favorite;
    public $denied;
    public $no_experience;
    public $hidden;
    public $archived;
    public $no_h1b;
    public $unqualified;
    public $source;

    public function exchangeArray($data) {
		$this->id     = (isset($data['vacancy_id'])) ? $data['vacancy_id'] : null;
		$this->city = (isset($data['city'])) ? $data['city'] : null;
		$this->title  = (isset($data['title'])) ? $data['title'] : null;
		$this->url  = (isset($data['url'])) ? $data['url'] : null;
		$this->created_original  = (isset($data['created_original'])) ? $data['created_original'] : null;
		$this->created  = (isset($data['created'])) ? $data['created'] : null;
		$this->text  = (isset($data['text'])) ? $data['text'] : null;
		$this->city  = (isset($data['job_city'])) ? $data['job_city'] : null;
		$this->application_id  = (isset($data['application_id'])) ? $data['application_id'] : null;
		$this->favorite  = (isset($data['favorite'])) ? $data['favorite'] : null;
		$this->denied  = (isset($data['denied'])) ? $data['denied'] : null;
		$this->no_experience  = (isset($data['no_experience'])) ? $data['no_experience'] : null;
		$this->hidden  = (isset($data['hidden'])) ? $data['hidden'] : null;
		$this->archived  = (isset($data['archived'])) ? $data['archived'] : null;
		$this->no_h1b  = (isset($data['no_h1b'])) ? $data['no_h1b'] : null;
		$this->unqualified  = (isset($data['unqualified'])) ? $data['unqualified'] : null;
		$this->source  = (isset($data['source'])) ? $data['source'] : null;
	}
}
 