<?php
class results {

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

		$sql = "SELECT * FROM `authentication`, access
			WHERE `Key` = '$key'
			AND `access`.ID = `authentication`.StudentID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			$userid = $row['Username'];
		}

		if($userid == ''){
			echo'NO KEY';
		} else {
			$this->resultsStatement($userid);
		}
	}
	
	function resultsStatement($studentID) {

		$studentNo = $studentID;
		$start = substr($studentID, 0, 4);

		$sql = "SELECT `Firstname`, `MiddleName`, `Surname`, `Sex`, `Status`
			FROM `basic-information`
			WHERE `basic-information`.ID = '$studentID' 
			LIMIT 1";

		$run = $this->core->database->doSelectQuery($sql);

		$started = FALSE;


		while ($fetch = $run->fetch_array()){

			$started = TRUE;

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$program=$fetch[5];
			$sex=$fetch[4]; 
			$studentname = $firstname . " " . $middlename . " " . $surname;


			// PAYMENT VERIFICATION FOR GRADES
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($studentID);
	

			if($actual > 100){
				$list = 'STUDENT IS OWING';
				if($this->core->role != 1000){
					$message['error'] = 'YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT VIEW RESULTS.'; 
					echo json_encode($message);

					return;
				}
			}


			$list = $this->detail($studentNo);
			echo json_encode($list);
		}
	}

	private function detail($studentNo) {

		$remarked = FALSE;
		$sql = "SELECT 
				`grades`.CourseNo,
				`grades`.Grade,
				`courses`.CourseDescription,
				`grades`.ID,
				`grades`.CAMarks,
				`grades`.ExamMarks,
				`grades`.AcademicYear,
				`grades`.Semester
			FROM 	`grades`, `courses`
			WHERE 	`grades`.StudentNo = '$studentNo'
			AND	`grades`.CourseNo = `courses`.Name  
			ORDER by `grades`.`academicyear` ASC, `grades`.`semester`";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$suppcount = 0;

		$i=0;
		$repeatlist = array();

		while ($row = $run->fetch_array()){

			$list[$row[6]][$row[7]][$row[0]]['GRADE'] = $row[1];
			$list[$row[6]][$row[7]][$row[0]]['CA'] = $row[4];
			$list[$row[6]][$row[7]][$row[0]]['DESCRIPTION'] = $row[2];
		
			if($row[1] == ''){
				
				$countb++;
				$row[1]  = '-';


				$percentage = 2*$ca+$exam/3;

				if($ca < 40){
					$suppoutput1 .= "FAILED $row[0]; ";
				} else {
					if($percentage < 40){
						$suppoutput1 .= "FAILED $row[0]; ";
					} else {
						$suppoutput1 .= "REFERRED IN $row[0]; ";
					}
				}

				if($countb == 3){
					$suppoutput1 .= "FAILED; ";
				}
			}


			$i++;				

	

			$count2 = $count2 + 3;

			if ($row[1] == "IN" or $row[1] == "D" or $row[1]=="F" or $row[1]=="NE") {

				$output .= "REPEAT $row[0]; ";
				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;
				}

				$courseno=$row[0];
				$countb=$countb + 1;
				$repeatlist[] =  $row[0];

				$upfail[$i] = $courseno;
			}
			

			if ($row[1]== "A+" or $row[1]=="A" or $row[1]=="B+" or $row[1]=="B" or $row[1]=="C+" or $row[1]=="C" or $row[1]=="P") {
				$k=$j-1;

				if (substr($row[0], -1) == 1){
					$count1=$count1 + 0.5;
					$count1before=$count1;

			 		if(count($upfail)>0){
						$count1 = $count1-0.5;
					}

					$checkcount=$count1before-$count1;

					if ($checkcount==1){
						$count=$count-1;
						$count1=$count1+1;
					}

					if ($checkcount==0.5){
						$count=$count-0.5;
						$count1=$count1+0.5;
					}
				} else {
					$count1=$count1 + 1;
					$count1before=$count1;
					if(count($upfail)>0){
						$count1 = $count1-0.5;
					}
					$checkcount=$count1before-$count1;

					if ($checkcount==1){
						$count=$count-1;
						$count1=$count1+1;
					}

					if ($checkcount==0.5){
						$count=$count-0.5;
						$count1=$count1+0.5;
					}
				}
			}

			if ($row[1] == "D+") {

				if($suppcount < 2){
					$suppoutput1 .= "SUPP IN $row[0]; ";
					$suppoutput2 .= "REPEAT $row[0]; ";
				}else{
					$suppoutput1 .= "REPEAT $row[0]; ";
				}

				$suppcount++;

				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;}
					$countb=$countb + 1;
					$courseno=$row[0];

					$upfail[$i] = $courseno;
				}

				if ($row[1] == "WP") {
					$suppoutput3 .= "DEF IN $row[0];";
					$countwp=$countwp + 1;
				}
				if ($row[1] == "DEF") {
					$suppoutput3 = "DEFFERED";
				}
				if ($row[1] == "EX") {
					$suppoutput3 .= "EXEMPTED IN $row[0]; ";
				}
				if ($row[1] == "DISQ") {
					$suppoutput3 = "DISQUALIFIED";
					$overallremark=="DISQUALIFIED";
				}
				if ($row[1] == "SP") {
					$suppoutput3 = "SUSPENDED";
					$overallremark=="SUSPENDED";
				}
				if ($row[1] == "LT") {
					$suppoutput3 = "EXCLUDE";
					$overallremark="EXCLUDE";
				}
				if ($row[1] == "WH") {
					$suppoutput3 = "WITHHELD";
					$overallremark="WITHHELD";
					$count = 0;
				}

				$year=$row[2];
			}

			while ($count2 < 27) {
				$count2 = $count2 + 1;
			}

			$calcount=$count1/($count+$count1)*100;

			if ($year=='1') {
		
				if ($calcount < 50) {
					$remarked = TRUE;
					$list[$acyr][$semester]['REMARK'] = 'EXCLUDE';

					$overallremark="EXCLUDE";
				}else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE;
							$list[$acyr][$semester]['REMARK'] = 'CLEAR PASS';
						} else {
							$remarked = TRUE;
							$list[$acyr][$semester]['REMARK'] = '$countwp ."\n".$suppoutput3.';
						}
	
						if ($countwp>2){
							$remarked = TRUE;
							$list[$acyr][$semester]['REMARK'] = '2'.$countwp.'<br> '.$suppoutput3.'WITHDRAWN WITH PERMISSION';
						} else {
							$remarked = TRUE;
							$list[$acyr][$semester]['REMARK'] = $suppoutput3;
						}
	
					}else {
						if ($count1 > 1) {
							$remarked = TRUE; 
							$output .= $suppoutput1;
							$list[$acyr][$semester]['REMARK'] = $output;
						}else {
							$remarked = TRUE;
							$output .= $suppoutput2;
							$list[$acyr][$semester]['REMARK'] = $output;
						}
					}
				}
	
			} else {
				
				if ($calcount < 75) {
					$remarked = TRUE; 
					$list[$acyr][$semester]['REMARK'] = $output;
					$overallremark="EXCLUDE";
				} else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE; 
							$list[$acyr][$semester]['REMARK'] = "CLEAR PASS";
						} else { 
							if ($countwp>2){
								$remarked = TRUE; 
								$list[$acyr][$semester]['REMARK'] = "WITHDRAWN WITH PERMISSION";
							}else{
								$remarked = TRUE; 
								$list[$acyr][$semester]['REMARK'] = $suppoutput3;
							}
						}
					} else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							$remarked = TRUE; 

							$list[$acyr][$semester]['REMARK'] = $output;
						} else {
							$output .= $suppoutput2;
							$remarked = TRUE; 

							$list[$acyr][$semester]['REMARK'] = $output;

						}
					}
				}
			}

		if($remarked == TRUE){
			
		} else {
			$list[$acyr][$semester]['REMARK'] = "WRONG";
		}

		if($i==0){
			$overallremark = "";
		}


		if(!empty($upfail)){
			$overallremark="FAILED";
		}


		$ocount=$ocount + $count;

		return $list;
	}	

}
?>
