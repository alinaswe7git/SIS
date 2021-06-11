<?php
class billing {

	public $core;
	public $service;
	public $item = NULL;
	public $total;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function runService($core,$item) {
		$this->core = $core;
		
		$total = $this->core->cleanGet['total'];
		$userid = $this->core->userID;
		$dates = $this->getDates();

		$sql = "SELECT IF(StudyType = 2,'DP',IF(StudyType = 3,'BA',IF(StudyType = 4,'PD','MS'))) as levelDefinition,
		 `Code` as code, (SELECT IF( StudyType ='Fulltime',1,2) FROM `basic-information` WHERE ID=a.StudentID) as modeOfStudy,
		(SELECT IF( FirstName=MiddleName,CONCAT(FirstName,' ',Surname),CONCAT(FirstName,' ',MiddleName,' ',Surname)) 
		FROM `basic-information` WHERE ID=a.StudentID) as name,
		 (SELECT MAX(Year) FROM `course-year-link` WHERE CourseID in (
				SELECT CourseID FROM `course-electives` 
					WHERE StudentID = a.StudentID and PeriodID =(SELECT ID FROM periods WHERE Year='". $dates['academicyear'] ."' AND Name='".$dates['semester']."')) 
				AND StudyID=b.ID) as year,
		(SELECT Semester FROM periods WHERE Year='".$dates['academicyear']."' AND Name='".$dates['semester']."') as semester,
		(SELECT count(CourseID) FROM `course-electives` WHERE StudentID = a.StudentID
			    AND PeriodID =(SELECT ID FROM periods WHERE Year='".$dates['academicyear']."' AND Name='".$dates['semester']."')) as numCourses,
		(SELECT Name FROM schools WHERE ID=b.ParentID) as school,
		a.StudentID as studentID,
		b.ParentID as schoolCode
		FROM `student-study-link` a, study b
		WHERE 	a.StudyID=b.ID 
		AND a.StudentID = '$userid'";
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$leveldefinition = urlencode($fetch['levelDefinition']);
			$code = urlencode($fetch['code']);
			$mode = urlencode($fetch['modeOfStudy']);
			$name = urlencode($fetch['name']);
			$year = urlencode($fetch['year']);
			$semester = urlencode($fetch['semester']);
			$courses = urlencode($fetch['numCourses']);
			$school = urlencode($fetch['school']);
			$schoolcode = urlencode($fetch['schoolCode']);


			if($total != ''){
				$courses = $total;
			}			
			
			if($item == "confirm"){
				$userid = $this->core->subItem();
				$url = "http://41.63.17.247:8080/AccPackService/studentbillsage?studentID=$userid&levelDefinition=$leveldefinition&Code=$code&modeOfStudy=$mode&name=$name&year=$year&semester=$semester&numCourses=$courses&school=$school&schoolCode=$schoolcode";
			}else{
				$url = "http://41.63.17.247:8080/AccPackService/studentbillget?studentID=$userid&levelDefinition=$leveldefinition&Code=$code&modeOfStudy=$mode&name=$name&year=$year&semester=$semester&numCourses=$courses&school=$school&schoolCode=$schoolcode";
			}
echo $url;
			$output = file_get_contents($url);
		}


		echo $output;
		
		die();

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

			$bill[$i]["amount"] = $amount;
			$bill[$i]["description"] = $description;
			$bill[$i]["iid"] = $iid;
			$bill[$i]["date"] = $date;

			if($iid == ""){
				$run = FALSE;
				unset($bill[$i]);
			}

			$i++;
		}

		
		echo json_encode($bill);
		
	}
	
	public function confirmBilling($core,$item) {
		echo 'confirm sage';
		$this->runService($core,$item);
	}

	private function getDates(){
		$d1=new DateTime("NOW");
		$data_now= (int)$d1->format("Y");
		$date_year = (int)$d1->format("Y");
		$date_month = (int)$d1->format("m");
	
		$p_year=$date_year+1;
		$m_year=$date_year-1;
		$_academicyear1=""; 
		$_semester1=""; 
		if($date_month >=7){
			$dates['academicyear'] = $date_year."/".$p_year; 
			$dates['semester'] = "Semester I";
		}else if($date_month <=6){
			$dates['academicyear'] = $m_year."/".$date_year; 
			$dates['semester'] = "Semester II";
		}

		return $dates;
	}
}
?>