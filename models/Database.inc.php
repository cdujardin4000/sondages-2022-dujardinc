<?php 
require_once("models/Survey.inc.php");
require_once("models/Response.inc.php");

class Database {

	private PDO $connection;
	/**
	 * Ouvre la base de données. Si la base n'existe pas elle
	 * est créée à l'aide de la méthode createDataBase().
	 */
	public function __construct() {

		$this->connection = new PDO("sqlite:database.sqlite");

		$q = $this->connection->query('SELECT name FROM sqlite_master WHERE type="table"');

		if (count($q->fetchAll()) === 0) {

			$this->createDataBase();
		}
	}

	/**
	 * Crée la base de données ouverte dans la variable $connection.
	 * Elle contient trois tables :
	 * - une table users(nickname char(20), password char(50));
	 * - une table surveys(id integer primary key autoincrement,
	 *						owner char(20), question char(255));
	 * - une table responses(id integer primary key autoincrement,
	 *		id_survey integer,
	 *		title char(255),
	 *		count integer);
	 */
	private function createDataBase(): void
	{
		$this->connection->query(
			'CREATE TABLE users(
    		nickname char (20),
    		password char (50))'
		);
		$this->connection->query(
			'CREATE TABLE surveys(
    		id INTEGER PRIMARY KEY AUTOINCREMENT,
    		owner char (20),
			question char (255))'
    	);
		$this->connection->query(
			'CREATE TABLE responses(
    		id INTEGER PRIMARY KEY AUTOINCREMENT,
    		idSurvey INTEGER,
    		title char (255),
    		count INTEGER)'
    	);
	}

	/**
	 * Vérifie si un pseudonyme est valide, c'est-à-dire,
	 * s'il contient entre 3 et 10 caractères et uniquement des lettres.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est valide, false sinon.
	 */
	private function checkNicknameValidity(string $nickname): bool
	{
		if (!(strlen($nickname) > 2 && strlen($nickname) < 11 && ctype_alpha($nickname))) {

			return 1;
		}
		return 0;
	}

	/**
	 * Vérifie si un mot de passe est valide, c'est-à-dire,
	 * s'il contient entre 3 et 10 caractères.
	 *
	 * @param string $password Mot de passe à vérifier.
	 * @return boolean True si le mot de passe est valide, false sinon.
	 */
	private function checkPasswordValidity(string $password): bool
	{
		if (!(strlen($password) > 2 && strlen($password) < 11)) {

			return 2;
		}
		return 0;
	}

	/**
	 * Vérifie la disponibilité d'un pseudonyme.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est disponible, false sinon.
	 */
	private function checkNicknameAvailability(string $nickname): bool
	{
		$response = $this->connection->query(

			"SELECT * FROM users WHERE nickname=='$nickname'"

		)->fetchall(PDO::FETCH_OBJ);

		if (count($response) !== 0) {

			return 3;
		}
		return 0;
	}

	/**
	 * Vérifie qu'un couple (pseudonyme, mot de passe) est correct.
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean True si le couple est correct, false sinon.
	 */
	public function checkPassword(string $nickname, string $password): bool
	{
		$response = $this->connection->query(

			"SELECT * FROM users WHERE nickname=='$nickname'"

		)->fetch(PDO::FETCH_OBJ);

		return $response->nickname === $nickname && $response->password === $password;
	}

	/**
	 * Ajoute un nouveau compte utilisateur si le pseudonyme est valide et disponible et
	 * si le mot de passe est valide. La méthode peut retourner un des messages d'erreur qui suivent :
	 * - "Le pseudo doit contenir entre 3 et 10 lettres.";
	 * - "Le mot de passe doit contenir entre 3 et 10 caractères.";
	 * - "Le pseudo existe déjà.".
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean|string True si le couple a été ajouté avec succès, un message d'erreur sinon.
	 */
	public function addUser(string $nickname, string $password): bool|string
	{
		$err_mess = "";

		if($this->checkNicknameValidity($nickname) !== 0) {

			$err_mess .= "Le pseudo doit contenir entre 3 et 10 lettres...\n";
		}

		if($this->checkPasswordValidity($password) !== 0) {

			$err_mess .= "Le mot de passe doit contenir entre 3 et 10 caractères...\n";
		}

		if($this->checkNicknameAvailability($nickname) !== 0) {

			$err_mess .= "Le pseudo existe déjà...\n";
		}

		if($err_mess !== "") {
			return $err_mess;
		}
		$this->connection->exec(
			"INSERT INTO users (nickname, password) VALUES ('$nickname', '$password')"
		);

		return 0;
	}

	/**
	 * Change le mot de passe d'un utilisateur.
	 * La fonction vérifie si le mot de passe est valide. S'il ne l'est pas,
	 * la fonction retourne le texte 'Le mot de passe doit contenir entre 3 et 10 caractères.'.
	 * Sinon, le mot de passe est modifié en base de données et la fonction retourne true.
	 *
	 * @param string $nickname Pseudonyme de l'utilisateur.
	 * @param string $password Nouveau mot de passe.
	 * @return boolean|string True si le mot de passe a été modifié, un message d'erreur sinon.
	 */
	public function updateUser(string $nickname, string $password): bool|string
	{
		if($this->checkPasswordValidity($password) === 2) {

			return "Le mot de passe doit contenir entre 3 et 10 caractères...\n";
		}
		$stmt = $this->connection->prepare("UPDATE users SET password=? WHERE nickname=?");
		return $stmt->execute([$password, $nickname]);
	}

	/**
	 * Sauvegarde un sondage dans la base de donnée et met à jour les indentifiants
	 * du sondage et des réponses.
	 *
	 * @param Survey survey Sondage à sauvegarder.
	 * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
	 */
	public function saveSurvey($survey) {
		$survey_id = $this->connection->query(

			"SELECT id FROM surveys ORDER BY id DESC LIMIT 1"

		)->fetch(PDO::FETCH_OBJ);

		$stmt = $this->connection->prepare(

			"INSERT INTO surveys (id, question, owner) VALUES (:id, :question, :owner)"

		);
		$stmt->execute([
			':id' => ++$survey_id->id ,
			':question' => $survey->getQuestion(),
			':owner' => $survey->getOwner()
		]);
	}

	/**
	 * Sauvegarde une réponse dans la base de donnée et met à jour son indentifiant.
	 *
	 * @param Survey $response Réponse à sauvegarder.
	 * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
	 */
	public function saveResponse($response) {

		$survey_id = $this->connection->query(

			"SELECT id FROM surveys ORDER BY id DESC LIMIT 1"

		)->fetch(PDO::FETCH_OBJ);

		$response_id = $this->connection->query(

			"SELECT id FROM responses ORDER BY id DESC LIMIT 1"

		)->fetch(PDO::FETCH_OBJ);

		$stmt = $this->connection->prepare(

			"INSERT INTO responses (id, id_survey, title) VALUES (:id, :id_survey ,:title)"
		);

		$stmt->execute([
			':id' => ++$response_id->id,
			':id_survey' => ++$survey_id->id,
			':title' => $response
		]);
	}

	/**
	 * Charge l'ensemble des sondages créés par un utilisateur.
	 *
	 * @param string $owner Pseudonyme de l'utilisateur.
	 * @return bool|array(Survey)| Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByOwner($owner) {

		$surveys = $this->connection->query(

			"SELECT * FROM surveys WHERE owner='$owner'",
			PDO::FETCH_ASSOC

		)->fetchAll();

		if($surveys) {

			return $surveys;
		}
		return false;
	}

	/**
	 * Charge l'ensemble des sondages dont la question contient un mot clé.
	 *
	 * @param string $keyword Mot clé à chercher.
	 * @return bool|array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByKeyword($keyword) {

		$surveys = $this->connection->query(

			"SELECT * FROM surveys WHERE question LIKE '%$keyword%'",
			PDO::FETCH_ASSOC

		)->fetchAll();

		if($surveys) {

			return $surveys;
		}
		return false;
	}


	/**
	 * Enregistre le vote d'un utilisateur pour la réponse d'indentifiant $id.
	 *
	 * @param int $id Identifiant de la réponse.
	 * @return boolean True si le vote a été enregistré, false sinon.
	 */
	public function vote($id) {

		if(!$this->connection->query(

			"UPDATE response SET count=count++ WHERE id='$id'",
			PDO::FETCH_OBJ

		)){
			return false;
		}
		return true;
	}

	/**
	 * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
	 * @param array $arraySurveys Tableau de lignes.
	 * @return array | bool tableau de sondages ou false si une erreur s'est produite.
	 */
	private function loadSurveys($arraySurveys) {

		$q = $this->connection->query(

			"SELECT * FROM surveys",
			PDO::FETCH_OBJ

		)->fetchAll();
		if(count($q) !== 0) {
			return false;
		}

		$surveys = array();
		foreach ($arraySurveys as $survey) {

			$surveys[] = $survey;
		}
		return $surveys;
	}

    /**
     * Construit un tableau de réponses à partir d'un tableau de ligne de la table 'responses'.
     * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
     *
     * @param $arrayResponses
     * @return array|boolean Le tableau de réponses ou false si une erreur s'est produite.
     */
	private function loadResponses($arrayResponses) {
		$q = $this->connection->query(
			"SELECT * FROM responses",
			PDO::FETCH_OBJ
		)->fetchAll();
		if(count($q) === 0) {
			return false;
		}


		$responses = array();
		foreach ($arrayResponses as $response) {

			$responses[] = $response;
		}
		return $responses;
	}

}


