<?php

$dbuser = 'postgres';
$dbpass = 'test123';
$dbhost = 'localhost';
$dbname='srinagarhc';

$connection = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, [PDO::ATTR_PERSISTENT => true]);
?>