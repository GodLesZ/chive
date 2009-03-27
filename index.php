<?php

// change the following paths if necessary
$yii='yii/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

function pre($_value) {
	echo "<pre>";
	print_r($_value);
	echo "</pre>";
}

function predie($_value) {
	pre($_value);
	CApplication::end();
}

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

$console = false;

if($console == true)
	$config = dirname(__FILE__).'/protected/config/dev.php';

require_once($yii);
$app = Yii::createWebApplication($config);

if(!$app->user->isGuest) {
	$app->db->connectionString = 'mysql:host=' . $app->user->host . ';dbname=information_schema';
	$app->db->username= $app->user->name;
    $app->db->password= $app->user->password;
    $app->db->autoConnect = true;
}

$app->setLanguage($app->request->getPreferredLanguage());

$app->run();