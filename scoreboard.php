<?php
	/**
	 * Interface for the score board/answer display
	 **/

	require_once('config.php');
	session_start();
	
	$backend = Backend::instance();
	$teams = indexBy($backend->getTeams(), 'ordinal');

	if(!empty($_GET['scores'])){
		$dataToSend = false;
		for($j=0; $j<3 && !$dataToSend; $j++){
			$teams = indexBy($backend->getTeams(), 'ordinal');
			for($i=0; $i<4; $i++){
				if($_GET['scores'][$i] != $teams[$i]['score']){
					echo "s|$i|" . $teams[$i]['score'] . "\n";
					$dataToSend = true;
				}
			}

			$state = $backend->getActiveIDs();
			if($state['activeQuestionID'] != $_GET['activeQuestionID'] || $state['activePlayerID'] != $_GET['activePlayerID']){
				echo "q|$state[activeQuestionID]\n";
				echo "p|$state[activePlayerID]\n";
				$dataToSend = true;
			}
			
			if(!$dataToSend){
				sleep(1);
			}else{
				break;
			}
		}
		exit();
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width' />

		<title>The Newlywed Game</title>

		<link rel="stylesheet" href="style/main.css" type="text/css" />
		<style type="text/css">
			body {
				margin-top: 225px;
				font-size: 20pt;
				font-weight: bold;
			}

			.scorebox {
				margin-bottom: 1.5em;
				text-align: center;
			}

			.score, .loves { color: #f00; }
			li {
				font-weight: normal;
			}
			.selected, #activePlayerName, #question, #answer {
				color: #fff;
			}
			.selected {
				background-color: #333;
			}

			#questionContainer {
				position: absolute;
				width: 100%;
				color: #999;
				display: none;
			}
			
			#questionContainer table {
				margin: auto;
			}

			#question {
				font-style: italic;
				font-size: 110%;
			}

			@media only screen  and (min-device-width : 800px) {
				.scorebox {
					position: absolute;
				}
				
				#team0-box, #team3-box { top: 5em; }
				#team1-box, #team2-box { top: 10em; }

				#team0-box { left: 2em; }
				#team3-box { right: 2em; }
				
				#team1-box { left: 20%; }
				#team2-box { right: 20%; }
			}

		</style>
		
		<script type="text/javascript">
			alert
			var activeQuestionID = null;
			var activePlayerID = null;

			var questions = <?php echo json_encode($backend->getFullQuestionData()) ?>;
			var players = <?php echo json_encode(indexBy($backend->getPlayers(), 'playerID')) ?>;
			
			function e(id){ return document.getElementById(id); }

			function getUpdate(){
				var httpRequest = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
				httpRequest.onreadystatechange = function(){
					if (httpRequest.readyState === 4) {
						if (httpRequest.status === 200 && httpRequest.responseText != '') {
							var lines = httpRequest.responseText.split("\n");
							for(var i=0; i<lines.length; i++){
								var record = lines[i].split("|");

								if(record[0] == 'q'){
									activeQuestionID = record[1];
								}else if(record[0] == 'p'){
									activePlayerID = record[1];
									if(activePlayerID != ''){
										e('activePlayerName').innerHTML = players[activePlayerID]['firstName'];
										if(players[activePlayerID]['gender'] == 'female'){
											e('genderPossessivePronoun').innerHTML = 'She';
										}else{
											e('genderPossessivePronoun').innerHTML = 'He';
										}
									}
								}else{
									setScore(record[1], record[2]);
								}
								if(activeQuestionID != ''){
									var q = questions[activeQuestionID];
									e('question').innerHTML = q['question'];
									
									if(q['questionType'] == 'multiple-choice'){
										e('answer').style.display = 'none';

										var choiceList = e('choices');
										while(choiceList.hasChildNodes()){
											choiceList.removeChild(choiceList.lastChild);
										}
										for(var optionID in q['choices']){
											var el = document.createElement('li');
											el.innerHTML = q['choices'][optionID]['answer'];
											if(q['answers'][activePlayerID] == optionID){
												el.className = 'selected';
											}
											e('choices').appendChild(el);
										}
										e('choices').style.display = 'block';
									}else{
										e('choices').style.display = 'none';
										e('answer').innerHTML = q['answers'][activePlayerID];
										e('answer').style.display = 'block';
									}

									setScoresVisible(false);
									e('questionContainer').style.display = 'block';
								}else{
									e('questionContainer').style.display = 'none';
									setScoresVisible(true);
								}
							}
						}
						getUpdate();
					}
				};
				var request = '?activeQuestionID=' + activeQuestionID + '&activePlayerID=' + activePlayerID;
				for(var i=0; i<4; i++){
					request += '&scores[' + i + ']=' + e('team' + i + '-score').innerHTML;
				}
				httpRequest.open('GET', request, true);
				httpRequest.send(null);
			}

			function setScoresVisible(showThem){
				var els = document.getElementsByClassName('scorebox');
				for(var i=0; i<els.length; i++){
					els[i].style.display = showThem ? 'block' : 'none';
				}
			}

			function setScore(team, score){
				if(team == "" || !team) return;
				e('team' + team + '-score').innerHTML = score;
			}
			
			function pageLoaded(){
				setTimeout(getUpdate, 2000);
			}
		</script>
	</head>
	<body onload="pageLoaded();">

<?php
foreach($teams as $id=>$team){
	echo "
		<div class='scorebox' id='team$id-box'>
			<div id='team$id-name' class='teamName'>" . $team['player1']['firstName'] . ' <span class="loves">&hearts;</span> ' . $team['player2']['firstName'] . "</div>
			<div id='team$id-score' class='score'>$team[score]</div>
		</div>";
}
?>
		<div id="questionContainer">
			<table><tr><td>
				<p>We asked <span id="activePlayerName"></span>...</p>
				<div id="question"></div>
				<p><span id="genderPossessivePronoun"></span> answered:</p>
				<ul id="choices"></ul>
				<div id="answer"></div>
			</td></tr></table>
		</div>
	</body>
</html>
