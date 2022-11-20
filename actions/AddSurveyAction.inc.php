<?php 

require_once("models/MessageModel.inc.php");
require_once("models/Survey.inc.php");
require_once("models/Response.inc.php");
require_once("actions/Action.inc.php");

/**
 * Traite les données envoyées par le formulaire d'ajout de sondage.
 *
 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
 *
 * Sinon, la fonction ajoute le sondage à la base de données. Elle transforme
 * les réponses et la question à l'aide de la fonction PHP 'htmlentities' pour éviter
 * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
 *
 * Un des messages suivants doivent être affichés à l'utilisateur :
 * - "La question est obligatoire.";
 * - "Il faut saisir au moins 2 réponses.";
 * - "Merci, nous avons ajouté votre sondage.".
 *
 * Le visiteur est finalement envoyé vers le formulaire d'ajout de sondage pour lui
 * permettre d'ajouter un nouveau sondage s'il le désire.
 *
 * @see Action::run()
 */
class AddSurveyAction extends Action {

	public function run()
    {
        $required = ['questionSurvey', 'responseSurvey1', 'responseSurvey2'];
        $errorMessage = "";
        foreach ($required as $i => $iValue) {
            if ($i === 0 && $_POST[$iValue] === "") {
                $errorMessage  .= "La question est obligatoire\n";
            }
            if (($i === 1 && $_POST[$iValue] === "") ||($i === 2 && $_POST[$iValue] === "")) {
                $errorMessage .= 'Vous devez proposer au moins 2 réponses'; break;
            }
        }

        if($errorMessage !== "") {
            $this->setModel(new MessageModel());
            $this->getModel()->setLogin($this->getSessionLogin());
            $this->getModel()->setMessage($errorMessage);
        } else {
            $survey = new Survey($this->getSessionLogin(), htmlentities($_POST['questionSurvey']));
            $survey->addResponse( htmlentities($_POST['responseSurvey1']));
            $survey->addResponse( htmlentities($_POST['responseSurvey2']));

            if (!empty($_POST['responseSurvey3'])){

                $survey->addResponse( htmlentities($_POST['responseSurvey3']));
            }
            if (!empty($_POST['responseSurvey4'])){

                $survey->addResponse( htmlentities($_POST['responseSurvey4']));
            }
            if (!empty($_POST['responseSurvey5'])){

                $survey->addResponse( htmlentities($_POST['responseSurvey5']));
            }
            foreach ($survey->getResponses() as $response){

                $this->database->saveResponse($response);
            }

            $this->database->saveSurvey($survey);

            $this->setModel(new MessageModel());
            $this->getModel()->setLogin($this->getSessionLogin());
            $this->getModel()->setMessage('Votre sondage à bien été enregistré');
        }
        $this->setView(getViewByName('AddSurveyForm'));


        /** //var_dump($_POST['questionSurvey']);die;
		if($_POST['questionSurvey'] == ""){

            $this->setModel(new MessageModel());
            $this->getModel()->setLogin($this->getSessionLogin());
            $this->getModel()->setMessage('La question est obligatoire');
		}

		if($_POST['responseSurvey1'] === "" || $_POST(['responseSurvey2']) === ""){

            $this->setModel(new MessageModel());
            $this->getModel()->setLogin($this->getSessionLogin());
            $this->getModel()->setMessage('Vous devez proposer au moins 2 réponses');
		}

		$survey = new Survey($this->getSessionLogin(), htmlentities($_POST['questionSurvey']));
		$survey->addResponse( htmlentities($_POST['responseSurvey1']));
		$survey->addResponse( htmlentities($_POST['responseSurvey2']));

		if (!empty($_POST['responseSurvey3'])){

			$survey->addResponse( htmlentities($_POST['responseSurvey3']));
		}
		if (!empty($_POST['responseSurvey4'])){

			$survey->addResponse( htmlentities($_POST['responseSurvey4']));
		}
		if (!empty($_POST['responseSurvey5'])){

			$survey->addResponse( htmlentities($_POST['responseSurvey5']));
		}
        foreach ($survey->getResponses() as $response){

            $this->database->saveResponse($response);
        }

		$this->database->saveSurvey($survey);

        $this->setModel(new MessageModel());
        $this->getModel()->setLogin($this->getSessionLogin());
        $this->getModel()->setMessage('Votre sondage à bien été enregistré');
        $this->setView(getViewByName('AddSurveyForm'));**/
	}
}


