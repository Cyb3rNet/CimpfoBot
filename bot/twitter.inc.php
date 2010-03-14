<?php

require_once("curl.inc.php");

require_once("twitter.confs.inc.php");

////
//// CLASS - TWITTER GET UPDATES FROM USERS
////
//
class CTwitterUserUpdatesRequester extends CCurlGetAuth
{
	public function __construct()
	{	
		parent::__construct(S_URL_TWITTER_USERUPDATES, S_LOGIN_TWITTER_USER, S_LOGIN_TWITTER_PASSWORD);
	}
}


////
//// CLASS - TWITTER USER UPDATES PARSER
////
//
class CTwitterUpdatesParser extends CTwitterUserUpdatesRequester
{
	private $_sXMLResponse;
	
	private $_aoStatuses;
	
	protected $_aoSTUs;

	public function __construct()
	{
		parent::__construct();
		
		$this->_sXMLResponse = "";
		$this->_aoSTUs = array();
		$this->_aoStatuses = null;
	}
	
	public function RequestUpdates()
	{
		parent::PrepareOptions();
		
		$this->_sXMLResponse = parent::Execute();
	}

	public function SplitUpdatesResponse()
	{
		global $oG_CCV;

		$oSXMLE = new SimpleXMLElement($this->_sXMLResponse);

		$this->_aoStatuses = $oSXMLE->xpath('/statuses/status');
		
		$oG_CCV->iTwitterNumUpdates = count($this->_aoStatuses);
	
		$this->_aoStatuses = array_reverse($this->_aoStatuses);

		foreach ($this->_aoStatuses as $oStatus)
		{
			$oSTU = new CSimpleTwitterUpdate($oStatus->id, $oStatus->text, $oStatus->created_at, $oStatus->user->screen_name);
			
			$this->_aoSTUs[] = $oSTU;
		}
	}
	
	public function __toString()
	{
		return $this->_sXMLResponse;
	}
}


////
//// CLASS - TINY PORTION OF A USER UPDATE RESPONSE UNIT
////
//
class CSimpleTwitterUpdate
{
	private $_iId;
	private $_sText;
	private $_sDate;
	private $_sUser;

	public function __construct($iId, $sText, $sDate, $sUser)
	{
		$this->_iId = strval($iId);
		$this->_sText = $sText;
		$this->_sDate = $this->_FormatDate($sDate);
		$this->_sUser = $sUser;
	}
	
	private function _FormatDate($sDate)
	{
		$aDate = date_parse($sDate);
	
		$sDate = $aDate['hour'].":".$aDate['minute'].":".$aDate['second']." ".$aDate['day']."/".$aDate['month']."/".$aDate['year'];
	
		return $sDate; 
	}
	
	public function GetId()
	{
		return $this->_iId;
	}
	
	public function GetText()
	{
		return $this->_sText;
	}
	
	public function GetDate()
	{
		return $this->_sDate;
	}
	
	public function GetUser()
	{
		return $this->_sUser;
	}
}


////
//// CLASS - TWITTER USER UPDATES PUBLISHER
////
//
class CTwitterUpdatesPublisher extends CTwitterUpdatesParser
{
	private $_sFileName;
	
	private $_iFileSize;

	private $_hFileUpdateId;
	
	private $_iLastUpdateId;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_sFileName = ROOTDIR.'/twitter.id.txt';
		
		$this->_iFileSize = filesize($this->_sFileName);
		
		$this->_hFileUpdateId = fopen($this->_sFileName, 'a+');
		
		$this->_iLastUpdateId = 0;
	}
	
	public function __destruct()
	{
		fclose($this->_hFileUpdateId);
	}
	
	public function LoadLastUpdateId()
	{
		if ($this->_iFileSize > 0)
		{
			$sValue = fread($this->_hFileUpdateId, $this->_iFileSize);
	
			$this->_iLastUpdateId = $sValue;
		}
		
		ftruncate($this->_hFileUpdateId, 0);
	}
	
	public function WriteLastUpdateId()
	{
		ftruncate($this->_hFileUpdateId, 0);
		
		fwrite($this->_hFileUpdateId, $this->_iLastUpdateId);
	}
	
	public function PublishUpdates()
	{
		global $oG_CCV;
		
		$asMsg = array();
		
		foreach ($this->_aoSTUs as $oSTU)
		{
			if (bccomp($oSTU->GetId(), $this->_iLastUpdateId) == 1)
			{
				$this->_iLastUpdateId = $oSTU->GetId();
			
				$oG_CCV->iLastUpdateId = $oSTU->GetId();
				
				$asMsg[] = S_TWITTER_NAME." - ".$oSTU->GetUser()." :: ".$oSTU->GetText();
				
				$oG_CCV->asCimpfoPublications[] = $oSTU->GetId()." &gt; ".S_TWITTER_NAME." - ".$oSTU->GetUser()." :: ".$oSTU->GetText();
			}
		}
		
		return $asMsg;
	}
}





?>