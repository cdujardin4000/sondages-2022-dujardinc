<?php  

require_once("models/Model.inc.php");
require_once("actions/Action.inc.php");

class LogoutAction extends Action {

   /**
    * Déconnecte l'utilisateur courant. Pour cela, la valeur 'null'
    * est affectée à la variable de session 'login' à l'aide d'une méthode
    * de la classe Action.
    *
    * @see Action::run()
    */


	public function run() {
        // determiner le modèle
        $model = new MessageModel;
        // determiner la vue
        $this->setView(getViewByName('Message'));
        $_SESSION['login'] = null;
        $model->setMessage('Vous Etes déconnecté, nous espérons vous revoir bientôt');
        $this->setSessionLogin($this->getSessionLogin());
        $this->setModel($model);
	}

}


