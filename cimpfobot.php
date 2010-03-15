<?php

include("bot/cimpfobot.confs.inc.php");
include("bot/cimpfobot.inc.php");

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
		
		
		if (!$oG_CCV->IsOnUA())
		{
			for ($i = 0; $i < count($asMsgs); $i++)
			{
				$iMinSecs = 3;
				$iMaxSecs = (I_NEXTSECSRUN / count($asMsgs));

				$oSubmitter->SubmitMessage($asMsgs[$i]);
				
				if ($i < count($asMsgs))
					sleep(rand($iMinSecs, $iMaxSecs));
			}
			
			if (count($asMsgs) == 0)
				$oSubmitter->NoticeNoMessages();
			else
				$oSubmitter->NoticeCompletion();
		}
		
		//
		//// RSS
		//

		//
		//// PUBLUSHING EMAIL REPORT
		//
		if (count($oG_CCV->asCimpfoPublications))
			$oSubmitter->AlertPublishing();
		
		//
		//// PRINT VALUES
		//
		if ($oG_CCV->IsOnUA())
		{
			if (count($oG_CCV->asCimpfoPublications))
			{
				foreach ($oG_CCV->asCimpfoPublications as $sMsg)
				{
					$oG_CCV->PublishMessage($sMsg);
				}
			}
		}
	}

	$oG_CCV->PrintValues();
}

?>