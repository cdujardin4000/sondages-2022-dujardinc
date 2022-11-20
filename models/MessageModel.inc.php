<?php 
require_once("models/Model.inc.php");

class MessageModel extends Model {
	
	/**
	 * @var string Message à afficher à l'utilisateur.
	 */
	private string $message;

	/** 
	 * Construit une instance de la classe 'MessageModel'.
	 * Initialise la propriété $message à "".
	 */
	public function __construct() {
		$this->message = "";
	}

	/**
	 * Retourne le message stocké dans le modèle.
	 * 
	 * @return string Message stocké dans le modèle.
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * Affecte un message au modèle.
	 * 
	 * @param string $message Message à stocké dans le modèle.
	 */
	public function setMessage($message): void
	{
	  	$this->message = $message;
	}

}

