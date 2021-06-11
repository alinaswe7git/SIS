<?php
class existing {

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

	function resultsExisting($item) {

		if(!isset($item) || $this->core->role < 100){
			$item = $this->core->userID;
		}

		if(isset($_GET['uid'])){
			$studentID = $_GET['uid'];
		} else {
			$studentID = $item;
		}

		$studentNo = $studentID;
		$start = substr($studentID, 0, 4);

		$sql = "SELECT CONCAT (Firstname,' ', IF(MiddleName = Firstname,'',MiddleName),' ', Surname) AS FNAME, `basic-information`.Status, Sex, Name,`study`.StudyType,`study`.ID AS StudyID FROM `basic-information`
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.ID = '$studentID' 
			LIMIT 1";
		$run = $this->core->database->doSelectQuery($sql);
	

		$started = FALSE;

		while ($fetch = $run->fetch_assoc()){

			$started = TRUE;

			//$firstname = $fetch['FirstName'];
			//$middlename = $fetch['Middlename'];
			//$surname = $fetch['Surname'];
			//$remark=$fetch['Remark'];
			$sex=$fetch['Sex']; 
			$studentname = $fetch['FNAME'];
			$studyID = $fetch['StudyID'];
			//$studentname = $firstname . " " . $middlename . " " . $surname;

			$program = $fetch['Name'];


			// PAYMENT VERIFICATION FOR GRADES
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($studentID);
			
			
			/***************temporary fix to sort out wrong registration**************/
			$sql_payroll_exempt= "SELECT * FROM ac_registration_exemption WHERE student_id = '$studentID'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
			
				//echo'<div class="warningpopup">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				//return FALSE;
				if($actual > 100){
					echo '<h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.number_format($actual,2).'</u>. 
					<br>Please check your payments and settle your balance to be able to access your grades
					<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$studentID.'">View your recent payments</a> </div>';
					
					//&& $this->core->role != 104 && $this->core->role != 103
					if($this->core->role != 1000){
						return;
					}
				}
			} else {
				echo '<div class="errorpopup">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>';
				echo'<div class="warningpopup">Temporary Payment exempted</div>';
				
			}
			/*
			if($actual > 100){
				echo '<h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.number_format($actual,2).'</u>. 
					<br>Please check your payments and settle your balance to be able to access your grades
					<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$studentID.'">View your recent payments</a> </div>';
					
				//&& $this->core->role != 104 && $this->core->role != 103
				if($this->core->role != 1000){
					return;
				}
			}
			*/
			if($this->core->action == "results"){
				echo'<div style="font-size: 16pt; padding-left: 30px; color: #333; margin-top: 15px;  clear: both; ">ORIGINAL UPLOAD OF RESULTS</div><br>';
				echo"<div style=\" width: 660px; padding-left: 30px; margin-top: 15px; height: 40px;\">
						Results for <b>$studentname</b>
						<br> Student No.:<b>$studentID</b>
						<br> Program: <b>$program</b>
						<br>
					</div>
					<div style=\" margin-top: 15px; margin-left: 30px;\">";
			}

			if(isset($year)){
				$overallremark= $this->currentyear($studentNo, $year);
			}else{
				$overallremark= $this->academicyear($studentNo);
			}

			//echo '<div style="  margin-left: 50px; margin-top: 10px;">';

			if ($overallremark=="EXCLUDE" or $overallremark=="DISQUALIFIED" or $overallremark=="SUSPENDED" or $overallremark=="WITHHELD" or $overallremark=="REPEAT SEMESTER" or $overallremark=="PROCEED SUPP"){
				//print "<hr><h2><b>OVERALL REMARK: $overallremark</b></h2>";
				
				


			} else { 
				//print "<hr><h2><b>OVERALL REMARK: PROCEED</b></h2>";
			}
			$periodID = $this->getPeriod();

			$year = date("Y");
			$sql = "INSERT INTO `comments` (`ID`, `StudentID`, `Year`, `PeriodID`, `Comment`, `Updated`, `StudyID`)
				VALUES (NULL, '$studentNo', '$year', '$periodID', '$overallremark', NOW(),'$studyID') 
				ON DUPLICATE KEY UPDATE `Comment`= '$overallremark';";
			$this->core->database->doInsertQuery($sql);

			echo '<div style="font-size:8px;">'. base64_encode($lis) . '</div>';

		}

		

	}

	private function currentyear($studentNo, $year) {

		echo '<table style="font-size: 11px; width: 700px; margin-top:-20px;">';

		$acyr = $year;
		$count = 0;
		$count1 = 0;
	
		$overallremark= $this->detail($studentNo, $acyr, $countyear, $repeat);
		$remark = $overallremark[0];
		$repeat = $overallremark[1];

		print "</table>\n";
		return $overallremark[0];
	}



	private function academicyear($studentNo) {

	
		echo '<table style="font-size: 11px; width: 700px; margin-top:30px;">';

		$sql = "SELECT distinct academicyear FROM `grades_process` WHERE StudentNo = '$studentNo' order by academicyear";

		$run = $this->core->database->doSelectQuery($sql);
		$countyear = 1;
		while ($fetch = $run->fetch_array()){
			$acyr = $fetch[0];
			$count = 0;
			$count1 = 0;
	
			$overallremark= $this->detail($studentNo, $acyr, $countyear, $repeat);
			$remark = $overallremark[0];
			$repeat = $overallremark[1];
			$countyear++;
		}

		print "</table>\n";
		return $overallremark[0];
	}

	private function detail($studentNo, $acyr, $countyear, $repeat) {

		$remarked = FALSE;

		echo'<tr class="heading">
			<td><br><b>'.$acyr.' (YEAR '.$countyear.')</b></td>
			<td><br><b>User</td>
			<td><br><b>COURSE NAME</td>
			<td><br><b>CA</b></td>
			<td><br><b>EXAM</b></td>
			<td><br><b>TOTAL</b></td>
			<td><br><b>GRADE</b></td>';
			


		echo'</tr>';

		$sql = "SELECT p1.CourseNo, p1.Grade, p2.CourseDescription, p1.ID, p1.Semester, `study`.ParentID, `study`.ID, p1.CAMarks, p1.ExamMarks, p1.TotalMarks, username
			FROM `grades_process` as p1
			LEFT JOIN `courses` as p2 ON p1.CourseNo = p2.Name  
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = '$studentNo'
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			LEFT JOIN `exm_users` ON `exm_users`.userid = p1.User
			WHERE 	 p1.StudentNo = '$studentNo'
			AND	 p1.AcademicYear = '$acyr'
			ORDER BY p1.Semester, p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$suppcount = 0;
		$countpass = 0;
		$dfail = 0;
		$dplusfail = 0;

		$i=0;
		$repeatlist = array();

			$ps = $_SERVER['REMOTE_ADDR'];
			$date = date('Y-m-d H:i:s');
			$lis =  $this->core->username . $ps . $date;
			

	
		$semester = 'Semester I';
		$cadplus=0;

		while ($row = $run->fetch_array()){
			$semester = $row[4];
			$school = $row[5];
			$study = $row[6];


			if($row[1] == ''){
				continue;
			}

			if($oldsemester != $semester){
				echo'<tr><td colspan="1" style="background-color:#ccc">'.$semester.'</td> <td colspan="6" style="background-color:#ccc"><div style="font-size:8px; color: #999;">'. base64_encode($lis) . '</div></td></tr>';
				$upfail = 0;
			}

			$i++;			
			echo "<tr>
				<td><b>$row[0]</b></td>
				<td>$row[10]</td>
				<td>$row[2]</td>
				<td>$row[7]</td>
				<td>$row[8]</td>
				<td>$row[9]</td>
				<td><b>$row[1]</b></td>";
			
			$oldsemester = $semester;

			if($this->core->role == "105" || $this->core->role == "1000"){
				echo '';
			}

			echo'</tr>';

			$count2 = $count2 + 3;

			if ($row[1] == "INC" or $row[1] == "D" or $row[1]=="F" or $row[1]=="E" or $row[1]=="NE") {

				$output .= "REPEAT $row[0]; ";
				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;
				}

				$courseno=$row[0];
				$countb=$countb + 1;
				$repeatlist[] =  $row[0];

				$upfail++;

				if($row[1] == "D"){
					$dfail++;
				}

	

			}
			

			if ($row[1]== "A+" or $row[1]=="A" or $row[1]=="B+" or $row[1]=="B" or $row[1]=="C+" or $row[1]=="C" or $row[1]=="P") {
				$k=$j-1;
				$countpass++;



				$index = "REPEAT $row[0];";
				$output = str_replace($index,'',$output);


				if (substr($row[0], -1) == 1){
					$count1=$count1 + 0.5;
					$count1before=$count1;

			 		if($upfail>0){
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
					if($upfail>0){
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
					
					if ($study ==216 && ($row[7] < 20)){
						$suppoutput1 .= "REPEAT $row[0]; ";
						$suppoutput2 .= "REPEAT $row[0]; ";
						$cadplus=$row[7];
					}else{
						$suppoutput1 .= "SUPP IN $row[0]; ";
						$suppoutput2 .= "REPEAT $row[0]; ";
					}
					
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

					$upfail++;

				if($row[1] == "D+"){
					$dplusfail++;
				}
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
					echo '<td class="title"><h2>EXCLUDE</h2> (Passed '.$countpass.' Courses)</td>';
					$overallremark="EXCLUDE";
				}else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE;
							echo '<td colspan="3" colspan="3"  class="title"><h2>CLEAR PASS</h2> (Passed '.$countpass.' Courses)</td>';
						} else {
							$remarked = TRUE;
							echo $countwp .'<br> '.$suppoutput3.'<br>';
						}
	
						if ($countwp>2){
							$remarked = TRUE;
							echo '2'.$countwp.'<br> '.$suppoutput3.'<br>';
							echo '<td colspan="3"  class="title"><h2>WITHDRAWN WITH PERMISSION</h2> (Passed '.$countpass.' Courses)</td>';
						} else {
							$remarked = TRUE;
							echo '<td colspan="3"  class="title"><h2>$suppoutput3</h2></td>'; 
						}
	
					}else {
						if ($count1 > 1) {
							$remarked = TRUE; 
							$output .= $suppoutput1;
							echo '<td colspan="3"  class="title"><h2>$output</h2> (Passed '.$countpass.' Courses)</td>';
						}else {
							$remarked = TRUE;
							$output .= $suppoutput2;
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2> (Passed '.$countpass.' Courses)</td>';
						}
					}
				}
	
			} else {
				
				if ($calcount < 75) {
					$remarked = TRUE; 
					echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
					$overallremark="EXCLUDE";
				} else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE; 
							echo'<td colspan="3"  class="title"><h2>CLEAR PASS</h2></td>';
						} else { 
							if ($countwp>2){
								$remarked = TRUE; 
								echo '<td colspan="3"  class="title"><h2>WITHDRAWN WITH PERMISSION</h2></td>'; 
							}else{
								$remarked = TRUE; 
								echo '<td colspan="3"  class="title"><h2>'.$suppoutput3.'</h2></td>'; 
							}
						}
					} else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							$remarked = TRUE; 
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
						} else {
							$output .= $suppoutput2;
							$remarked = TRUE; 
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
						}
					}
				}
			}

		if($remarked == TRUE){
			
		} else {
			echo 'WRONG';
		}
