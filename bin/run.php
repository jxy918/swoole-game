<?php
date_default_timezone_set('Asia/Shanghai');
header("Content-type: text/html; charset=utf-8"); 

use \Game\Core\GameServer;

require __DIR__ .'/../vendor/autoload.php';

GameServer::getInstance()->initServer()->start();
