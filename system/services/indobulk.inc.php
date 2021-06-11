<?php
class indobulk {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function runService($core) {
		$this->core = $core;
		
		$key = $this->core->cleanGet['key'];
		$userid = $this->core->cleanGet['uid'];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if($key != 'e0bc8ea4bc77cfd3f675f196f92c29612'){
			echo'ACCESS DENIED';
			die();
		}

		if($ip == '41.77.145.210' || $ip == '41.215.180.245'){
		
		}else{
		
			echo'ACCESS DENIED';
			die();
		}
			

		if($userid == ''){
			echo'NO KEY';
		}
	
			
		$sql = "SELECT * FROM `basic-information`";

		//include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		//$payments = new payments();
		//$payments->buildView($this->core);
		//$balance = $payments->getBalance($userid);
		//$balance = round($balance);
	

		//$output["status"] = 0;

		$date = date("d-m-Y");

		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$i++;
			$output[$i]["student_id"] = $row['ID'];
			$output[$i]["status"] = 1;
			$output[$i]["student_name"] = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['Surname'];
		}


		echo json_encode($output);
		
	}
}
?>