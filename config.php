<?php

require_once('Backend.php');

class UserException extends Exception {}

mt_srand();

function errorHandler($code, $message, $file, $line, $vars){
	if(error_reporting() == 0) return;
	static $errorTypes = array (
		E_ERROR              => 'Error',
		E_WARNING            => 'Warning',
		E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		E_CORE_ERROR         => 'Core Error',
		E_CORE_WARNING       => 'Core Warning',
		E_COMPILE_ERROR      => 'Compile Error',
		E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
		E_STRICT             => 'Runtime Notice',
		E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	);
	
	$msg = "[" . date('Y-m-d H:i:s') . "] [$errorTypes[$code]] [CRDonors : $message] in [$file:$line]\n";
	error_log($msg);
}

function exceptionHandler($exception){
	$t = $exception->getTrace();
	if(!empty($t[0])) $t = $t[0];
	trigger_error("Uncaught exception in ".$t['file']." line ".$t['line'].": " . $exception->getMessage(), E_USER_ERROR);
}

function logVar($var){
	trigger_error(print_r($var, true));
}

function formatError(){
	if(!empty($_SESSION['error'])){
		$msg = "<div class='error'>$_SESSION[error]</div>\n";
		unset($_SESSION['error']);
		
		return $msg;
	}
	return '';
}

function formatMessage(){
	if(!empty($_SESSION['message'])){
		$msg = "<div class='message'>$_SESSION[message]</div>\n";
		unset($_SESSION['message']);
		
		return $msg;
	}
	return '';
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
