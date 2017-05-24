<?php

/**
 * Exchangeclient class.
 *
 * @author Riley Dutton
 * @author Rudolf Leermakers
 */
class ExchangeClient {  

	private $wsdl;
	public $client;
	private $user;
	private $pass;
	/**
	 * The last error that occurred when communicating with the Exchange server.
	 * 
	 * @var mixed
	 * @access public
	 */
	public $lastError;
	private $impersonate;
	private $delegate;

	/**
	 * Initialize the class. This could be better as a __construct, but for CodeIgniter compatibility we keep it separate.
	 * 
	 * @access public
	 * @param string $user (the username of the mailbox account you want to use on the Exchange server)
	 * @param string $pass (the password of the account)
	 * @param string $delegate. (the email address you would like to access...the account you are logging in as must be an administrator account.
	 * @param string $wsdl. (The path to the WSDL file. If you put them in the same directory as the Exchangeclient.php script, you can leave this alone. default: "Services.wsdl")
	 * @return void
	 */
	public function init($user, $pass, $delegate = NULL, $wsdl = "Services.wsdl") {
		$this->wsdl = $wsdl;
		$this->user = $user;
		$this->pass = $pass;
		$this->delegate = $delegate;

		$this->setup();

		$this->client = new NTLMSoapClient($this->wsdl, array(
			'trace' => 1,
			'exceptions' => true,
			'login' => $user,
			'password' => $pass
		));

 		$this->teardown();
	}
   
	public function getClient()
	{
		$this->setup();

		$FindItem = new stdClass();		
		$FindItem->UserConfigurationName = new stdClass();
		$FindItem->UserConfigurationName->Name = 'TestConfig';
		$FindItem->UserConfigurationName->DistinguishedFolderId = new stdClass();
		$FindItem->UserConfigurationName->DistinguishedFolderId->Id = 'drafts';

		$FindItem->UserConfigurationProperties = NULL;

		
		$response = $this->client->GetUserConfiguration($FindItem);

		return $response;
	}
	
	/**
	 * Get the messages for a mailbox.
	 * 
	 * @access public
	 * @param int $limit. (How many messages to get? default: 50)
	 * @param bool $onlyunread. (Only get unread messages? default: false)
	 * @param string $folder. (default: "inbox", other options include "sentitems")
	 * @param bool $folderIdIsDistinguishedFolderId. (default: true, is $folder a DistinguishedFolderId or a simple FolderId)
	 * @return array $messages (an array of objects representing the messages)
	 */
	
	public function get_messagesCorp($limit = 50, $onlyunread = false, $folder = "drafts", $folderIdIsDistinguishedFolderId = true) {
		$this->setup();

		$FindItem = new stdClass();
		$FindItem->Traversal = "Shallow";

		$FindItem->ItemShape = new stdClass();
		$FindItem->ItemShape->BaseShape = "IdOnly";

		$FindItem->ParentFolderIds = new stdClass();
		
		if ($folderIdIsDistinguishedFolderId) {
			$FindItem->ParentFolderIds->DistinguishedFolderId = new stdClass();
			$FindItem->ParentFolderIds->DistinguishedFolderId->Id = $folder;
			//
		} else {
			$FindItem->ParentFolderIds->FolderId = new stdClass();
			$FindItem->ParentFolderIds->FolderId->Id = $folder;
    	}

		if($this->delegate != NULL) {
			$FindItem->ParentFolderIds->DistinguishedFolderId->Mailbox->EmailAddress = $this->delegate;
		}
		
		$response = $this->client->FindItem($FindItem);

		return $response;
		
		if($response->ResponseMessages->FindItemResponseMessage->ResponseCode != "NoError") {
			$this->lastError = $response->ResponseMessages->FindItemResponseMessage->ResponseCode;
			return false;
		}
		
		$items = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Message;
		
		$i = 0;
		$messages = array();
		
		if(count($items) == 0)
			return false; //we didn't get anything back!
		
		if(!is_array($items)) //if we only returned one message, then it doesn't send it as an array, just as a single object. so put it into an array so that everything works as expected.
			$items = array($items);
		
		foreach($items as $item) {
			$GetItem = new stdClass();
			$GetItem->ItemShape = new stdClass();

			$GetItem->ItemShape->BaseShape = "Default";
			$GetItem->ItemShape->IncludeMimeContent = "true";

			$GetItem->ItemIds = new stdClass();
			$GetItem->ItemIds->ItemId = $item->ItemId;

			$response = $this->client->GetItem($GetItem);
			
			if($response->ResponseMessages->GetItemResponseMessage->ResponseCode != "NoError") {
				$this->lastError = $response->ResponseMessages->GetItemResponseMessage->ResponseCode;
				return false;
			}
			
			$messageobj = $response->ResponseMessages->GetItemResponseMessage->Items->Message;

			if($onlyunread && $messageobj->IsRead)
				continue;

			$newmessage = new stdClass();
			
			$newmessage->from = $messageobj->From->Mailbox->EmailAddress;
			$newmessage->from_name = $messageobj->From->Mailbox->Name;
			
			$messages[] = $newmessage;
			
			if(++$i > $limit) {
				break;
      		}
		}
		
		$this->teardown();
		
		return $messages;
	}

