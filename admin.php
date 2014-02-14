<?php
	/**
	 * Interface for scoreboard remote
	 **/

	require_once('config.php');
	session_start();
	if(!empty($_REQUEST['logout'])){
		session_unset();
		header("Location: $_SERVER[PHP_SELF]");
		exit();
	}
	if(!empty($_POST)){
		if($_POST['password'] != 'fatcat'){
			$_SESSION['error'] = 'Invalid password';
		}else{
			$_SESSION['isAdmin'] = true;
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />

		<title>Newly Wed Game - Control Panel</title>
		
		<link rel="stylesheet" href="style/main.css" type="text/css" />
	</head>
	<body>
		<table><tr><td>
		<h1>Control Panel</h1>
<?php
echo formatError();
echo formatMessage();

if(empty($_SESSION['isAdmin'])){

?>
		<hr/>
		<p>You must be signed in the view this content.</p>
		<hr/>
		<form method="post">
			<table>
				<tr>
					<td>Password</td>
					<td><input type="password" name="password" /></td>
				</tr>
			</table>
			<input type="submit" name="action" value="Login"/>
		</form>
<?php
}else{

?>
			<a href="teams.php"><div class='button'>Team Panel</div></a>
			<a href="remote.php"><div class='button'>Remote</div></a>
			<a href="?logout=1"><div class='button'>Logout</div></a>

<?php
}

?>

		</td></tr></table>
	</body>
</html>
