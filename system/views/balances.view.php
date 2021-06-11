<?php
class balances {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	function runBalances($item) {

		$sqls = "SELECT `ID` FROM `basic-information` WHERE `Status` = 'Approved' OR  `Status` = 'Requesting'";
		$runs = $this->core->database->doSelectQuery($sqls);

		while ($fetchs = $runs->fetch_assoc()) {
			$uid = $fetchs["ID"];

			$amount = $this->getBalance($uid);

			if(isset($amount)){

				$sql  = "INSERT INTO `balances-report` (`StudentID`, `Balance`, `Date`) VALUES ('$uid', '$amount', NOW());";		 
				$run = $this->core->database->doInsertQuery($sql);
			
				echo "CALCULATED AND SAVED - $uid - $amount<br>";
			} else{
				echo "FAILED TO CALCULATE - $uid - $amount<br>";
			}
		}
	}


	public function getBalance($item){

			$output = file_get_contents("http://41.63.17.247:8080/AccPackService/balance/$item");

			$p = xml_parser_create();
			xml_parse_into_struct($p, $output, $vals, $index);

			xml_parser_free($p);


			$balance = $vals[1]["value"];

			$sql = "INSERT INTO `balances` (`StudentID`, `Amount`, `LastUpdate`, `LastTransaction`, `Original`) VALUES ($item, $balance, NOW(), '', 0) ON DUPLICATE KEY UPDATE `Amount`=$balance;";
			$run = $this->core->database->doInsertQuery($sql);

			return $balance;
	} 




	function tabletsBalances($item) {

		$sqls = "SELECT `ID` FROM `basic-information` WHERE `Status` = 'Approved' OR  `Status` = 'Requesting'";
		$runs = $this->core->database->doSelectQuery($sqls);

		$i=0;

		while ($fetchs = $runs->fetch_assoc()) {
			$uid = $fetchs["ID"];
			$amount = $this->filterBalances($uid);
		}
	}



	public function filterBalances($item){

		ob_implicit_flush(true);
		ob_start();

		$output = file_get_contents("http://41.63.17.247:8080/AccPackService/trans/$item");

		$p = xml_parser_create();
		xml_parse_into_struct($p, $output, $vals, $index);
		xml_parser_free($p);


		$run = TRUE;

		$i=0;
		$count=0;
		while($run == TRUE){

			

			$amount = $vals[$index["AMOUNT"][$i]]["value"];
			$description = $vals[$index["ITEMDESCRIPTION"][$i]]["value"];
			$iid = $vals[$index["ITEMID"][$i]]["value"];
			$date = $vals[$index["TRANSDATE"][$i]]["value"];


			$date = date("Ymd", strtotime($date));

			$payment[$i]["amount"] = $amount;
			$payment[$i]["description"] = $description;
			$payment[$i]["iid"] = $iid;
			$payment[$i]["date"] = $date;


			if($date > 20170601){

				if($amount == "2000"){
					echo " $item - TABLET $amount - $date - $description<br>";
					$tablet = TRUE;
					ob_flush();
					flush();
				}
	
				if($amount == "-2000" && $tablet == TRUE){
					echo " $item - REVERSAL $amount - $date - $description<br>";
					$tablet = FALSE;
				}
			
			}

			ob_flush();

			if($iid == ""){
				$run = FALSE;
				unset($payment[$i]);
			}

			$i++;

		}

		ob_flush();
		ob_end_flush(); 

		if($tablet==TRUE){
			return TRUE;
		}else{
			return FALSE;
		}


	}


}
?>