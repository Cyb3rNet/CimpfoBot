<?php

include("cimpfo.inc.php");

define('B_RUN', true);

define('DEBUGPARAM', 'UA');

//////
////
//
define('I_NEXTSECSRUN', 900);
//
////
//////

// For debug purposes
$oG_CCV = new CCimpfoCollectedValues((isset($_GET[DEBUGPARAM]) ? true : false));

if (B_RUN)
{
	// Get autorisation to publish
	$oAutorisator = new CCimpfoPublishingAutorisator();

	$oAutorisator->ProcessRoomUsersListing();

	$oG_CCV->iCampfireNumUsers = $oAutorisator->TellNumberOfUsersInRoom();

	if ($oAutorisator->TellAutorisation() == true AND B_RUN)
	{
		//
		//// CAMPFIRE MESSAGE SUBMITTER
		//
		
		$oSubmitter = new CCimpfoMessageSubmitter();

		//
		//// TWITTER
		//
		
		$oTUP = new CTwitterUpdatesPublisher();
		
		$oTUP->RequestUpdates();
		
		$oTUP->SplitUpdatesResponse();
		
		$oTUP->LoadLastUpdateId();
		
		$asMsgs = $oTUP->PublishUpdates();
		
		$oG_CCV->iTwitterNumPublications = count($asMsgs);
		
		$oTUP->WriteLastUpdateId();
		
		foreach ($asMsgs as $sMsg)
		{
			$iMinSecs = 3;
			$iMaxSecs = (I_NEXTSECSRUN / count($asMsgs));
		
			if (!$oG_CCV->IsOnUA())
			{
				$oSubmitter->SubmitMessage($sMsg);
			
				sleep(rand($iMinSecs, $iMaxSecs));
			}
		}
		
		//
		//// RSS
		//

		//
		//// PUBLUSHING EMAIL REPORT
		//
		
		$oSubmitter->AlertPublishing();
		
		//
		//// PRINT VALUES
		//
		if ($oG_CCV->IsOnUA())
		{
			foreach ($oG_CCV->asCimpfoPublications as $sMsg)
			{
				$oG_CCV->PublishMessage($sMsg);
			}
		}
	}

	$oG_CCV->PrintValues();
}

?>