<?php


include("campfire.inc.php");

$oCf = new CCampfireListAllRooms();

$oCf->PrepareOptions();

echo $oCf->Execute();

?>