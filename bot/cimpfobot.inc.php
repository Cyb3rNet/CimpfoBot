<?php

include("lib/campfire.inc.php");
include("bot/twitter.inc.php");


////
//// CLASS - FOR UA PURPOSES
////
//
class CCimpfoCollectedValues
{
	public $sTwitterXMLResponse;
	public $iTwitterNumUpdates;
	public $iTwitterNumPublications;
	public $iLastUpdateId;
	
	public $iCampfireNumUsers;
	
	public $asCimpfoPublications;
	
	private $_bIsOnUA;
	
	public function __construct($bIsOnUA)
	{
		$this->_bIsOnUA = $bIsOnUA;
	}
	
	public function IsOnUA()
	{
		return $this->_bIsOnUA;
	}
	
	public function GetTotalNumPublications()
	{
		return count($this->asCimpfoPublications);
	}
	
	public function PrintValues()
	{
		echo "Num Twitter Updates: ".$this->iTwitterNumUpdates."<br />";
		echo "Num Twitter Publications: ".$this->iTwitterNumPublications."<br />";
		echo "Num Twitter Last Update Id: ".$this->iLastUpdateId."<br />";
	
		echo "Num Campfire Logged Users: ".$this->iCampfireNumUsers."<br />";
	
		echo "Num Total Publications: ".$this->GetTotalNumPublications()."<br />";
	}
	
	public function PublishMessage($sMsg)
	{
		echo "<div style='border:1px solid black;margin:10px;font-family:Georgia;'>".$sMsg."</div>";
	}
	
	
}


////
//// CLASS - FEED SUBMISSION AUTORISATOR
////
//
class CCimpfoPublishingAutorisator extends CCampfireShowRoom
{
	private $_sXMLResponse;
	private $_aUsersId;
	private $_aUsers;
	private $_bIsAutorisedToPublish;

	public function __construct()
	{
		parent::__construct(I_CAMPFIRE_ROOMID);
		
		$this->_sXMLResponse = "";
		$this->_aUsersId = array();
		$this->_aUsers = array();
		
		$this->_bIsAutorisedToPublish = false;
	}
	
	public function ProcessRoomUsersListing()
	{
		parent::PrepareOptions();
		
		$this->_sXMLResponse = parent::Execute();
		
		if (isset($_GET["TEST"]))
                    echo $this->_sXMLResponse;

		$this->_IdentifyUsers();
		
		if (count($this->_aUsersId))
		{
			$this->_bIsAutorisedToPublish = true;
		}
	}
	
	private function _IdentifyUsers()
	{
		$oSXMLE = new SimpleXMLElement($this->_sXMLResponse);
		
		$this->_aUsersId = $oSXMLE->xpath('/room/users/user/id');
		
		$this->_aUsers = $oSXMLE->xpath('/room/users/user');
	}
	
	public function TellAutorisation()
	{
		return $this->_bIsAutorisedToPublish;
	}
	
	public function TellNumberOfUsersInRoom()
	{
		return count($this->_aUsersId);
	}
	
	public function GiveRoomUsersIdArray()
	{
		return $this->_aUsersId;
	}
	
	public function GiveRoomUsersArray()
	{
		return $this->_aUsers;
	}
}


////
//// CLASS - MESSAGE SUBMITTER
////
//
class CCimpfoMessageSubmitter extends CCampfireSpeak
{
	private $_iPublishCount;
	private $_iStartTime;

	public function __construct()
	{
		parent::__construct(I_CAMPFIRE_ROOMID);
		
		parent::PrepareOptions();
		
		$this->_iPublishCount = 0;
		
		$this->_iStartTime = time();
	}
	
	public function SubmitMessage($sMsg)
	{
		if (isset($_GET["UA"]))
		{
			echo $sMsg."<hr />";
		}
		else
		{
			$oXMLMsg = new CCampfireXMLMessage();
		
			$oXMLMsg->SetMessage($sMsg, sMsgTypeTextMsg);
		
			parent::SetPostString((string) $oXMLMsg);
			
			$this->_iPublishCount++;
			
			echo parent::Execute();
		}
	}
	
	public function AlertPublishing()
	{
		$sMsg = $this->_iPublishCount." messages published with step ".I_NEXTSECSRUN." in ".(time() - $this->_iStartTime)." seconds";
	
		mail("toutix@gmail.com", "[CIMPFO] ".date("Y-m-d H:i:s", time()), $sMsg);
	}
}


?>