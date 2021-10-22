<?
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/javascript; charset=UTF-8');
include ("../lib/config/config.php");
include _CLASS_DIR."/class.API.php";

$objAPI = new API();
$objAPI->loadClass($objAPI->reqData['method']);
$objAPI->runMethod();   // run method()
?>