$overallremark = "";

		if($i==0){
			$overallremark = "";
		}



		if($study == 216 && $dfail == 1){
			$overallremark="REPEAT SEMESTER";
		}

		if($study == 216 && $dplusfail>=2){
			$overallremark="REPEAT SEMESTER";
			if($study == 216 && $dfail>=1){
				$overallremark="EXCLUDE";
			}
		}
		if($study == 216 && $dplusfail==3){
			$overallremark="REPEAT SEMESTER";
			
		}
		
		if($study == 216 && $dplusfail==1 && $cadplus < 20 ){
			//$overallremark="REPEAT SEMESTER";
			
		}
		
		if($study == 216 && $dplusfail == 2){
			$overallremark="PROCEED SUPP";
			if($study == 216 && $dfail>=1){
				$overallremark="EXCLUDE";
			}
		}

		if($study == 216 && $dplusfail>=4){
			$overallremark="EXCLUDE";
		}
		
		


		if($school == 205 && $upfail > 5){
			$overallremark="EXCLUDE";
		}else if($study == 216 && $dfail>=2){
			$overallremark="EXCLUDE";
		}else if($countpass > 0 && $overallremark != "REPEAT SEMESTER"){
			$overallremark="PROCEED";
		}
		$ocount=$ocount + $count;

		$out = array($overallremark, $repeatlist);
		return $out;
	}	

}
?>
