<?php

include("twitter.inc.php");

$iMaxSeconds = 300;
$iSecondSteps = 60;
$iTotalSeconds = 0;

$oTPU = new CTwitterParsableUpdates();

$oTPU->PrepareOptions();

while ($iTotalSeconds < $iMaxSeconds)
{
	$oTPU->Execute();

	$oTPU->ProcessTwitsList();

	$oTPU->PublishLatestTwits();
	
	sleep($iSecondSteps);
	
	$iTotalSeconds += $iSecondSteps;
}

?>