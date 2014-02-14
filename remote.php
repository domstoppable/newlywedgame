<?php
	/**
	 * Interface for joining couples and scoreboard remote
	 **/

	require_once('config.php');
	session_start();
	
	$backend = Backend::instance();
	$teams = $backend->getTeams();
	$players = indexBy($backend->getPlayers(), 'playerID');

	if(!empty($_POST['teams'])){
		
	}

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

		<title>Newly Wed Game - Remote Control</title>
		
		<style type="text/css">
		</style>
		
		<script type="text/javascript">
			var teams = <?php echo json_encode($teams) ?>;
			var players = <?php echo json_encode($players) ?>;
		</script>
	</head>
	<body>
		<form method='post'>
<?php

if(count($teams) < 4){
	for($i=0; $i<4; $i++){
		$player1ID = null;
		$player2ID = null;
		if($i <= count($teams)){
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
}
?>
		</form>
	</body>
</html>
