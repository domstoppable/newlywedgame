<?php
	/**
	 * Interface for the score board/answer display
	 **/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width' />
		
		<title>Newly Wed Game</title>
		
		<style type="text/css">
			body {
				text-align: center;
				background-color: #000;
				color: #fff;
				background-image: url(images/newlywedgame.png);
				background-position: center 0%;
				background-repeat: no-repeat;
				margin-top: 250px;
			}

			.button {
				font-size: 20pt;
				padding: 0.25em;
				padding-left: 1em;
				padding-right: 1em;
				margin: 0.5em;
				cursor: pointer;
				background: #333;
				border-radius: 5px;
			}

			a, a:visited, a:hover {
				text-decoration: none;
				color: #fff;
			}
		</style>
		
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<table style="margin: auto"><tr><td>
			<a href="players.php"><div class='button'>Player Panel</div></a>
			<a href="scoreboard.php"><div class='button'>Scoreboard</div></a>

		</td></tr></table>
	</body>
</html>
