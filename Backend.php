<?php

require_once('config.php');
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

	function createTeam($ordinal, $player1ID, $player2ID){
		$this->db->query('DELETE FROM teams WHERE ordinal = ?', $ordinal);
		if(empty($player1ID) xor empty($player2ID)){
			throw new UserException("Must specify both playerID's. Received '$player1ID' & '$player2ID'.");
		}
		if(!empty($player1ID) && !empty($player2ID)){
			$sql = 'INSERT INTO teams (ordinal, player1ID, player2ID) VALUES (?, ?, ?)';
			$this->db->query($sql, $ordinal, $player1ID, $player2ID);
		}
	}

	function getQuestionsForGender($gender){
		$sql = '
			SELECT * FROM questions
			WHERE gender = ?
			ORDER BY round, ordinal';
		return $this->db->query($sql, $gender)->fetchAll();
	}

	function getFullQuestionData(){
		$questions = indexBy($this->db->query('SELECT * FROM questions ORDER BY round, ordinal')->fetchAll(), 'questionID');
		$players = indexBy($this->getPlayers(), 'playerID');
		
		foreach($questions as $questionID=>$question){
			if($question['questionType'] == 'multiple-choice'){
				$questions[$questionID]['choices'] = indexBy($this->getOptions($questionID), 'optionID');
			}

			$questions[$questionID]['answers'] = array();
			foreach($players as $playerID=>$p){
				$answer = $this->getAnswer($playerID, $questionID);
				if(!empty($answer['answer'])){
					if(!empty($answer['optionID'])){
						$questions[$questionID]['answers'][$playerID] = $answer['optionID'];
					}else{
						$questions[$questionID]['answers'][$playerID] = $answer['answer'];
					}
				}
			}
		}

		return $questions;
	}

	function getPlayer($playerID){
		$sql = 'SELECT * FROM players WHERE playerID = ?';
		return $this->db->query($sql, $playerID)->fetch();
	}

	function getPlayers(){
		$sql = 'SELECT * FROM players ORDER BY firstName, lastName';
		return $this->db->query($sql)->fetchAll();
	}

	function getTeams(){
		$sql = 'SELECT teams.* FROM teams';
		$teams = $this->db->query($sql)->fetchAll();
		foreach($teams as $i=>$team){
			$teams[$i]['player1'] = $this->getPlayer($team['player1ID']);
			$teams[$i]['player2'] = $this->getPlayer($team['player2ID']);
		}
		return $teams;
	}

	function getQuestion($questionID){
		$sql = 'SELECT * FROM questions WHERE questionID = ?';
		return $this->db->query($sql, $questionID)->fetch();
	}

	function getOptions($questionID, $playerID=null){
		$params = Array('questionID'=>$questionID);
		
		$sql = 'SELECT * FROM answerOptions';
		if(empty($playerID)){
			$sql = 'SELECT *';
		}else{
			$sql = '
				SELECT *, (
					SELECT COUNT(*) FROM answerChoices
					WHERE answerChoices.optionID = answerOptions.optionID
						AND questionID = :questionID
						AND playerID = :playerID
				) AS selected';
			$params['playerID'] = $playerID;
		}
		$sql .= '
			FROM answerOptions
			WHERE questionID = :questionID';
		
		return $this->db->query($sql, $params)->fetchAll();
	}

	function answerQuestion($playerID, $questionID, $answer){
		$question = $this->getQuestion($questionID);
		if($question['questionType'] == 'multiple-choice'){
			$this->db->query('DELETE FROM answerChoices WHERE playerID=? AND questionID=?', $playerID, $questionID);
			if(!empty($answer)){
				$sql = 'INSERT INTO answerChoices (questionID, playerID, optionID) VALUES (?, ?, ?)';
				$this->db->query($sql, $questionID, $playerID, $answer);
			}
		}else{
			$this->db->query('DELETE FROM shortAnswers WHERE playerID=? AND questionID=?', $playerID, $questionID);
			$sql = 'INSERT INTO shortAnswers (questionID, playerID, answer) VALUES (?, ?, ?)';
			$this->db->query($sql, $questionID, $playerID, $answer);
		}
	}

	function getAnswer($playerID, $questionID){
		$question = $this->getQuestion($questionID);
		if($question['questionType'] == 'multiple-choice'){
			$sql = '
				SELECT * FROM answerChoices
					JOIN answerOptions ON answerChoices.optionID = answerOptions.optionID
				WHERE answerChoices.questionID = ?
					AND playerID = ?';
		}else{
			$sql = '
				SELECT * FROM shortAnswers
				WHERE questionID = ?
					AND playerID = ?';
		}
		return $this->db->query($sql, $questionID, $playerID)->fetch();
	}

	function setActiveAnswer($round, $question, $playerID){
		$player = $this->getPlayer($playerID);
		$questionID = $round . substr($player['gender'], 0, 1) . $question;
		$this->setActiveAnswerByIDs($questionID, $playerID);
	}

	function setActiveAnswerByIDs($questionID, $playerID){
		$sql = 'UPDATE system SET activeQuestionID = ?, activePlayerID = ?';
		$this->db->query($sql, $questionID, $playerID);
	}

	function clearActiveAnswer(){
		$this->db->query('UPDATE system SET activeQuestionID = NULL, activePlayerID = NULL');
	}

	function getActiveQuestion(){
		$questionID = $this->db->query('SELECT activeQuestionID FROM system')->fetch()['activeQuestionID'];
		return $this->getQuestion($questionID);
	}

	function getActiveIDs(){
		return $this->db->query('SELECT * FROM system')->fetch();
	}

	function addPoints($round, $playerID, $mult=1){
		$pointsByRound = [0, 10, 15, 25];
		$sql = 'UPDATE teams SET score = score + :points WHERE player1ID = :playerID or player2ID = :playerID';
		$params = array(
			'points' => $pointsByRound[$round] * $mult,
			'playerID' => $playerID
		);
		$this->db->query($sql, $params);
	}
	
	function subtractPoints($round, $playerID){
		$this->addPoints($round, $playerID, -1);
	}
}
