<?php

require_once("models/SurveysModel.inc.php");
require_once("actions/Action.inc.php");

/**
 * Construit la liste des sondages dont la question contient le mot clé
 * contenu dans la variable $_POST["keyword"]. Cette liste est stockée dans un modèle
 * de type "SurveysModel". L'utilisateur est ensuite dirigé vers la vue "ServeysView"
 * permettant d'afficher les sondages.
 *
 * Si la variable $_POST["keyword"] est "vide", le message "Vous devez entrer un mot clé
 * avant de lancer la recherche." est affiché à l'utilisateur.
 *
 * @see Action::run()
 */
class SearchAction extends Action {

	public function run() {


		if(!empty($_POST["keyword"])){

			$surveysObjectList = [];
			$surveys = $this->database->loadSurveysByKeyword($_POST["keyword"]);

			if($surveys) {

				foreach ($surveys as $survey){

					$surveysObjectList[] = new Survey($survey['owner'], $survey['question']);
				}

				$this->setModel(new SurveysModel());
				$this->getModel()->setLogin($this->getSessionLogin());
				$this->getModel()->setSurveys($surveysObjectList);
				$this->setView(getViewByName('Surveys'));
			}

		} else {

			$this->setModel(new MessageModel());
			$this->getModel()->setMessage("Vous devez entrer un mot clé avant de lancer la recherche...");
			$this->setView(getViewByName('Message'));
		}
	}
}

