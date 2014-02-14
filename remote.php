<?php
	/**
	 * Interface for scoreboard remote
	 **/

	require_once('config.php');
	session_start();
	if(empty($_SESSION['isAdmin'])){
		header('Location: admin.php');
		exit();
	}
	
	$backend = Backend::instance();
	if(!empty($_POST)){
		if($_POST['action'] == 'Display'){
			$backend->setActiveAnswer($_POST['round'], $_POST['question'], $_POST['playerAsked']);
		}elseif($_POST['action'] == 'Hide'){
			$backend->clearActiveAnswer();
		}elseif($_POST['action'] == '+ Correct'){
			$backend->addPoints($_POST['round'], $_POST['playerAsked']);
		}elseif($_POST['action'] == '- Wrong'){
			$backend->subtractPoints($_POST['round'], $_POST['playerAsked']);
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />

		<title>Newly Wed Game - Remote Control</title>
		<link rel="stylesheet" href="style/main.css" type="text/css" />
	</head>
	<body>
		<table><tr><td>
			
		<h1>Remote Control</h1>
		<form method="post">
			<table>
				<tr>
					<td>Round</td>
					<td>
<?php

for($i=1; $i<4; $i++){
	$selected = @$_POST['round'] == $i ? "checked='1'" : '';
	echo "
						<div><label><input type='radio' name='round' value='$i' $selected/>Round $i</label></div>";
}

?>
					</td>
				</tr>
				<tr>
					<td>Question</td>
					<td>
<?php

for($i=1; $i<4; $i++){
	$selected = @$_POST['question'] == $i ? "checked='1'" : '';
	echo "
						<div><label><input type='radio' name='question' value='$i' $selected/>Question $i</label></div>";
}

?>
					</td>
				</tr>
				<tr>
					<td>Player</td>
					<td>
<?php


$teams = $backend->getTeams();
foreach($teams as $team){
	$p2selected = @$_POST['playerAsked'] == $team['player2ID'] ? "checked='1'" : '';
	$p1selected = @$_POST['playerAsked'] == $team['player1ID'] ? "checked='1'" : '';
	echo "
						<div><label><input type='radio' name='playerAsked' value='$team[player2ID]' $p2selected/>" . $team['player1']['firstName'] . ' ' . $team['player1']['lastName'] . "</label></div>
						<div><label><input type='radio' name='playerAsked' value='$team[player1ID]' $p1selected/>" . $team['player2']['firstName'] . ' ' . $team['player2']['lastName'] . "</label></div>
						<hr/>";
}

?>
					</td>
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><input type="submit" name="action" value="Display"/></td>
					<td><input type="submit" name="action" value="Hide"/></td>
				</tr>
				<tr>
					<td><input type="submit" name="action" value="+ Correct"/></td>
					<td><input type="submit" name="action" value="- Wrong"/></td>
				</tr>
			</table>
		</form>

		
		</td></tr></table>
	</body>
</html>
