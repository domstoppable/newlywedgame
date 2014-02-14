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
				$playerID = $backend->addPlayer($_POST['firstName'], $_POST['lastName'], $_POST['gender']);
				$_SESSION['player'] = $backend->getPlayer($playerID);

				$backend->commit();
			}catch(Exception $exc){
				$backend->rollback();
				logVar($exc);
				$_SESSION['error'] = 'Oh no! :( An error has occurred while registering you: ' . $exc->getMessage();
			}
		}elseif($_POST['action'] == 'Login'){
			$playerID = $_POST['playerID'];
			$_SESSION['player'] = $backend->getPlayer($playerID);
			header("Location: $_SERVER[PHP_SELF]");
			exit();
		}
	}
	if(!empty($_SESSION['player']) && !empty($_POST['answers'])){
		$errors = [];
		foreach($_POST['answers'] as $questionID=>$answer){
			try{
				$backend->startTransaction();
				$backend->answerQuestion($_SESSION['player']['playerID'], $questionID, $answer);
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
		
		<style type="text/css">
			body {
				background-color: #000;
				color: #fff;
				background-image: url(images/newlywedgame.png);
				background-position: center 0%;
				background-repeat: no-repeat;
				margin-top: 225px;
				margin-bottom: 2em;
			}

			input, select {
				width: 100%;
			}
			
			input[type=submit] {
				font-size: 110%;
			}

			form table {
				width: 100%;
			}

			hr {
				margin-top: 2em;
				margin-bottom: 2em;
			}

			img {
				border: none;
			}
		</style>
		
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<table style="margin: auto;"><tr><td>
		<!--<a href="./"><img src="images/newlywedgame.png" /></a>-->

<?php

echo formatError();
echo formatMessage();
if(empty($_SESSION['player'])){
	$players = $backend->getPlayers();
	$existingPlayerWidget = '';
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
