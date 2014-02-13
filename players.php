<?php
	/**
	 * Interface for participants to enter:
	 * 	Name
	 * 	Gender
	 * 	Answer questions
	 **/

	require_once('config.php');
	session_start();

	$backend = Backend::instance();
	if(empty($_SESSION['player']) && !empty($_POST)){
		try{
			$backend->startTransaction();
			
			if(empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['gender'])){
				throw new Exception('Missing data');
			}
			$playerID = $backend->addPlayer($_POST['firstName'], $_POST['lastName'], $_POST['gender']);
			$_SESSION['player'] = $backend->getPlayer($playerID);

			$backend->commit();
		}catch(Exception $exc){
			$backend->rollback();
			logVar($exc);
			$_SESSION['error'] = $exc->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />

		<title>Newly Wed Game - Player Questionnaire</title>
		
		<style type="text/css">
		</style>
		
		<script type="text/javascript">
		</script>
	</head>
	<body>

<?php

echo getError();
if(empty($_SESSION['player'])){
?>
		<form method="post">
			<table>
				<tr>
					<td>First Name:</td>
					<td><input type="text" name="firstName" value="<?php @$_POST['firstName'] ?>" /></td>
				</tr>
				<tr>
					<td>Last Name: </td>
					<td><input type="text" name="lastName" value="<?php @$_POST['lastName'] ?>" /></td>
				</tr>
				<tr>
					<td>Gender: </td>
					<td>
						<ul>
							<label><input type="radio" name="gender" value="female" />Female</label><br/>
							<label><input type="radio" name="gender" value="male" />Male</label>
						</ul>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>
<?php
}else{
?>
		<h3>Welcome <?php $_SESSION['player']['firstName'] ?>!</h3>
		Questions go here
<?php
		echo "<br/><pre>";
		print_r($_SESSION['player']);
}
?>


	</body>
</html>
