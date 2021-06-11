<?php
class statement {

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

	

	public function graduationStatement($item) {	
	
	
		$sql = "SELECT `basic-information`.ID, `FirstName`, `MiddleName`, `Surname`, COUNT(`grades`.StudentNo) AS COURSES, SUM(`grades`.Points) AS POINTS, `schools`.Name, `study`.ShortName
			FROM `grades`, `basic-information`, `student-study-link`, `study`, `schools`
			WHERE `basic-information`.ID = `grades`.StudentNo
			AND `basic-information`.ID = `student-study-link`.StudentID
			AND `student-study-link`.StudyID = `study`.ID
			AND `schools`.ID = `study`.ParentID	
			AND UPPER(`grades`.Grade) IN ('A+','A','B+','B','C+','C','P','EX','EXEMP', 'CP')
			AND `basic-information`.`Status` != 'Graduated'
			GROUP BY `grades`.StudentNo
			HAVING COUNT(`grades`.StudentNo) > 39
			ORDER BY `schools`.Name, `study`.Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table">
			<thead>
				<tr>
					<th>Student ID</td>
					<th>Name</td>
					<th>Courses</td>
					<th>Points</td>
					<th>Class</td>
					<th>School</td>
					<th>Program</td>
				</tr>
			</thead>
			<tbody>';
		while ($fetch = $run->fetch_array()){
			$sid = $fetch['ID'];

			$suspect = FALSE;
			$back = '';

			$sql = "SELECT * FROM `clients` WHERE `ClientID` = '$sid'";
			$runx = $this->core->database->doSelectQuery($sql);
			while ($fetchx = $runx->fetch_array()){
				$suspect = TRUE;
				$back = 'style="background-color: red;"';
			}



			$name = $fetch[	'FirstName'] . ' ' .  $fetch['MiddleName'] .' ' .  $fetch['Surname'];
			$courses = $fetch['COURSES'];
			$school = $fetch['Name'];
			$program = $fetch['ShortName'];
			$points = $fetch['POINTS'];


			switch (true){
			case ($points>=40):
				$class = "Distinction";
				break;
			case ($points>=30 && $points<40):
				$class = "Merit";
				break;
			case ($points>=20 && $points<30):
				$class = "Credit";
				break;
			case ($points<20):
				$class = "Pass";
			}


			echo'<tr '.$back.'>
				<td><a href="/information/show/'.$sid.'"<b>'.$sid.'</b></td>
				<td><b>'.$name.'</b></td>
				<td>'.$courses.' courses</td>
				<td>'.$points.' points</td>
				<td>'.$class.'</td>
				<td>'.$school.'</td>
				<td>'.$program.'</td>
				<td>'.$suspect.'</td>

			</tr>';
		}
		echo'</tbody></table>';
	}




	public function clientStatement($item) {	
	
	
		$sql = "SELECT `basic-information`.Status, `basic-information`.ID, `FirstName`, `MiddleName`, `Surname`, COUNT(`grades`.StudentNo) AS COURSES, SUM(`grades`.Points) AS POINTS, `schools`.Name, `study`.ShortName
			FROM `grades`, `basic-information`, `student-study-link`, `study`, `schools`, `clients`
			WHERE `basic-information`.ID = `grades`.StudentNo
			AND `basic-information`.ID = `clients`.ClientID
			AND `basic-information`.ID = `student-study-link`.StudentID
			AND `student-study-link`.StudyID = `study`.ID
			AND `schools`.ID = `study`.ParentID	
			AND UPPER(`grades`.Grade) IN ('A+','A','B+','B','C+','C','P','EX','EXEMP', 'CP')
			GROUP BY `grades`.StudentNo
			ORDER BY `schools`.Name, `study`.Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Student ID</td>
					<th>Name</td>
					<th>Courses</td>
					<th>Points</td>
					<th>Class</td>
					<th>School</td>
					<th>Program</td>
					<th>Status</td>
				</tr>
			</thead>
			<tbody>';
		while ($fetch = $run->fetch_array()){
			$sid = $fetch['ID'];


			$name = $fetch[	'FirstName'] . ' ' .  $fetch['MiddleName'] .' ' .  $fetch['Surname'];
			$courses = $fetch['COURSES'];
			$school = $fetch['Name'];
			$program = $fetch['ShortName'];
			$points = $fetch['POINTS'];
			$status = $fetch['Status'];


			switch (true){
			case ($points>=40):
				$class = "Distinction";
				break;
			case ($points>=30 && $points<40):
				$class = "Merit";
				break;
			case ($points>=20 && $points<30):
				$class = "Credit";
				break;
			case ($points<20):
				$class = "Pass";
			}

			if($courses<40 && $status != 'Graduated'){
				$class="NOT GRADUATING";
			}
			$i++;

			echo'<tr '.$back.'>
				<td>'.$i.'</td>
				<td><a href="/information/show/'.$sid.'"<b>'.$sid.'</b></td>
				<td><b>'.$name.'</b></td>
				<td>'.$courses.' courses</td>
				<td>'.$points.' points</td>
				<td>'.$class.'</td>
				<td>'.$school.'</td>
				<td>'.$program.'</td>
				<td>'.$status.'</td>

			</tr>';
		}
		echo'</tbody></table>';
	}




