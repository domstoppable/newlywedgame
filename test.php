<?
require_once('config.php');

$backend = Backend::instance();
echo $backend;

$questions = $backend->getQuestionsForGender('male');
print_r($questions);
