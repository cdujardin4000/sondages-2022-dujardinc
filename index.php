<?php 
session_start();

function getActionByName($name): Action
{
	$name .= 'Action';
	require("actions/$name.inc.php");
	return new $name();
}

function getViewByName($name): View
{
	$name .= 'View';
	require("views/$name.inc.php");
	return new $name();
}

function getAction(): Action
{

	$action = $_REQUEST['action'] ?? 'Default';

	$actions = array(
		'Default',
		'Login',
		'Logout',
		'SignUp',
		'SignUpForm',
		'UpdateUserForm',
		'UpdateUser',
		'GetMySurveys',
		'AddSurvey',
		'AddSurveyForm',
		'Search',
		'Vote'
	);

	if (!in_array($action, $actions, true)) {
		$action = 'Default';
	}
	return getActionByName($action);
}

$action = getAction();
$action->run();
$view = $action->getView();
$model = $action->getModel();
$view->run($model);


