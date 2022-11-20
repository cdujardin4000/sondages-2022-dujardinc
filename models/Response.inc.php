<?php
require_once("models/Survey.inc.php");

class Response {
	
	private $id;
	private $survey;
	private $title;
	private mixed $count;
	private int|float $percentage;

	public function __construct($survey, $title, $count = 0) {
		$this->id = null;
		$this->survey = $survey;
		$this->title = $title;
		$this->count = $count;
		$this->percentage = $this->computePercentage($survey->getResponses());
	}

	public function setId($id) {
		$this->id = $id;
	}
	
	public function computePercentage($total): float|int
	{
		$survey = $this->getSurvey();
		$nbVotes = count($survey->getResponses());

		return $nbVotes / $total;
	}

	public function getId() {
		return $this->id;
	}

	public function getSurvey() {
		return $this->survey;
	}
	
	public function getTitle() {	
		return $this->title;
	}

	public function getCount() {
		return $this->count;
	}
	
	public function getPercentage(): float|int
	{
		return $this->percentage;
	}
}

