<?php

require_once('DatabaseClient.php');

class Backend {
	private function __construct($db){
		$this->db = $db;
	}

	public static function instance(){
		static $instance = null;
		$db = new DatabaseClient(
			'mysql',
			'NewlyWedDBUser',
			'dBX4H5x7gHLVwMMN',
			'localhost',
			'NewlyWedGame'
		);
		if(!$instance) $instance = new Backend($db);
		
		return $instance;
	}

	public function startTransaction(){
		try{
			$this->db->beginTransaction();
			return true;
		}catch(PDOException $exc){
			return false;
		}
	}

	public function commit(){
		$this->db->commit();
	}
	
	public function rollback(){
		$this->db->rollback();
	}
	
	function addPlayer($firstName, $lastName, $gender){
		$sql = 'INSERT INTO players (firstName, lastName, gender) VALUES (?, ?, ?)';
		$this->db->query($sql, $firstName, $lastName, $gender);

		return $this->db->getLastInsertID('players');
	}

	function createTeam($playerID1, $playerID2, $ordinal){
		$sql = 'INSERT INTO teams (playerID1, playerID2, ordinal) VALUES (?, ?, ?)';
		$this->db->query($sql, $playerID1, $playerID1, $ordinal)->fetchAll();
	}

	function getQuestionsForGender($gender){
		$sql = '
			SELECT * FROM questions
			WHERe gender = ?
			ORDER BY round, ordinal';
		return $this->db->query($sql, $gender)->fetchAll();
	}

	function getPlayer($playerID){
		$sql = 'SELECT * FROM players WHERE playerID = ?';
		return $this->db->query($sql, $playerID)->fetchArray();
	}

	function getQuestion($questionID){
		$sql = 'SELECT * FROM questions WHERE questionID = ?';
		return $this->db->query($sql, $questionID)->fetch();
	}

	function getChoices($questionID){
		$sql = 'SELECT * FROM answerChoices WHERE questionID = ?';
		return $this->db->query($sql, $questionID)->fetchAll();
	}

	function answerQuestion($playerID, $questionID, $answer){
		$question = $this->getQuestion($questionID);
		if($question['questionType'] == 'multiple-choice'){
			$sql = 'INSERT INTO answerChoices (questionID, playerID, optionID) VALUES (?, ?, ?)';
			$this->db->query($sql, $questionID, $playerID, $answer);
		}else{
			$sql = 'INSERT INTO shortAnswers (questionID, playerID, answer) VALUES (?, ?, ?)';
			$this->db->query($sql, $questionID, $playerID, $answer);
		}
	}

	function getAnswer($questionID, $playerID){
		$question = $this->getQuestion($questionID);
		if($question['questionType'] == 'multiple-choice'){
			$sql = '
				SELECT * FROM answerChoices
					JOIN answerOptions ON answerChoices.optionID = answerOptions.optionID
				WHERE questionID = ?
					AND playerID = ?';
		}else{
			$sql = '
				SELECT * FROM shortAnswers
				WHERE questionID = ?
					AND playerID = ?';
		}
		return $this->db->query($sql, $questionID)->fetchAll();
	}
}