	public function compareStatement($item) {	
	
	
		$sql = "SELECT `case`, `basic-information`.Status, `basic-information`.ID, `FirstName`, `MiddleName`, `Surname`, COUNT(`grades`.StudentNo) AS COURSES, SUM(`grades`.Points) AS POINTS, `schools`.Name, `study`.ShortName
			FROM `grades`, `basic-information`, `student-study-link`, `study`, `schools`, `clients`, `graduates`
			WHERE `basic-information`.ID = `grades`.StudentNo
			AND `basic-information`.ID = `clients`.ClientID
			AND `graduates`.StudentID = `basic-information`.ID
			AND `basic-information`.ID = `student-study-link`.StudentID
			AND `student-study-link`.StudyID = `study`.ID
			AND `schools`.ID = `study`.ParentID	
			AND UPPER(`grades`.Grade) IN ('A+','A','B+','B','C+','C','P','EX','EXEMP', 'CP')
			GROUP BY `grades`.StudentNo
			ORDER BY `case` DESC";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Student ID</td>
					<th>Name</td>
					<th>Courses Passed</td>
					<th>School</td>
					<th>Program</td>
					<th>Status</td>
				</tr>
			</thead>
			<tbody>';
		while ($fetch = $run->fetch_array()){
			$sid = $fetch['ID'];


			$name = $fetch[	'FirstName'] . ' ' .  $fetch['MiddleName'] .' ' .  $fetch['Surname'];
			$courses = $fetch['COURSES'];
			$school = $fetch['Name'];
			$program = $fetch['ShortName'];
			$points = $fetch['POINTS'];
			$status = $fetch['case'];



			switch (true){
			case ($points>=40):
				$class = "Distinction";
				break;
			case ($points>=30 && $points<40):
				$class = "Merit";
				break;
			case ($points>=20 && $points<30):
				$class = "Credit";
				break;
			case ($points<20):
				$class = "Pass";
			}

			if($courses<40 && $status != 'Graduated'){
				$class="NOT GRADUATING";
			}
			$i++;

			echo'<tr '.$back.'>
				<td>'.$i.'</td>
				<td><a href="/information/show/'.$sid.'"<b>'.$sid.'</b></td>
				<td><b>'.$name.'</b></td>
				<td>'.$courses.' courses</td>
				<td>'.$school.'</td>
				<td>'.$program.'</td>
				<td>'.$status.'</td>

			</tr>';
		}
		echo'</tbody></table>';
	}






	function overviewStatement($item) {		
		$studentID = $_GET['uid'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT COUNT( DISTINCT  `grades`.StudentNo) FROM `grades`, `basic-information`
			WHERE `grades`.StudentNo LIKE '$studentID%' AND `basic-information`.ID = `grades`.StudentNo ";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$count = $fetch[0];

			$pages = $count / 100;
			$count = 1;

			while($count < $pages){
				$show = $count * 100;
				$old  = $show-100;
				echo 'Print results from '.$old.' to '.$show.' - <a href="' . $this->core->conf['conf']['path'] . '/statement/batch/?uid='.$studentID.'&start='.$old.'&end='.$show.'">CLICK HERE</a><br>';
				$count++;
			}
		}
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
			$year =$date_year; 
			$semester =2;
		}else if($date_month <=6){
			$year = $date_year; 
			$semester = 1;
		}

