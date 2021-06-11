<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';

class mailer{

	public $core;

	public function __construct($core) {

		$this->core = $core;
	}
	
	function mailCount() {

		imap_timeout(IMAP_READTIMEOUT, 5);
		imap_timeout(IMAP_OPENTIMEOUT, 5);
		$mbox = @imap_open("{" . $this->core->conf['mail']['server'] . ":143/novalidate-cert}", $_SESSION['username'], $_SESSION['password'], OP_HALFOPEN);

		if (!$mbox) {
			return false;
		}

		$status = @imap_status($mbox, "{" . $this->core->conf['mail']['server'] . "}INBOX", SA_ALL);

		if ($status) {
			$out = $status->unseen;
		}

		@imap_close($mbox);

		if(!isset($out)) {
			$out = "0";
		}

		return ($out);
	}

	public function sendMail($destination, $name, $subject, $content, $attachment){

		$mail = new PHPMailer(true);
		try {
			//$mail->SMTPDebug = 2;  	
			$mail->isSMTP();
			$mail->Host = 'mail.mu.ac.zm';
			$mail->SMTPAuth = true;
			$mail->Username = 'utility@mu.ac.zm';
			$mail->Password = 'support@mu2018';
			$mail->Port = 25;
	
	    		$mail->setFrom('utility@mu.ac.zm', 'Mulungushi ICT Department');
			$mail->addReplyTo('utility@mu.ac.zm', 'Mulungushi ICT Department');
			$mail->addAddress($destination, $name);
	
	
			if($attachment!=''){
				$mail->addAttachment($attachment);
			}
	
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $content;
			$mail->AltBody = strip_tags($content);
			$mail->send();
	
			return TRUE;
		} catch (Exception $e) {
			return FALSE;
		}
	}	
}
?>