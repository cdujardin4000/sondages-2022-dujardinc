<?php 

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

/**
 * Met à jour le mot de passe de l'utilisateur en procédant de la façon suivante :
 *
 * Si toutes les données du formulaire de modification de profil ont été postées
 * ($_POST['updatePassword1'] et $_POST['updatePassword2']), on vérifie que
 * le mot de passe et la confirmation sont identiques.
 * S'ils le sont, on modifie le compte avec les méthodes de la classe 'Database'.
 *
 * Si une erreur se produit, le formulaire de modification de mot de passe
 * est affiché à nouveau avec un message d'erreur.
 *
 * Si aucune erreur n'est détectée, le message 'Modification enregistrée.'
 * est affiché à l'utilisateur.
 *
 * @see Action::run()
 */
class UpdateUserAction extends Action {

	public function run() {

		if(empty($_POST['updatePassword1']) || empty($_POST['updatePassword2'])) {

            $this->createUpdateUserFormView('Veuillez remplir tous les champs');

		}
		if ($_POST['updatePassword1'] !== $_POST['updatePassword2']) {

            $this->createUpdateUserFormView('Les mots de passe ne sont pas identiques');

		}
        if ($this->database->updateUser($this->getSessionLogin(), $_POST['updatePassword1']) === true) {

            $this->setModel(new MessageModel());
            $this->getModel()->setMessage('Modification enregistrée...');
            $this->setView(getViewByName("Message"));
        }
	}

	private function createUpdateUserFormView($message): void {

		$this->setModel(new MessageModel());
		$this->getModel()->setMessage($message);
		$this->getModel()->setLogin($this->getSessionLogin());
		$this->setView(getViewByName("UpdateUserForm"));
	}
}

