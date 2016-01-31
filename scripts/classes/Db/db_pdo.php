<?php
/**
 * Если вызывается без параметров, то используются дефолтные значения
 * из конфига scripts/classes/Cfg.php
 *
 * Пример:
 * 
 * $pdo = getPdo(Cfg::DB_TECDOC_DBNAME);
 * $pst = $pdo->prepare("SELECT * FROM tablename");
 * $execRes = $pst->execute();
 * ...
 *
 */
function getPdo($_dbName="", $_dbUserName="", $_dbUserPassword="", $_dbOptionsSetNames="")
{
	$dbName = (!empty($_dbName)) ? $_dbName : Cfg::DB_NAME;
	$dbUserName = (!empty($_dbUserName)) ? $_dbUserName : Cfg::DB_USERNAME;
	$dbUserPassword = (!empty($_dbUserPassword)) ? $_dbUserPassword : Cfg::DB_USERPW;

	$pdo = new PDO('mysql:dbname=' . $dbName . ';host=localhost', $dbUserName, $dbUserPassword);

	$dbOptionsSetNames = (!empty($_dbOptionsSetNames)) ? $_dbOptionsSetNames : 'cp1251';

	$pst = $pdo->prepare('SET NAMES ' . $dbOptionsSetNames);
	$pst->execute();

	return $pdo;
}
