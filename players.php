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
	}
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
			$_SESSION['error'] = 'Oh no! :( An error has occurred while registering you: ' . $exc->getMessage();
		}
	}
	if(!empty($_SESSION['player']) && !empty($_POST['answers'])){
		$errors = [];
		foreach($_POST['answers'] as $questionID=>$answer){
			if(empty($answer)) continue;
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

		<title>Newly Wed Game - Player Questionnaire</title>
		
		<style type="text/css">
		</style>
		
		<script type="text/javascript">
		</script>
	</head>
	<body>

<?php

echo formatError();
echo formatMessage();
if(empty($_SESSION['player'])){
	$players = $backend->getPlayers();
	$existingPlayerWidget = '';
	if(!empty($players)){
		foreach($players as $player){
			$$existingPlayerWidget .= "<option value='$player[playerID]'>$player[firstName] $player[lastName]</option>";
		}
		$existingPlayerWidget = "<select name='playerID'>$widget</select>";
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
					<td colspan="2"><input type="submit" value="Submit" /></td>
				</tr>
			</table>
	<?php if(!empty($existingPlayerWidget)){ ?>
			<h2>Existing Player</h2>
			<table>
				<tr>
					<td>Name:</td>
					<td><?php echo $widget ?></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Submit" /></td>
				</tr>
			</table>
	<?php } ?>
		</form>
<?php }else{ ?>
		<h2>Welcome <?php echo $_SESSION['player']['firstName'] ?>!</h2>
		[ <a href="?logout=1">logout</a> ]
		<form method='post'><pre>
<?php
		$questions = $backend->getQuestionsForGender($_SESSION['player']['gender']);
		foreach($questions as $question){
			echo "<h3>$question[question]</h3>\n";
			if($question['questionType'] == 'multiple-choice'){
				$options = $backend->getOptions($question['questionID'], $_SESSION['player']['playerID']);
				$blah = '';
				echo "<select name='answers[$question[questionID]]'>\n";
				echo "	<option value='' />\n";
				foreach($options as $option){
					echo "	<option value='$option[optionID]'>$option[answer]</option>\n";
					$blah .= print_r($option, true);
				}
				echo "</select>\n";
				echo $blah;
			}else{
				echo "<input type='text' name='answers[$question[questionID]]' />\n";
			}
			echo "<hr/>\n";
		}
		
		echo "<br/><pre>";
		print_r($_SESSION['player']);
		print_r($_POST);
}
?>
		</form>

	</body>
</html>
