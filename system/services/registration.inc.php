<?php
class registration {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function getPeriod(){
		$d1=new DateTime("NOW");
		$data_now= (int)$d1->format("Y");
		$date_year = (int)$d1->format("Y");
		$date_month = (int)$d1->format("m");
	
		$p_year=$date_year+1;
		$m_year=$date_year-1;
		$_academicyear1=""; 
		$_semester1=""; 
		if($date_month >=7){
			$year =$date_year."/".$p_year; 
			$semester ="Semester I";
		}else if($date_month <=6){
			$year = $m_year."/".$date_year; 
			$semester = "Semester II";
		}

		$sql = "SELECT * FROM `periods` WHERE `Year` = '$year' AND `Name` = '$semester'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
		}
		return $item;
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



		if($courses != 'all'){
			
			// PAYMENT VERIFICATION FOR REGISTRATION
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($userid);
			
			$sql_payroll_exempt= "SELECT Count(*) AS CT FROM ac_registration_exemption WHERE student_id = '$userid'";
				
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			while ($row_exempt = $run_payroll_exempt->fetch_assoc()) {
				$ct = $row_exempt['CT'];
			}
			
			if( $ct <= 0 ){
			
				if($actual > 100){
					$message['error'] = 'YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER.'; 
					echo json_encode($message);
					return;
				}
				
			} else {
				
				$message['error'] = 'Payment exempted';
				echo json_encode($message);
				
			}
			
			$sql = "SELECT COUNT(`ID`) as CT FROM `course-electives` WHERE `StudentID` = '$userid' AND `PeriodID` = '$period'";
			$run = $this->core->database->doSelectQuery($sql);
		
			while ($row = $run->fetch_assoc()) {
				$count = $row['CT'];
			}

			if($count>0){
				$message['error'] = 'YOU HAVE ALREADY REGISTERED';
				echo json_encode($message);
				return;
			}

			$courses = trim(urldecode($courses));
			$courses = str_replace("&#34;",'"',$courses); 
			$vars = json_decode($courses);

			$period = $this->getPeriod();

			foreach($vars as $course){
				foreach($course as $cc){
					$code =  $cc->coursecode;
					$name =  $cc->coursename;

					$sql = "SELECT * FROM `courses` WHERE `courses`.Name = '$code'";
					$run = $this->core->database->doSelectQuery($sql);
	
					while ($row = $run->fetch_assoc()) {
						$courseid = $row['ID'];
					}

					
					$sql = "INSERT INTO `course-electives` (`ID`, `StudentID`, `CourseID`, `EnrolmentDate`, `Approved`, `PeriodID`) 
						VALUES (NULL, '$userid', '$courseid', NOW(), 0, '$period');";

					$run = $this->core->database->doInsertQuery($sql);

					$set = TRUE;

					
				}
		
				if($set == TRUE){
					$message['error'] = 'COURSES REGISTERED SUCCESFULLY, PLEASE AWAIT APPROVAL';
					echo json_encode($message);
				} else {
					$message['error'] = 'COURSES NOT REGISTERED';
					echo json_encode($message);
				}
				
			}

			return;
		}


		$sql = "SELECT * FROM `student-study-link` as sp, `study` as p
			WHERE sp.`StudentID` = '$userid' 
			AND sp.`StudyID` = p.`ID` 
			ORDER BY sp.`ID` DESC LIMIT 1";


		$run = $this->core->database->doSelectQuery($sql);
	
		while ($row = $run->fetch_assoc()) {
			$name = $row['Name'];
			$studyid =  $row['StudyID'];
			$studentid = $row['StudentID'];
		}

		if($studyid == ''){
			echo '["ERROR":"Please see ICT as your program is not attached"]';
			die();
		}
		$i=0;
			
		$sql = "SELECT DISTINCT `courses`.ID, `courses`.Name, `courses`.CourseDescription, `courses`.CourseCredit FROM `courses`, `course-year-link` 
			WHERE `course-year-link`.CourseID = `courses`.ID 
			AND `course-year-link`.StudyID = '$studyid' 
			AND `courses`.Name NOT IN (SELECT `CourseNo` FROM grades 
						WHERE grades.Grade IN ('A', 'B', 'C', 'A+', 'B+', 'C+',  'P',  'CP','S') 
						AND grades.StudentNo = '$userid')
			ORDER BY SUBSTRING(courses.Name,4,1),SUBSTRING(courses.Name,6,1),`courses`.Name ASC ";

		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$i++;
			$output["courses"][$i]["id"] = $row['ID'];
			$output["courses"][$i]["coursecode"] = $row['Name'];
			$output["courses"][$i]["coursename"] = $row['CourseDescription'];
			$output["courses"][$i]["coursecredits"] = $row['CourseCredit'];
		}

		$output["study"] = $studyid;
		$output["studyname"] = $name;
		$output["studentid"] = $studentid;


		echo json_encode($output);
		
	}


	private function checkRegistered($item){
		$userid = $item;

		$sql = "SELECT * FROM `course-electives` 
			WHERE StudentID = '$userid' 
			AND `PeriodID` IN (SELECT `periods`.ID
			FROM `periods`, `basic-information` 
			WHERE `basic-information`.ID = '$userid' 
			AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
			AND `Delivery` = `StudyType`)";


		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0){
			echo '["ERROR":"You have already submitted your course registration"]';
			die();
		}

	}
}
?>