	public function get_messages($limit = 50, $onlyunread = false, $folder = "sentitems", $folderIdIsDistinguishedFolderId = true) {
		$this->setup();
		
		$FindItem = new stdClass();
		$FindItem->Traversal = "Shallow";

		$FindItem->ItemShape = new stdClass();
		$FindItem->ItemShape->BaseShape = "IdOnly";

		$FindItem->ParentFolderIds = new stdClass();
		
		if ($folderIdIsDistinguishedFolderId) {
			$FindItem->ParentFolderIds->DistinguishedFolderId = new stdClass();
			$FindItem->ParentFolderIds->DistinguishedFolderId->Id = $folder;
		} else {
			$FindItem->ParentFolderIds->FolderId = new stdClass();
			$FindItem->ParentFolderIds->FolderId->Id = $folder;
    	}

		if($this->delegate != NULL) {
			$FindItem->ParentFolderIds->DistinguishedFolderId->Mailbox->EmailAddress = $this->delegate;
		}
		
		$response = $this->client->FindItem($FindItem);
		
		if($response->ResponseMessages->FindItemResponseMessage->ResponseCode != "NoError") {
			$this->lastError = $response->ResponseMessages->FindItemResponseMessage->ResponseCode;
			return false;
		}
		
		$items = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Message;
		
		$i = 0;
		$messages = array();
		
		if(count($items) == 0)
			return false; //we didn't get anything back!
		
		if(!is_array($items)) //if we only returned one message, then it doesn't send it as an array, just as a single object. so put it into an array so that everything works as expected.
			$items = array($items);
		
		foreach($items as $item) {
			$GetItem = new stdClass();
			$GetItem->ItemShape = new stdClass();

			$GetItem->ItemShape->BaseShape = "Default";
			$GetItem->ItemShape->IncludeMimeContent = "true";

			$GetItem->ItemIds = new stdClass();
			$GetItem->ItemIds->ItemId = $item->ItemId;

			$response = $this->client->GetItem($GetItem);
			
			if($response->ResponseMessages->GetItemResponseMessage->ResponseCode != "NoError") {
				$this->lastError = $response->ResponseMessages->GetItemResponseMessage->ResponseCode;
				return false;
			}
			
			$messageobj = $response->ResponseMessages->GetItemResponseMessage->Items->Message;

			if($onlyunread && $messageobj->IsRead)
				continue;

			$newmessage = new stdClass();
			/*$newmessage->source = base64_decode($messageobj->MimeContent->_);
			$newmessage->bodytext = $messageobj->Body->_;
			$newmessage->bodytype = $messageobj->Body->BodyType;
			$newmessage->isread = $messageobj->IsRead;
			$newmessage->ItemId = $item->ItemId;*/
			$newmessage->from = $messageobj->From->Mailbox->EmailAddress;
			$newmessage->from_name = $messageobj->From->Mailbox->Name;
			
			/*$newmessage->to_recipients = array();

			if(!is_array($messageobj->ToRecipients->Mailbox)) {
				$messageobj->ToRecipients->Mailbox = array($messageobj->ToRecipients->Mailbox);
      		}

			foreach($messageobj->ToRecipients->Mailbox as $mailbox) {
				$newmessage->to_recipients[] = $mailbox;
			}
			
			$newmessage->cc_recipients = array();

			if(isset($messageobj->CcRecipients->Mailbox)) {
				if(!is_array($messageobj->CcRecipients->Mailbox)) {
					$messageobj->CcRecipients->Mailbox = array($messageobj->CcRecipients->Mailbox);
        		}

				foreach($messageobj->CcRecipients->Mailbox as $mailbox) {
					$newmessage->cc_recipients[] = $mailbox;
				}
			}
			
			$newmessage->time_sent =  $messageobj->DateTimeSent;
			$newmessage->time_created = $messageobj->DateTimeCreated;
			$newmessage->subject = $messageobj->Subject;
			$newmessage->attachments = array();*/

			$messages[] = $newmessage;
			
			if(++$i > $limit) {
				break;
      		}
		}
		
		$this->teardown();
		
		return $messages;
	}
	
	/**
	 * Sets up strream handling. Internally used.
	 * 
	 * @access private
	 * @return void
	 */
	private function setup() {
		if($this->impersonate != NULL) {
			$impheader = new ImpersonationHeader($this->impersonate);
			$header = new SoapHeader("http://schemas.microsoft.com/exchange/services/2006/messages", "ExchangeImpersonation", $impheader, false);
			$this->client->__setSoapHeaders($header);
		}

		ExchangeNTLMStream::setCredentials($this->user, $this->pass);
			
		stream_wrapper_unregister('http');
		stream_wrapper_unregister('https');

		if(!stream_wrapper_register('http', 'ExchangeNTLMStream')) {
			throw new Exception("Failed to register protocol");
		}

		if(!stream_wrapper_register('https', 'ExchangeNTLMStream')) {
			throw new Exception("Failed to register protocol");
		}
	}
	
	/**
	 * Tears down stream handling. Internally used.
	 * 
	 * @access private
	 * @return void
	 */
	private function teardown() {
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
	}
}

class ImpersonationHeader {
	public $ConnectingSID;

	function __construct($email) {
		$this->ConnectingSID->PrimarySmtpAddress = $email;
	}
}
