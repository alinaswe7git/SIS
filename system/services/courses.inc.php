<?php
class courses {

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
		$courses = $this->core->cleanGet['courses'];

		$sql = "SELECT * FROM `authentication` WHERE `Key` = '$key'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$userid = $row['StudentID'];
		}

		if($userid == ''){
			echo'NO KEY';
		}
	
			
		$period = $this->core->getPeriod();

		if($courses == 'current'){
			$sql = "SELECT *, `course-electives`.ID AS CID
			FROM `course-electives`
			RIGHT JOIN `courses` ON `course-electives`.CourseID = `courses`.ID
			WHERE `course-electives`.`StudentID` = '$userid' 
			AND `PeriodID` = '$period'";

			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$i++;
				
				$output[$i]["id"] = $row['CID'];
				$output[$i]["code"] = $row['Name'];
				$output[$i]["name"] = $row['CourseDescription'];
				$output[$i]["status"] = $row['Approved'];
			}
		}else {
			$sql = "SELECT *, `courses`.Name AS CN, `course-electives`.ID AS CID
			FROM `course-electives`
			RIGHT JOIN `courses` ON `course-electives`.CourseID = `courses`.ID
			RIGHT JOIN `periods` ON `periods`.ID = `course-electives`.PeriodID
			WHERE `course-electives`.`StudentID` = '$userid' 
			ORDER BY `periods`.Year, `periods`.Semester DESC";

			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$i++;
				$year = $row['Year'];
				$output[$year][$i]["id"] = $row['CID'];
				$output[$year][$i]["code"] = $row['CN'];
				$output[$year][$i]["name"] = $row['CourseDescription'];
				$output[$year][$i]["status"] = $row['Approved'];
				$output[$year][$i]["semester"] =  $row['Semester'];
			}
		}


		echo json_encode($output);
		
	}
}
?>