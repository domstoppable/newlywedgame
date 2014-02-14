<?php
	/**
	 * Interface for joining couples and scoreboard remote
	 **/

	require_once('config.php');
	session_start();
	
	$backend = Backend::instance();
	$teams = $backend->getTeams();
	$players = indexBy($backend->getPlayers(), 'playerID');

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
	</body>
</html>
