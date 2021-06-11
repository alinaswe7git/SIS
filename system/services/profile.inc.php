<?php
class profile {

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
	
			
		$sql = "SELECT *, `basic-information`.Status as STAT, `basic-information`.StudyType as ST FROM `basic-information`
			LEFT JOIN `student-data-other` ON `basic-information`.ID = `student-data-other`.StudentID
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			 WHERE `basic-information`.`ID` = '$userid'";

		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$output["id"] = $row['StudentID'];
			$output["name"] = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['Surname'];
			$output["delivery"] = $row['StudyType'];
			$output["status"] = $row['STAT'];
			$output["phone"] = $row['MobilePhone'];
			$output["email"] = $row['PrivateEmail'];
			$output["sex"] = $row['Sex'];
			$output["nrc"] = $row['GovernmentID'];
			$output["street"] = $row['StreetName'];
			$output["postal"] = $row['PostalCode'];
			$output["town"] = $row['Town'];
			$output["country"] = $row['Country'];
			$output["nationality"] = $row['Nationality'];
			$output["dob"] = $row['DateOfBirth'];
			$output["center"] = $row['ExamCentre'];
			$output["study"] = $row['Name'];
		}


		echo json_encode($output);
		
	}
}
?>