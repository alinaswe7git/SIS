<?php
class examination {

	public $core;
	public $view;
	public $item = NULL;
	public $register = FALSE;
	public $payments = FALSE;

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

	function selfExamination() {
		$uid = $this->core->userID;
		$this->resultsExamination($uid);
	}

	function printExamination() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$schools = $optionBuilder->showSchools();
		$periods = $optionBuilder->showPeriods();
		$programs = $optionBuilder->showStudies();
		$centres = $optionBuilder->showCentres();
		include $this->core->conf['conf']['formPath'] . "searchexamslip.form.php";
	}

	function batchExamination($item) {
		$studentID = $this->core->cleanGet['uid'];
		$start = $this->core->cleanGet['start'];
		$end = $this->core->cleanGet['end'];
		$time = $this->core->cleanGet['time'];
		$period = $this->core->cleanGet['period'];
		$sid = $this->core->cleanGet['schools'];
		$study = $this->core->cleanGet['study'];
		$centre = $this->core->cleanGet['centre'];
		
		if($centre == "" ){
				$centre = '%';
		}
			
			
		if(!empty($study)){
			
			$sql = "SELECT `basic-information`.ID FROM `basic-information`, `study`, `student-study-link`, `schools` 
				WHERE `basic-information`.`Status` IN ('Requesting', 'Approved')
				AND `basic-information`.StudyType = '$time' 
				AND `student-study-link`.StudentID = `basic-information`.ID
				AND `student-study-link`.StudyID = `study`.ID 
				AND `study`.ParentID = `schools`.ID
				AND `study`.ID  = '$study'
				GROUP BY `basic-information`.ID";
				
		} else {
			
			$sql = "SELECT `basic-information`.ID 
				FROM `basic-information`, `study`, `student-study-link`, `schools`,`student-data-other` ,`periods`
				WHERE `basic-information`.`Status` IN ('Requesting', 'Approved')
				AND `basic-information`.StudyType = '$time' 
				AND `student-study-link`.StudentID = `basic-information`.ID
				AND `student-study-link`.StudyID = `study`.ID 
				AND `study`.ParentID = `schools`.ID
				AND `periods`.ID = '$period'
				AND `schools`.ID = '$sid'
				AND `basic-information`.ID = `student-data-other`.StudentID
				AND `student-data-other`.ExamCentre LIKE '$centre'
				GROUP BY `basic-information`.ID";
				
		}

		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;


		echo	'PRINTING FOR: ' . $run->num_rows . ' STUDENTS <hr>';
		
		while ($fetch = $run->fetch_array()){
			
			if($x == 2){
				//echo'<div style="page-break-after: always;"> </div> ';
				$x=0;
			} else {
				if($first == FALSE){
					//echo "<hr noshade>";
				}
			}

			$studentid = $fetch[0];
					$this->resultsExamination($studentid, $period);
		
			$first = FALSE;
			$i++;
			$x++;
		}


	}

	function listExamination($item) {
		$uid = $_GET['uid'];

		$students = explode(",", $uid);

		foreach($students as $studentid){
			$studentid = trim($studentid);
			$this->resultsExamination($studentid);
		}
	}
	
	function resultsExamination($item, $period) {

		//echo'<div class="errorpopup" style="border: 2px solid #000; padding: 15px; background-color: #ffce75;">NOTE THAT EVALUATING YOUR COURSES IS MANDATORY AND YOU MUST FILL IN THE COURSE EVALUATION FORMS <a href="/evaluation/course">CLICK HERE TO DO YOUR COURSE EVALUATIONS</a>. IF YOU DO NOT EVALUATE ALL YOUR COURSES RESULTS FOR THOSE COURSES NOT EVALUATED WILL BE HIDDEN. <b>-DVC</b></div>';

		if(empty($period)){
			$period = $this->core->getPeriod();
		}		 
		  
		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}

		if($item == ""){
			$studentID = $_GET['uid'];
			$period = $_GET['period'];
		} else {
			$studentID = $item;
		}
     	 
		$syear = substr($studentID, 0, 4);
		$cyear = date("Y");
		$year =  $cyear - $syear;

		$sqlx = "SELECT `study`.Name as study, `study`.ParentID, `schools`.Name as school 
		FROM `student-study-link`, `study`, `schools` 
		WHERE `student-study-link`.StudentID = '$studentID' 
		AND `student-study-link`.StudyID = `study`.ID 
		AND `study`.ParentID = `schools`.ID LIMIT 1";

		$school = FALSE;
	
		$runx = $this->core->database->doSelectQuery($sqlx);
		
		while ($fetchx = $runx->fetch_assoc()){	
			$study = $fetchx["study"];
			$school = $fetchx["school"];
		}

		

		$sql = "SELECT DISTINCT `basic-information`.ID,
					`basic-information`.GovernmentID, 	
					`basic-information`.Firstname,
					`basic-information`.MiddleName, 
					`basic-information`.Surname,
					`basic-information`.Status, 
					`basic-information`.Sex, 
					`courses`.Name, 
					`courses`.ID, 
					`courses`.CourseDescription, 
					`courses`.CourseCredit,
					`student-data-other`.ExamCentre, 
					`periods`.Year, 
					`periods`.Name as Semester, 
					`balances`.LastTransaction
			FROM `course-electives`
			LEFT JOIN `student-data-other` ON `course-electives`.StudentID =  `student-data-other`.StudentID AND `student-data-other`.PeriodID = '$period'
			LEFT JOIN `balances` ON `balances`.StudentID =  `course-electives`.StudentID
			LEFT JOIN `basic-information` ON `course-electives`.StudentID = `basic-information`.ID
			LEFT JOIN `courses` ON `course-electives`.CourseID = `courses`.ID 
			LEFT JOIN `periods` ON `course-electives`.PeriodID = `periods`.ID
			WHERE  `course-electives`.StudentID = '$studentID' 
			AND `course-electives`.Approved = '1'
			AND `course-electives`.PeriodID = $period";
	
		$run = $this->core->database->doSelectQuery($sql);



		$sqlx = "SELECT DISTINCT `courses`.Name
			FROM `course-electives`
			LEFT JOIN `student-data-other` ON `course-electives`.StudentID =  `student-data-other`.StudentID AND `student-data-other`.PeriodID = '$period'
			LEFT JOIN `balances` ON `balances`.StudentID =  `course-electives`.StudentID
			LEFT JOIN `basic-information` ON `course-electives`.StudentID = `basic-information`.ID
			LEFT JOIN `courses` ON `course-electives`.CourseID = `courses`.ID 
			LEFT JOIN `periods` ON `course-electives`.PeriodID = `periods`.ID
			WHERE  `course-electives`.StudentID = '$studentID' 
			AND `course-electives`.Approved = '1'
			AND `course-electives`.PeriodID = $period";
	
		$runx = $this->core->database->doSelectQuery($sqlx);

			while ($fetchx = $runx->fetch_assoc()){
				$courses = $courses. $fetchx['Name'] . "\n";
			}

		

		$count = 1;
		$currentid = TRUE;
		$total = 0; 
		$start = TRUE;
		$billed = FALSE;
		///Get balances
		include_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		//if($payments == FALSE){
		$this->payments = new payments();
		//}
		$this->payments->buildView($this->core);
		$balance = $this->payments->getBalance($studentID);
		$balance = round($balance);
		$calc=0;
	
		
		if($balance < 0){
			$calc = $balance+$threshold;
		}else{
			$calc = $balance-$threshold;
		}
		
		//$this->payments->checkStudentPayments($studentID);
		//$calc = $balance-$threshold;
		
		// PAYMENT VERIFICATION FOR EXAM SLIP Addition of 80% Check
		//include_once $this->core->conf['conf']['viewPath'] . "register.view.php";
		//if($this->register == FALSE){
		//	$this->registration = new register();
		//}
		//$this->registration->buildView($this->core);
		//$this->registration->getBillSage($studentID, $num_courses, FALSE);
		/*
		$sql_bill = "SELECT MAX(Amount) AS Amount FROM `paymets-trail` WHERE StudentID =$studentID AND TYPE='BILLING' AND (Description LIKE 
	CONCAT('%',(SELECT SUBSTRING(Description,1,18) FROM `billing-temp` WHERE StudentID=$studentID AND `Amount` <> 0 AND PeriodID=".$period."),'%') OR Description LIKE '%2020%')";
		
		$run_bill = $this->core->database->doSelectQuery($sql_bill);
		
		$bill  = 0;
		
		while ($fetch_bill = $run_bill->fetch_assoc()) {
			$bill  = $fetch_bill['Amount'];
			
			///Check if student is billed
			if($bill != 0){
				$billed = TRUE;	
				
			}
		}
		
			

		if($bill == 0){
			
			$sql_bill = "SELECT * FROM `billing-temp` WHERE StudentID='$studentID' AND `Amount` <> 0 AND PeriodID=".$period;
			$run_bill = $this->core->database->doSelectQuery($sql_bill);
			
			while ($fetch_bill = $run_bill->fetch_assoc()) {
				$bill  = $fetch_bill['Amount'];
			}
			
			if($bill == 0){
				echo'<div class="errorpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;">80% threshold could not be determined because no bill was set for this program by Accounts Department</div>';
				return FALSE;
				
			}
		}
		


		if($bill=="21450" || $bill == "35250"){
			$bill = 9750;
			
		}
		*/
		$threshold = round($bill*0.20);
		$percentail = round($bill*0.80);
		
		
		
		
		
		
		//echo'<div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;"><b>YOU HAVE NOT MET THE CRITERIA</b><br>STUDENT BALANCE: K'.number_format($balance).'   <br>BALANCE DEFICIT: K'.number_format($calc).'</div>';
		/*
		if($billed == TRUE){
				
			if ($balance > $threshold){
			
			$sql_payroll_exempt= "SELECT * FROM ac_payroll WHERE student_id = '$studentID'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
				
				echo'<br><div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;"><b>YOU HAVE NOT MET THE CRITERIA FOR EXAMINATIONS</b><br> K '.$threshold.' STUDENT BALANCE: K'.number_format($balance).'   <br>BALANCE DEFICIT: K'.number_format($calc).'</div>';
				
				if($school == '204'){
					echo'<div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;"><b>YOU HAVE NOT MET THE CRITERIA</b><br>STUDENT BALANCE: K'.number_format($balance).'   <br>BALANCE DEFICIT: K'.number_format($calc).'</div>';	
					return FALSE;
				}else{
					/*echo'<h1>COMMITMENT TO SETTLE BALANCE</h1>
					<b>In order to write your exam you must fill in the following details: </b>
					<p>I have contacted my sponsor and have confirmed payment arrangements for my remaining balance of K'.number_format($balance).'. <br> Failing to settle my remaining balance will result in my results not being proccessed.</p>
					<h3>Name sponsor: ....................................................................</h3>
					<h3>NRC sponsor: ......................................................................</h3>
					<h3>Phone numbers sponsor: ....................................................</h3>
					<h3>Source of Income: ...............................................................</h3>
					<h3>Date before which balance will be paid: ...../...../2019</h3>

					<b>I hereby certify that the details for my sponsor as indicated on this form are correct</b>
					<h2>SIGNATURE: ..................................................</h2>';
					*/
					/*
					echo'<br><div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;">You cannot print the exam slip because your balance is currently K'.number_format($balance).', therefore the you have not met the 80% threshhold required to collect an exam slip. <b><a href="javascript:history.go(-1)">GO BACK</a></b></div>';
					return FALSE;
				}
			} else {
				
				echo'<div class="warningpopup">Paying via payroll or exempted</div>';
				
			}
			}
		}else {
			echo $billed;
			if ($balance < $threshold){
			
			$sql_payroll_exempt= "SELECT * FROM ac_payroll WHERE student_id = '$studentID'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
				$calc = $balance-$threshold;
				echo'<br><div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;"><b>YOU HAVE NOT MET THE CRITERIA FOR EXAMINATIONS</b><br>'.$threshold.' STUDENT BALANCE: K'.number_format($balance).'   <br>BALANCE DEFICIT: K'.number_format($calc).'</div>';
				
				if($school == '204'){
					echo'<div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;"><b>YOU HAVE NOT MET THE CRITERIA</b><br>STUDENT BALANCE: K'.number_format($balance).'   <br>BALANCE DEFICIT: K'.number_format($calc).'</div>';	
					return FALSE;
				}else{
					/*echo'<h1>COMMITMENT TO SETTLE BALANCE</h1>
					<b>In order to write your exam you must fill in the following details: </b>
					<p>I have contacted my sponsor and have confirmed payment arrangements for my remaining balance of K'.number_format($balance).'. <br> Failing to settle my remaining balance will result in my results not being proccessed.</p>
					<h3>Name sponsor: ....................................................................</h3>
					<h3>NRC sponsor: ......................................................................</h3>
					<h3>Phone numbers sponsor: ....................................................</h3>
					<h3>Source of Income: ...............................................................</h3>
					<h3>Date before which balance will be paid: ...../...../2019</h3>

					<b>I hereby certify that the details for my sponsor as indicated on this form are correct</b>
					<h2>SIGNATURE: ..................................................</h2>';
					*//*
					echo'<br><div class="warningpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;">You cannot print the exam slip because your balance is currently K'.number_format($balance).', therefore the you have not met the 80% threshhold required to collect an exam slip. <b><a href="javascript:history.go(-1)">GO BACK</a></b></div>';
					return FALSE;
				}
			} else {
				
				echo'<div class="warningpopup">Paying via payroll or exempted</div>';
				
			}
		}	
		}
		*/
		
		/*// PAYMENT VERIFICATION FOR EXAM SLIP Cont
		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$balance = $payments->getBalance($studentID);
		//$balance = 'Unverified';
			*/
			/*
		$sqlw = "SELECT * FROM `ac_payroll` WHERE `student_id` = '$studentID'";
		$runw = $this->core->database->doSelectQuery($sqlw);

		$bal = $balance;
		if($runw->num_rows > 0){
			$balance = 'Payroll';
		}
		*/

		while ($fetch = $run->fetch_assoc()){

			$id = $fetch["ID"];
			$firstname = $fetch["Firstname"];
			$middlename = $fetch["MiddleName"];
			if($firstname == $middlename){
				$firstname="";
			}
			$surname = $fetch["Surname"];
			$status = $fetch["Status"];
			$sex = $fetch["Sex"]; 
			$courseid = $fetch["CID"]; 
			$description = $fetch["CourseDescription"]; 
			$programno = $fetch["ProgramName"]; 
			$programid = $fetch["ProgramID"]; 
			$type = $fetch["ProgramType"];
			$course = $fetch["Name"]; 
			$nrc = $fetch["GovernmentID"];
			$started = TRUE;
			$studentname = $firstname . " " . $middlename . " " . $surname;
			$examcent = $fetch["ExamCentre"];
			$status = $fetch["Status"];
			$year = $fetch["Year"]; 
			$semester = $fetch["Semester"]; 

			$name = $fetch["CourseDescription"];
			$credits = $fetch["CourseCredit"];

			$lasttrans = $fetch['LastTransaction'];

			if($status == "Requesting" || $status == "Approved" ){
				$status = "Fully registered";
			} else {
				$status = "NOT FULLY REGISTERED";
			}
			

			if($start == TRUE){
				// SECURITY
				$rand = rand(100000,999999);

				$owner = $this->core->userID;
				$secname = $studentID . "-".date('Y-m-d')."-".$rand;
				 ///opt/lampp/htdocs/sis/datastore/output/secure
				$path = "datastore/output/secure/";
				$filename = $path. $secname . ".htm";
	
				require_once $this->core->conf['conf']['classPath'] . "security.inc.php";
				$security = new security();
				$security->buildView($this->core);
				$balance=0;
			
				$qrname = $security->qrSecurity($studentID, $studentID, $courses, $studentname, $balance);
				$start = FALSE;
			}

		

			// BEGIN PRINTING COURSES
			if($currentid == TRUE){
				echo'<div style="clear:left; width: 800px; padding-top:20px; padding-left: 25px; min-height: 530px; display:block; margin-top: 15px; border: 3px solid #000; page-break-inside: avoid; ">
					<div style="float: left; width: 800px; position: relative; "><div style="position: absolute; top: -15px; right: 10px; font-size: 10pt;"><img src="'.$this->core->conf['conf']['path'].'/'.$qrname.'"/><br>'.$secname.'</div>';
					//echo'<iframe src="qrset.php?data=Alinaswe" height="100" width="100" ></iframe>';
					echo'
					<center>
							<a href="'. $this->core->conf['conf']['path'] .'">
								<img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" />
							</a>
						</center>
					</div>
					<div style="font-size: 18pt; color: #000; margin-top: 15px; width: 800px; ">
						<center>
							'.$this->core->conf['conf']['organization'].'
							<div style="font-size: 13pt; font-weight: bold;">Examination Slip '.$year.' - '.$semester.'</div>
						</center>
					</div>
					<div style="width: 800px; margin-left: 20px; margin-top: 20px;">';


					// DISPLAY STUDENT PHOTO
					//echo'<div style="width: 100px; float: left; margin-right: 20px; border: 1px solid #000;">
					//<img width="100px" src="'.$this->core->conf['conf']['path'].'/information/picture/'.$studentID.'">
					//</div>';
					echo'<div style="width: 140px; float: left; margin-right: 20px; border: 1px solid #000;">';
					if (file_exists("datastore/identities/pictures/$studentID.png_final.png")) {
						echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $studentID. '.png_final.png">';
					} else 	if (file_exists("datastore/identities/pictures/$studentID.png")) {
						echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $studentID. '.png">';
					} else {
						echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
					}
					echo'</div>';



						echo'<div style="float: left; width: 300px; ">
							Name: <b>'.$studentname.'</b> 
							<br> StudentID No.: <b>'.$studentID.'</b>
							<br> NRC No.: <b>'.$nrc.'</b>
							<br> Sex: <b>'.$sex.'</b> 
						</div>
						<div style="float: left; width: 400px;">
							 Division: <b>'.$school.'</b>
							<br> Study: <b>'.$study.'</b>
							<br> Exam Center: <b>'.$examcent.'</b>
							<br> Status: <b>'.$status.'</b>
						</div>
					</div>

					<div style="clear: both; width: 800px; margin-left: 20px; padding-top: 20px;">
						<b>Candidate has been authorized to write examination in the following courses: </b>
					</div>

					<div style="width: 600px; margin-left: 20px; margin-top: 20px;">
				       		<div style="float: left; width: 600px;">
							<u>REGISTERED COURSES:</u>
						</div>';

				$currentid = FALSE;
			}

			echo'<div style="float: left; width: 600px;">'.$count.' - <b>'.$course.'</b> - '.$description.'</div>';

			$count++;
			$isset = TRUE;
			$total = $total+$credits;
		}
	
		if($isset == TRUE){

			$this->core->audit(__CLASS__, $studentID, $studentID, "Exam slip printed $studentID - K$bal - $period");

		
			echo '</div>
			</div>';
			$isset = FALSE;
		}
	}


	private function academicyear($studentNo) {

	
		echo '<table style="font-size: 11px;">';

		$sql = "SELECT distinct academicyear FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";

		$run = $this->core->database->doSelectQuery($sql);
		$countyear = 1;
		while ($fetch = $run->fetch_array()){
			print "<tr>\n";
			$acyr = $fetch[0];
			$count = 0;
			$count1 = 0;
	
			$overallremark= $this->detail($studentNo, $acyr, $countyear, $repeat);
			$remark = $overallremark[0];
			$repeat = $overallremark[1];
			$countyear++;
			
		//	var_dump($repeat);
		
			print "</tr>\n\n";
		}

		print "</table>\n";
		

		return $remark;
	}

	private function detail($studentNo, $acyr, $countyear, $repeat) {

		print "<td>";
		print "$acyr";
		print "&nbsp";
		print "(YEAR $countyear)</td>";
		print "<td>&nbsp&nbsp</td>";

		$sql = "SELECT 
				p1.CourseNo,
				p1.Grade,
				p2.CourseDescription
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$i=0;
		$repeatlist = array();

		while ($row = $run->fetch_array()){
			$i++;			
			echo "<td>$row[0]</td><td><b>$row[1]</b></td><td>&nbsp&nbsp</td>";
			$count2 = $count2 + 3;

			if ($row[1] == "IN" or $row[1] == "D" or $row[1]=="F" or $row[1]=="NE") {

				$output .= "REPEAT $row[0];";
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

				$suppoutput1 .= "SUPP IN $row[0]; ";
				$suppoutput2 .= "REPEAT $row[0]; ";

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
				print "<td>&nbsp&nbsp</td>";
				$count2 = $count2 + 1;
			}

			$calcount=$count1/($count+$count1)*100;

			if ($year=='1') {
		
				if ($calcount < 50) {
					print "<td>EXCLUDE</td>";
					$overallremark="EXCLUDE";
				}else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							print "<td>CLEAR PASS</td>";
						} else { 
							print "$countwp<br> $suppoutput3<br>";
						}
	
						if ($countwp>2){
							print "2$countwp<br> $suppoutput3<br>";
							print "<td>WITHDRAWN WITH PERMISSION</td>";
						} else {
							print "<td>$suppoutput3</td>"; 
						}
	
					}else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							print "<td>$output</td>";
						}else {
							$output .= $suppoutput2;
							print "<td>$output</td>";
						}
					}
				}
	
			} else {

				if ($calcount < 75) {
					print "<td>EXCLUDE</td>";
					$overallremark="EXCLUDE";
				} else {


					if ($countb == 0) {
						if ($suppoutput3=="") {
							print "<td>CLEAR PASS</td>";
						} else { 
							if ($countwp>2){
								print "<td>WITHDRAWN WITH PERMISSION</td>"; 
							}else{
								print "<td>$suppoutput3</td>"; 
							}
						}
					} else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							print "<td>$output</td>";
						} else {
							$output .= $suppoutput2;
							print "<td>$output</td>";
						}
					}
				}
			}

	

		if(!empty($upfail)){
			$overallremark="FAILED";
		}


		$ocount=$ocount + $count;

		$out = array($overallremark, $repeatlist);
		return $out;
	}	

}
?>
