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
	if(!empty($_REQUEST['logout'])){
		session_unset();
		header("Location: $_SERVER[PHP_SELF]");
		exit();
	}
	if(empty($_SESSION['player']) && !empty($_POST)){
		if($_POST['action'] == 'Register'){
			try{
				$backend->startTransaction();
				if(empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['gender'])){
					throw new Exception('Missing data');
				}
				$playerID = $backend->addPlayer(
					htmlentities($_POST['firstName'], ENT_QUOTES),
					htmlentities($_POST['lastName'], ENT_QUOTES),
					htmlentities($_POST['gender'], ENT_QUOTES)
				);
				$_SESSION['player'] = $backend->getPlayer($playerID);

				$backend->commit();
			}catch(Exception $exc){
				$backend->rollback();
				logVar($exc);
				$_SESSION['error'] = 'Oh no! :( An error has occurred while registering you: ' . $exc->getMessage();
			}
		}elseif($_POST['action'] == 'Login'){
			if($_POST['password'] != 'wurtwurt'){
				$_SESSION['error'] = 'Invalid password';
			}else{
				$playerID = $_POST['playerID'];
				$_SESSION['player'] = $backend->getPlayer($playerID);
				header("Location: $_SERVER[PHP_SELF]");
				exit();
			}
		}
	}
	if(!empty($_SESSION['player']) && !empty($_POST['answers'])){
		$errors = [];
		foreach($_POST['answers'] as $questionID=>$answer){
			try{
				$backend->startTransaction();
				$backend->answerQuestion($_SESSION['player']['playerID'], $questionID, htmlentities($answer, ENT_QUOTES));
				$backend->commit();
			}catch(Exception $exc){
				$errors[] = $exc;
				logVar($exc);
				$backend->rollback();
			}
		}
		if(empty($errors)){
			$_SESSION['message'] = 'Your answers have been saved!';
		}else{
			$_SESSION['error'] = 'Oh no! :( ' . count($errors) . ' error(s) occurred while saving your answers';
		}

	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width' />
		
		<title>Newly Wed Game - Player Questionnaire</title>
		
		<link rel="stylesheet" href="style/main.css" type="text/css" />
		<style type="text/css">
			hr {
				margin-top: 2em;
				margin-bottom: 2em;
			}
		</style>
		
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<table><tr><td>
<?php

echo formatError();
echo formatMessage();
if(empty($_SESSION['player'])){
	$players = $backend->getPlayers();
	$existingPlayerWidget = '<option></option>';
	if(!empty($players)){
		foreach($players as $player){
			$existingPlayerWidget .= "<option value='$player[playerID]'>$player[firstName] $player[lastName]</option>";
		}
		$existingPlayerWidget = "<select name='playerID'>$existingPlayerWidget</select>";
	}
?>
		<form method="post">
			<h2>New Player</h2>
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
					<td colspan="2"><input type="submit" value="Register" name='action'/></td>
				</tr>
			</table>
	<?php if(!empty($existingPlayerWidget)){ ?>
			<hr/>
			<h2>Existing Player</h2>
			<table>
				<tr>
					<td>Name:</td>
					<td><?php echo $existingPlayerWidget ?></td>
				</tr>
				<tr>
					<td>Admin password:</td>
					<td><input type="password" name="password" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Login" name='action'/></td>
				</tr>
			</table>
	<?php } ?>
		</form>
<?php }else{ ?>
		<h2>Welcome <?php echo $_SESSION['player']['firstName'] ?>! <span style="font-weight: normal; font-size: 66.6%;">[ <a href="?logout=1">logout</a> ]</span></h2>
		<hr/>
		<form method='post'>
<?php
		$questions = $backend->getQuestionsForGender($_SESSION['player']['gender']);
		foreach($questions as $question){
			$answer = $backend->getAnswer($_SESSION['player']['playerID'], $question['questionID']);
			echo "$question[question]<br/>\n";
			if($question['questionType'] == 'multiple-choice'){
				$options = $backend->getOptions($question['questionID'], $_SESSION['player']['playerID']);
				echo "<select name='answers[$question[questionID]]'>\n";
				echo "	<option value='' />\n";
				foreach($options as $option){
					$selected = $option['optionID'] == $answer['optionID'] ? " selected='1'" : '';
					echo "	<option value='$option[optionID]'$selected>$option[answer]</option>\n";
				}
				echo "</select>\n";
			}else{
				echo "<input type='text' name='answers[$question[questionID]]' value='$answer[answer]'/>\n";
			}
			echo "<hr/>\n";
		}
		echo "
			<input type='submit' value='Save my answers' />
		</form>\n";

}
?>

		</td></tr></table>
	</body>
</html>
