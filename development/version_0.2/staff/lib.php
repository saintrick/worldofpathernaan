<?php
session_start();
include("../lib/config.php");
include('../adodb/adodb.inc.php');
$db = ADONewConnection("mysql"); # eg. 'mysql' or 'oci8' 
$db->Connect($server, $user, $password, $database);
include("../lib/functions.php");
?>
