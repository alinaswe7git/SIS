<?php
class balance {

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

		$output = file_get_contents("http://41.63.17.247:8080/AccPackService/balance/$userid");


		$p = xml_parser_create();
		xml_parse_into_struct($p, $output, $vals, $index);

		xml_parser_free($p);


		$balance = $vals[1]["value"];

		echo json_encode($balance);
		
	}
}
?>