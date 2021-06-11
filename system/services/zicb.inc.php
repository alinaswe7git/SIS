<?php
class zicb {

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
		
		if($key != 'cy024oncv92y9sojcv'){
			echo'ACCESS DENIED';
			die();
		}

		if($ip == '159.8.22.213' || $ip == '41.63.16.142'){

		} else {
			echo'ACCESS DENIED';
			die();
		}
			

		if($userid == ''){
			echo'NO KEY';
		}
	
			
		$sql = "SELECT *, `basic-information`.Status as STAT, `basic-information`.StudyType as ST FROM `basic-information`
			LEFT JOIN `student-data-other` ON `basic-information`.ID = `student-data-other`.StudentID
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			 WHERE `basic-information`.`ID` = '$userid'";

		//include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		//$payments = new payments();
		//$payments->buildView($this->core);
		///$balance = $payments->getBalance($userid);
		//$balance = round($balance);
	

		$output["status"] = 0;

		$date = date("d-m-Y");

		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$output["student_id"] = $row['StudentID'];
			$output["status"] = 1;
			$output["student_name"] = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['Surname'];
			
			//$output["items_to_pay"]["amount_to_pay"] = $balance;
			//$output["items_to_pay"]["item_name"] = "Balance as of $date";
			//$output["items_to_pay"]["item_id"] = 0;
			//$output["amount_must_pay"] = $balance;

		}


		echo json_encode($output);
		
	}
}
?>