		$sql = "SELECT * FROM `periods` WHERE `Year` = '$year' AND `Semester` = '$semester'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
		}
		return $item;
	}

	function batchStatement($item) {
		$studentID = $_GET['uid'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT `grades`.StudentNo FROM `grades`, `basic-information`
			WHERE `grades`.StudentNo LIKE '$studentID%' 
			AND `basic-information`.ID = `grades`.StudentNo 
			GROUP BY `grades`.StudentNo ORDER BY `grades`.StudentNo DESC";

		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;

		while ($fetch = $run->fetch_array()){
			if($x == 5){
				echo'<div style="page-break-after: always;"> </div> ';
				$x=0;
			} else {
				if($first == FALSE){
					echo "<hr noshade>";
				}
			}

			$studentid = $fetch[0];
			$this->resultsStatement($studentid);
			$first = FALSE;
			$i++;
			$x++;
		}

		echo'<script type="text/javascript">
			window.print();
		</script>';

	}
	
	function resultsStatement($item) {

		if(!isset($item) || $this->core->role < 100){
			$item = $this->core->userID;
		}

		if(isset($_GET['uid'])){
			$studentID = $_GET['uid'];
		} else {
			$studentID = $item;
		}

		if($this->core->userID == '628' OR $this->core->userID == '2010226397'){
			echo '<div class="toolbar"> <a href="' . $this->core->conf['conf']['path'] . '/existing/results/'.$studentID.'">SHOW INITIALLY UPLOADED RESULTS</a> </div>';
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
					echo '<h2>OUTSTANDING BALANCE!</h2><div class="alert alert-warning" role="alert">According to our financial records you are owing the institution <u>K'.number_format($actual,2).'</u>. 
					<br>Please check your payments and settle your balance to be able to access your grades
					<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$studentID.'">View your recent payments</a> </div>';
					
					//&& $this->core->role != 104 && $this->core->role != 103
					if($this->core->role != 1000){
						return;
					}
				}
			} else {
				echo '<div class="alert alert-warning" role="alert">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>';
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
				echo"<p style='margin-left:30px;'><i>All Official Communication should be<br> 		
						Addressed to the <strong>Executive Director</strong>	<br>
						And Not to Individual Officers
					<i></p>

					<div style='display:flex;'>
					<div style='width:50%;'>
					<img src='../../templates/loanpro/images/logo-large.png' style='margin-left: 30px; width:128px;height:128px;'>
					</div>

					<div style='width:50%; float:right;'>
					<h2 style='text-align:right;'><strong>NATIONAL INSTITUTE OF PUBLIC<br> ADMINISTRATION</strong></h2><br>
					<p style='text-align:right;'>
						<b>P.O. Box 31990</b><br>
						<b>Plot 4810, Dushanbe Road, Lusaka, Zambia</b><br>
						Tel: +260 211 228802-4, 233643, 222480<br>
						Fax: +260 211 227213<br>
						E-Mail: executivedirector@nipa.ac.zm<br>
						Website: www.nipa.ac.zm<br>

					</p>
					</div>
					</div>
					<div style='font-size: 16pt; padding-left: 30px; color: #333; margin-top: 15px;  clear: both; '>STATEMENT OF RESULTS</div><br>";
				echo"
					<div style='text-align:right; margin-right:50px'>
						
						<p id='date'></p>
						

						
					</div>

					<div style=' width: 660px; padding-left: 30px; margin-top: 15px; height: 40px;'>
						Results for <b>$studentname</b>
						<br> Student No.:<b>$studentID</b>
						<br> Program: <b>$program</b>
						<br>
					</div>
					<div style=' margin-top: 15px; margin-left: 30px;'>
						<script>
						var d = new Date();
						document.getElementById('date').innerHTML = d.toDateString();
						
						</script>

					";
			}

			if(isset($year)){
				$overallremark= $this->currentyear($studentNo, $year);
			}else{
				$overallremark= $this->academicyear($studentNo);
			}

			//echo '<div style="  margin-left: 50px; margin-top: 10px;">';

			if ($overallremark=="EXCLUDE" or $overallremark=="DISQUALIFIED" or $overallremark=="SUSPENDED" or $overallremark=="WITHHELD" or $overallremark=="REPEAT SEMESTER" or $overallremark=="PROCEED SUPP"){
				print "<hr><h2><b>OVERALL REMARK: $overallremark</b></h2>";
				
				


			} else { 
				print "<hr><h2><b>OVERALL REMARK: PROCEED</b></h2>";
			}
			$periodID = $this->getPeriod();

			$year = date("Y");
			$sql = "INSERT INTO `comments` (`ID`, `StudentID`, `Year`, `PeriodID`, `Comment`, `Updated`, `StudyID`)
				VALUES (NULL, '$studentNo', '$year', '$periodID', '$overallremark', NOW(),'$studyID') 
				ON DUPLICATE KEY UPDATE `Comment`= '$overallremark';";
			$this->core->database->doInsertQuery($sql);

			echo '<div style="font-size:8px;">'. base64_encode($lis) . '</div>';

			if($this->core->action == "results"){
				echo "<hr>
				<p style='text-align:left; margin-left:50px;'>
					<br><br><br><br>
					<b>Nasilele B. Nasilele</b><br><br>
					DEPUTY REGISTRAR - ACADEMIC AFFAIRS<br>
					For/EXECUTIVE DIRECTOR</b>
					<br>
				</p>
				<hr>
				<p style='text-align:right; margin-right:100px;'>
						<br><br>
						<b>Maxwell Saya</b><br><br>
						REGISTRAR<br><br>
						For/EXECUTIVE DIRECTOR<br>
				</p>";
			}
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

		$sql = "SELECT distinct academicyear FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";

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
			<td><br><b>COURSE NAME</td>
			<td><br><b>GRADE</b></td>';
			


		echo'</tr>';

		$sql = "SELECT p1.CourseNo, p1.Grade, p2.CourseDescription, p1.ID, p1.Semester, `study`.ParentID, `study`.ID, p1.CAMarks,p1.AcademicYear, p2.ID,
				(SELECT ID FROM periods WHERE Name=p1.semester AND Year=p1.AcademicYear) AS period
			FROM `grades` as p1
			LEFT JOIN `courses` as p2 ON p1.CourseNo = p2.Name  
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = '$studentNo'
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
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
				echo'<tr><td colspan="1" style="background-color:#ccc">'.$semester.'</td> <td colspan="2" style="background-color:#ccc"><div style="font-size:8px; color: #999;">'. base64_encode($lis) . '</div></td></tr>';
				$upfail = 0;
			}

			$i++;
			//Code to check evaluation from students
			$grade ='';
			$semesterEv = $row[4];
			$academicyearEv = $row[8];
			$courseIDEv = $row[9];
			$periodEv = $row[10];
			
			if ($this->core->role != 1000 ){
				if ($periodEv >= 57){
					$sqlev ="SELECT COUNT(*) AS num, PeriodID FROM evaluation 
							WHERE UserID =$studentNo
							AND CourseID=$courseIDEv
							AND PeriodID=(SELECT ID FROM periods WHERE Name='$semesterEv ' AND Year='$academicyearEv')";
					
					$runev = $this->core->database->doSelectQuery($sqlev);
					while ($rowev = $runev->fetch_array()){
						$check = $rowev[0];
					
					}
					if($check > 0){
						$grade = $row[1];
					}else{
						
						$grade = '  <a href="' . $this->core->conf['conf']['path'] . '/evaluation/course/">Click to evaluate</a>';
					}
				}else{
					$grade = $row[1];	
				}
			}else{
				$grade = $row[1];
			}
			
			//$grade = $row[1]; End of evaluation check code
			
			echo "<tr>
				<td><b>$row[0]</b></td>
				<td>$row[2]</td>
				<td><b>$grade</b></td>";
			
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
