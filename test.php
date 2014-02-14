<?
require_once('config.php');

$backend = Backend::instance();

//$backend->setActiveAnswer('2f1', 0);
$x = $backend->getFullQuestionData();
print_r($x);
