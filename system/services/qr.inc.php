<?php
class qr {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function runService($core) {
		$this->core = $core;
		
		require_once $this->core->conf['conf']['libPath'] . 'phpqrcode/qrlib.php';
		
		
		$userid = $this->core->cleanGet['uid'];
		$pid = $this->core->cleanGet['pid'];
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$admin = $this->core->userID;
		
		include_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$this->payments = new payments();
		$this->payments->buildView($this->core);
		$balance = $this->payments->getBalance($userid);

		$sqlReg ="SELECT CONCAT(b.FirstName,' ',b.Surname) `Name`,b.ID,IF(c.Approved=1,'yes','no') as status,
				(SELECT `Name` FROM courses WHERE ID = c.CourseID) as Courses 
				FROM `basic-information` as b 
				LEFT JOIN `course-electives` c ON b.ID=c.StudentID 
				WHERE b.ID = $userid 
				AND c.PeriodID=$pid";	

		$runx = $this->core->database->doSelectQuery($sqlReg);
		
		while ($fetchx = $runx->fetch_assoc()){	
			$name = $fetchx["Name"];
			$studentID = $fetchx["ID"];
			$courses .= $fetchx["Courses"].', ';
			$status = $fetchx["status"];
		}
		
		$output["ID"] = $userid;
		$output["N"] = $name;
		$output["C"] = $courses;
		$output["B"] = $balance;

		//$output["domain"] = $this->core->conf['conf']['domain'];
		//$content = "https://edurole.mu.ac.zm/secure/" . $file;

		$content = json_encode($output);
		$content = $this->encrypt($content);
		$sql = "INSERT INTO `security` (`ID`, `Data`, `File`, `Date`, `StudentID`, `Creator`) VALUES (NULL, '$ip', '$content', NOW(), '$userid', '$admin')";
		$this->core->database->doInsertQuery($sql);
		
		$item = md5($sid);
		
		QRcode::png($content);
		//echo $content;
				
	}
	public function encrypt($plaintext){
		$password = 'NipaEXAM';
		$method = 'aes-256-cbc';
	
		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		//$iv = "4e5Wa71fYoT7MFEX";
		$encrypted = base64_encode(openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv));
		return $encrypted;	
	}	



	public function decrypt($plaintext){
		$password = 'NipaEXAM';
		$method = 'aes-256-cbc';
	
		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		//$iv = "4e5Wa71fYoT7MFEX";
		$decrypted = openssl_decrypt(base64_decode($encrypted), $method, $password, OPENSSL_RAW_DATA, $iv);
		return $decrypted;	
	}
}
?>