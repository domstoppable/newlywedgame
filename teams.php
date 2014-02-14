<?php
	/**
	 * Interface for joining couples and scoreboard remote
	 **/

	require_once('config.php');
	session_start();
	
	$backend = Backend::instance();

	if(!empty($_POST['teams'])){
		$errors = [];
		foreach($_POST['teams'] as $ordinal=>$team){
			try{
				$backend->createTeam($ordinal, $team['female'], $team['male']);
			}catch(UserException $exc){
				$errors[] = $exc;
			}catch(Exception $exc){
				$errors[] = $exc;
				logVar($exc);
			}
		}
		if(empty($errors)){
			$_SESSION['message'] = 'Your teams have been saved!';
			header("Location: $_SERVER[PHP_SELF]");
			exit();
		}else{
			$_SESSION['error'] = 'Oh no! :( ' . count($errors) . ' error(s) occurred while saving your teams';
		}
	}

	$teams = indexBy($backend->getTeams(), 'ordinal');
	$players = indexBy($backend->getPlayers(), 'playerID');
	
	function generatePlayerSelect($name, $playerID, $gender){
		global $players;
		
		$output = "<select name='$name'>";
		$output .= "<option value=''></option>";
		foreach($players as $id=>$player){
			if($player['gender'] != $gender) continue;
			
			$selected = $playerID == $id ? " selected='1'" : '';
			$output .= "<option value='$id'$selected>$player[firstName] $player[lastName]</option>";
		}
		$output .= "</select>";

		return $output;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width' />
		
		<title>Newly Wed Game - Remote Control</title>
		
		<style type="text/css">
		</style>
		
		<script type="text/javascript">
			var teams = <?php echo json_encode($teams) ?>;
			var players = <?php echo json_encode($players) ?>;
		</script>
	</head>
	<body>
		<table style="margin: auto;"><tr><td>

		<form method='post'>
<?php

echo formatError();
echo formatMessage();
for($i=0; $i<4; $i++){
	$player1ID = null;
	$player2ID = null;
	if(!empty($teams[$i])){
		$player1ID = $teams[$i]['player1ID'];
		$player2ID = $teams[$i]['player2ID'];
	}

	echo "
			<h3>Team " . ($i+1) . "</h3>
			<table>
				<tr>
					<td>Player 1:</td>
					<td>" . generatePlayerSelect("teams[$i][female]", $player1ID, 'female') . "</td>
				</tr>
				<tr>
					<td>Player 2:</td>
					<td>" . generatePlayerSelect("teams[$i][male]", $player2ID, 'male') . "</td>
				</tr>
			</table>
			<hr/>";
}
echo "
			<input type='submit' value='Save Teams' />";
?>
		</form>

	</td></tr></table>
	</body>
</html>
