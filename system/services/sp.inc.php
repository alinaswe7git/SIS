<?php
class sp {

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



		if($userid == '' || $key == ''){
			echo'NO KEY';
		}


		if($key == 'x39g7jdavnkeduyf76c'){
	
			
			$sql = "SELECT *, `basic-information`.Status as STAT, `basic-information`.StudyType as ST FROM `basic-information`
			LEFT JOIN `student-data-other` ON `basic-information`.ID = `student-data-other`.StudentID
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			 WHERE `basic-information`.`ID` = '$userid'";

			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$output["id"] = $row['StudentID'];
				$output["name"] = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['Surname'];	
			}


			echo json_encode($output);

		}
		
	}
}
?>