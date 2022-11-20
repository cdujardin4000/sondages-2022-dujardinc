<?php  

require_once("models/Model.inc.php");
require_once("actions/Action.inc.php");

/**
 * Déconnecte l'utilisateur courant. Pour cela, la valeur 'null'
 * est affectée à la variable de session 'login' à l'aide d'une méthode
 * de la classe Action.
 *
 * @see Action::run()
 */
class LogoutAction extends Action {

	public function run() :void {
        // determiner le modèle
        $model = new MessageModel;
        // determiner la vue
        $this->setView(getViewByName('Message'));
        $model->setMessage('Vous Etes déconnecté, nous espérons vous revoir bientôt');
        $this->setSessionLogin(null);
        $this->setModel($model);
	}
}


