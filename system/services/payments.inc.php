<?php
class payments {

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

		$sql = "SELECT * FROM `authentication` WHERE `Key` = '$key'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$userid = $row['StudentID'];
		}

		if($userid == ''){
			echo'NO KEY';
		}

		// BALANCE
		require_once $this->core->conf['conf']['viewPath'] . "information.view.php";
		$payments = new information();
		$payments->buildView($this->core);
		$actual = $payments->getBalance($userid);
	


		$output = file_get_contents("http://41.63.16.6:8081/AccPackService/trans/$userid");

		$p = xml_parser_create();
		xml_parse_into_struct($p, $output, $vals, $index);
		xml_parser_free($p);

		$run = TRUE;

		$i = 0;
		
		while($run == TRUE){

			$amount = $vals[$index["AMOUNT"][$i]]["value"];
			$description = $vals[$index["ITEMDESCRIPTION"][$i]]["value"];
			$iid = $vals[$index["ITEMID"][$i]]["value"];
			$date = $vals[$index["TRANSDATE"][$i]]["value"];

			$payment[$i]["amount"] = $amount;
			$payment[$i]["description"] = $description;
			$payment[$i]["iid"] = $iid;
			$payment[$i]["date"] = $date;

			if($iid == ""){
				$run = FALSE;
				unset($payment[$i]);
			}

			$i++;
		}

		$payment[$i]["amount"] = $actual;
		$payment[$i]["description"] = "CURRENT BALANCE";
		$payment[$i]["iid"] = 1000;
		$payment[$i]["date"] = date("Y-m-d");


		//var_dump($payment);
		echo json_encode($payment);
		
	}
}
?>