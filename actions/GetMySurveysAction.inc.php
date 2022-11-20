<?php 

require_once("models/SurveysModel.inc.php");
require_once("actions/Action.inc.php");

/**
 * Construit la liste des sondages de l'utilisateur dans un modèle
 * de type "SurveysModel" et le dirige vers la vue "ServeysView"
 * permettant d'afficher les sondages.
 *
 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
 *
 * @see Action::run()
 */
class GetMySurveysAction extends Action {

	public function run() {

		if ($this->getSessionLogin() === null) {

			$this->setMessageView("Vous devez être authentifié.");

            return;
		}

        $surveysObjectList = [];
		$surveys = $this->database->loadSurveysByOwner($this->getSessionLogin());

        if (!empty($surveys)) {

            foreach ($surveys as $survey) {

                $surveysObjectList[] = new Survey($survey['owner'], $survey['question']);
            }
        }

        if(!empty($surveysObjectList)){

            $this->setModel(new SurveysModel());
            $this->getModel()->setLogin($this->getSessionLogin());
            $this->getModel()->setSurveys($surveysObjectList);
            $this->setView(getViewByName('Surveys'));

        } else {

            $model = new MessageModel();
            $model->setLogin($this->getSessionLogin());
            $this->setModel($model);
            $this->setMessageView('Vous n\'avez pas soumis de sondage.');
        }
	}
}


