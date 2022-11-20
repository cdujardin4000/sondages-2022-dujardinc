<?php 

require_once("models/MessageModel.inc.php");
require_once("models/Database.inc.php");

abstract class Action {
	private ?View $view;
	private ?Model $model;
	protected Database $database;

	/**
	 * Construit une instance de la classe Action.
	 */
	public function __construct(){
		$this->view = null;
		$this->model = null;
		$this->database = new Database();
	}

	/**
	 * Fixe la vue qui doit être affichée par le contrôleur.
	 *
	 * @param View $view Vue qui doit être affichée par le contrôleur.
	 */
	protected function setView(View $view): void
	{
		$this->view = $view;
	}

	/**
	 * Fixe le modèle à fournir à la vue lors de son affichage.
	 *
	 * @param Model $model Modèle à fournir à la vue.
	 */
	protected function setModel(Model $model): void
	{
		$this->model = $model;
	}

	/**
	 * Récupére la pseudonyme de l'utilisateur s'il est connecté, ou null sinon.
	 *
	 * @return string|null Pseudonyme de l'utilisateur ou null.
	 */
	protected function getSessionLogin(): ?string
	{
		return $_SESSION['login'] ?? null;
	}

	/**
	 * Sauvegarde le pseudonyme de l'utilisateur dans la session.
	 *
	 * @param string|null $login Pseudonyme de l'utilisateur.
	 */
	protected function setSessionLogin(?string $login): void
    {
		$_SESSION['login'] = $login;
	}

	/**
	 * Fixe de modèle et la vue de façon à afficher un message à l'utilisateur.
	 * 
	 * @param string $message Message à afficher à l'utilisateur.
	 */
	protected function setMessageView(string $message): void
    {
		$this->setModel(new MessageModel());
		$this->getModel()->setMessage($message);
		$this->getModel()->setLogin($this->getSessionLogin());
		$this->setView(getViewByName("Message"));
	}

	/**
	 * Retourne la vue qui doit être affichée par le contrôleur.
	 *
	 * @return View|null Vue qui doit être affichée par le contrôleur.
	 */
	public function getView(): ?View
	{
		return $this->view;
	}

	/**
	 * Retourne le modèle qui doit être donnée à la vue par le contrôleur.
	 *
	 * @return Model|null Modèle à fournir à la vue.
	 */
	public function getModel(): ?Model
	{
		return $this->model;
	}

	/**
	 * Méthode qui doit être implémentée par chaque action afin de décrire
	 * son comportement.
	 */
	abstract public function run();
}

