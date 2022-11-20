<?php 

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

/**
 * Dirige l'utilisateur vers le formulaire d'inscription.
 *
 * @see Action::run()
 */
class SignUpFormAction extends Action {

	public function run() {

		$this->setModel(new MessageModel());
		$this->getModel()->setLogin($this->getSessionLogin());
		$this->setView(getViewByName("SignUpForm"));
	}
}


