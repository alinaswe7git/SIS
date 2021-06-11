<?php
class exams {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

	public function decrypt($encrypted){
		$password = 'EduRoleMU2018EXAM';
		$method = 'aes-256-cbc';
	
		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		$decrypted = openssl_decrypt(base64_decode($encrypted), $method, $password, OPENSSL_RAW_DATA, $iv);
		return $decrypted;	
	}


	public function runService($core) {
		$this->core = $core;
		$username = $_GET['username'];
		$data = $this->decrypt($_GET['data']);
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "INSERT INTO `exam-log` (`ID`, `DateTime`, `Data`, `User`, `IP`) VALUES (NULL, NOW(), '$data', '$username', '$ip');";
		$this->core->database->doInsertQuery($sql);
			

		$input = json_decode($data, true);
		$studentID = $input["ID"];

		// PAYMENT VERIFICATION FOR EXAM SLIP
		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$balance = $payments->getBalance($studentID);
	
		$sql = "SELECT * FROM `ac_payroll` WHERE `student_id` = '$studentID'";

		$run = $this->core->database->doSelectQuery($sql);
		if($run->num_rows > 0){
			$balance = 'Student is on Payroll';
		}

		$output['B'] = $balance;
		echo json_encode($output); 
	}
}
?>