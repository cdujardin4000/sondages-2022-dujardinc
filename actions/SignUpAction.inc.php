<?php 

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

class SignUpAction extends Action {

	/**
	 * Traite les données envoyées par le formulaire d'inscription
	 * ($_POST['signUpLogin'], $_POST['signUpPassword'], $_POST['signUpPassword2']).
	 *
	 * Le compte est crée à l'aide de la méthode 'addUser' de la classe Database.
	 *
	 * Si la fonction 'addUser' retourne une erreur ou si le mot de passe et sa confirmation
	 * sont différents, on envoie l'utilisateur vers la vue 'SignUpForm' avec une instance
	 * de la classe 'MessageModel' contenant le message retourné par 'addUser' ou la chaîne
	 * "Le mot de passe et sa confirmation sont différents.";
	 *
	 * Si l'inscription est validée, le visiteur est envoyé vers la vue 'MessageView' avec
	 * un message confirmant son inscription.
	 *
	 * @see Action::run()
	 */
	public function run() {

		$this->setModel(new MessageModel());

		if ($_POST['signUpPassword1'] !== $_POST['signUpPassword2']) {

			$message = "Le mot de passe et sa confirmation sont différents...";

		} else {

			$message = $this->database->addUser($_POST['signUpLogin'], $_POST['signUpPassword1']);
            if($message === "Votre compte à bien été crée, nous sommes heureux de vous compter parmis nous!!!"){

                $this->setSessionLogin($_POST['signUpLogin']);
                $this->getModel()->setLogin($_POST['signUpLogin']);
                $this->getModel()->setMessage($message);
                $this->setView(getViewByName('Message'));

            }
		}

        $this->getModel()->setMessage($message);
        $this->createSignUpFormView($message);



	}

	private function createSignUpFormView($message): void
    {

		$this->setModel(new MessageModel());
		$this->getModel()->setMessage($message);
		$this->getModel()->setLogin($this->getSessionLogin());
		$this->setView(getViewByName("SignUpForm"));
	}
}
