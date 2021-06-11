<?php
class register {

	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->internalMenu = TRUE;
		$this->view->javascript = array('register', 'jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
		
		echo'<style>
			.bodywrapper {
				width: 1015px !important;
			}
			.contentwrapper {
				padding: 20px;
			}
		</style>';
	}

	private function viewMenu(){
                echo '<div class="">
                	<ul class="nav side-nav">
                		<li class="active"><strong>Home menu</strong></li>
                		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/">' . $this->core->translate("Home") . '</a></li>
                		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/studies">' . $this->core->translate("All programmes") . '</a></li>
                		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake">' . $this->core->translate("Open for intake") . '</a></li>
               			<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/register">' . $this->core->translate("Current student registration") . '</a></li>
                		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/password/recover">' . $this->core->translate("Recover lost password") . '</a></li>
                	</ul>
			<div id="page-wrapper">';
	}

	public function submitRegister() {
		include $this->core->conf['conf']['classPath'] . "students.inc.php";
		$students = new students($this->core);

		$students->registerStudent();
	}

	public function confirmRegister($item) {
		$userid = $this->core->userID;
		$boarding = $this->core->cleanPost['boarding'];

		$delivery = $this->core->cleanPost['delivery'];
		$year = $this->core->cleanPost['year'];
		$tp = $this->core->cleanPost['tp'];


		$tpy = $year;

		// DEALING WITH FULL TIME STUDENTS


		if($delivery == "Fulltime" && empty($tp) && $tpy>2){
			echo'<form id="register" name="register" method="post"  onsubmit="return validateForm()" action="'.$this->core->conf['conf']['path'].'/register/confirm/">
			<p></p><div class="label">Are you going on teaching practice this term?</div><br>
			<select name="tp" style="font-size: 15pt;">
				<option value="">SELECT ANSWER</option>
				<option value="yes">YES I AM</option>
				<option value="no">NO I AM NOT</option>
			</select>
			<input type="hidden" value="'.$year.'" name="year">
			<input type="hidden" value="'.$delivery.'" name="delivery">
			<input type="hidden" value="'.$boarding.'" name="boarding">
			<br>
			<input type="submit" class="input submit" style="font-size: 18px; font-weight: bold; color: #FFF; padding: 5px;  padding-left: 20px; padding-bottom: 10px; padding-right: 20px; border: 1px solid #000; background-color: #333"> 
	
			</form>';
		} else if($delivery == "Fulltime" && $tpy<3 ||  $delivery == "Fulltime" && $tp == "yes" ||  $delivery == "Fulltime" && $tp == "no"){
			$year = DATE(Y);


			$boardstatus = "D";
			$boarding = '';
			$sql = "SELECT * FROM `housing`,`rooms` WHERE `housing`.StudentID = '$userid' AND `housing`.RoomID = `rooms`.ID";

			$run = $this->core->database->doSelectQuery($sql);
			while ($fetch = $run->fetch_row()) {
				$boardstatus = "B";
				$boarding = 'BOARDER';
			}

			// BILLING NEW CODE
			$year = date("Y");
			$studentyear = substr($userid, 0, 4);
			$currentyear = $year-$studentyear;
			$currentyear++;
			if($currentyear > 4) { $currentyear = 4; }
			$feepackdate = date(Yn);

			$sql = "SELECT DISTINCT SUM(Amount), `fee-package`.Name 
			FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$userid'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$userid'
			WHERE `fee-package`.`Name` LIKE concat( ChargeType, '-$boardstatus-$feepackdate-$tpy')
			OR `fee-package`.`Name` LIKE 'GRZ-$boardstatus-$feepackdate-$tpy'
			ORDER BY `fee-package-charge-link`.ID DESC
			LIMIT 1";

			

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($fetch = $run->fetch_row()) {
				if ($fetch[1] != $previous) {

					$method = $fetch[4];
					if($fetch[0] != $previous & $i != 0){
						$totalx = $total;
					}

					$packagename = $fetch[1];
					$description = $fetch[2];

					$i++;
				}

				$fee = $fetch[0];
				$total =  $total + $fee;
				$previous = $fetch[1];
				$set = TRUE;
				$yeard = $fetch[1];
			}

			if($tp == 'yes'){
				$total = 600;
				$packagename  = "Fulltime TP";
				$description  = "Fulltime Teaching Practice";
				$set = TRUE;
			} else {
				$sqlp = "SELECT `periods`.ID as PID, `periods`.Name as Name  
				FROM `periods`, `basic-information` 
				WHERE `basic-information`.ID = '$userid' 
				AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
				AND `Delivery` = `StudyType`";
			}


			if($total != 0){

				echo '<div class=\"alert alert-info\" role=\"alert\">You have been billed K'.$total.' for this term.</div>';

				$description = 'Billing Tuition';

				$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`, `PackageName`) 
					VALUES (NULL, '$userid', '$total', NOW(), '$description', '$packagename');";
				$this->core->database->doInsertQuery($sql, TRUE);


				$sql = "INSERT INTO `fee-package-charge-link`
					(ID, StudentID, ChargeType, ChargedTerm) VALUES (NULL, $userid, '$yeard', '$packagename')
					ON DUPLICATE KEY UPDATE `ChargedTerm`='$packagename'";
				$this->core->database->doInsertQuery($sql);



			} else {

				echo '<div class="warningpopup">We could not bill you</div>';

			}



			$runp = $this->core->database->doSelectQuery($sqlp);
				
			if ($runp->num_rows < 1){
				echo '<span class="errorpopup">No term is currently open for full-time students</span>';
				return;
			}

			while ($row = $runp->fetch_assoc()) {
				$periodid = $row["PID"];
				$name = $row["Name"];
			}

			$sql = "SELECT * FROM `course-electives` WHERE `StudentID` = '$userid'";
			$ran = $this->core->database->doSelectQuery($sql);
				
			if ($ran->num_rows > 0) {
				$listing = TRUE;
				echo '<span class="successpopup">YOUR COURSES WERE SET FOR THIS PERIOD - '.$name.'</span>';

				$sqli = "INSERT INTO `course-electives` SELECT NULL, `StudentID`, `CourseID`, NOW(), '1', '$periodid' FROM `course-electives` WHERE `StudentID`='$userid' AND `Approved` IN ('0', '1') AND PeriodID != '$periodid';";
				$this->core->database->doInsertQuery($sqli);
			} else {
				//echo '<span class="successpopup">NO EXISTING COURSES FOUND. PLEASE REGISTER YOUR COURSES AFTER APPLYING FOR A ROOM</span>';
				$this->courseRegister();
				//return;
			}

			
			
		} else if($delivery == "Distance") {
			// DISTANCE STUDENTS
			// SET PERIOD AND UPDATE OR SAVE COURSES



			// BILLING NEW CODE
			$year = date("Y");
			$studentyear = substr($item, 0, 4);
			$currentyear = $year-$studentyear;
			$currentyear++;
			if($currentyear > 4) { $currentyear = 4; }

			$sql = "SELECT DISTINCT SUM(Amount), `fee-package`.Name 
			FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$userid'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$userid'
			WHERE `fee-package`.`Name` LIKE concat( 'DES', '-$currentyear-%')
			ORDER BY `fee-package-charge-link`.ID DESC
			LIMIT 1";

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($fetch = $run->fetch_row()) {
				if ($fetch[1] != $previous) {

					$method = $fetch[4];
					if($fetch[0] != $previous & $i != 0){
						$totalx = $total;
					}

					$packagename = $fetch[1];
					$description = $fetch[2];

					$i++;
				}

				$fee = $fetch[0];
				$total =  $total + $fee;
				$previous = $fetch[1];
				$set = TRUE;
				$yeard = $fetch[1];
			}


			if($total != 0){

				echo '<div class="alert alert-success" role="alert">You have been billed K'.$total.' for this term.</div>';

				$description = 'Billing Tuition';

				$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`, `PackageName`) 
					VALUES (NULL, '$userid', '$total', NOW(), '$description', '$packagename');";
				$this->core->database->doInsertQuery($sql, TRUE);


				$sql = "INSERT INTO `fee-package-charge-link`
					(ID, StudentID, ChargeType, ChargedTerm) VALUES (NULL, $userid, '$yeard', '$packagename')
					ON DUPLICATE KEY UPDATE `ChargedTerm`='$packagename'";
				$this->core->database->doInsertQuery($sql);



			} else {

				echo '<div class="warningpopup">We could not bill you</div>';

			}


			$no = FALSE;
			$year = DATE(Y);

			$weeks = $this->core->cleanPost['weeks'];
			$group = $this->core->cleanPost['group'];
			$intake = substr($userid, 0, 4);


			// NO FIRST YEARS FIX
			if(substr($userid, 0, 4) != DATE(Y) && $delivery != "Fulltime" || substr($userid, 1, 4) == DATE(Y) && $delivery != "Fulltime"){


				if(substr($userid,0,1) == 1){
					$masters = TRUE;
				}

				if($masters == TRUE){

					$sqlp = "SELECT `periods`.ID as PID, `periods`.Name as Name  
					FROM `periods`, `basic-information` 
					WHERE `basic-information`.ID = '$userid' 
					AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
					AND `Delivery` = `StudyType`
					AND `Intake` = 'M'";

				} else {

					$sqlp = "SELECT `periods`.ID as PID, `periods`.Name as Name  
					FROM `periods`, `basic-information` 
					WHERE `basic-information`.ID = '$userid' 
					AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
					AND `Delivery` = `StudyType`
					AND `Intake` = '$year'";

					
				}

				$runp = $this->core->database->doSelectQuery($sqlp);
					
				if ($runp->num_rows < 1){
					echo '<span class="errorpopup">No first year group this term for the first time</span>';
				}


				while ($row = $runp->fetch_assoc()) {
					$periodid = $row["PID"];
					$name = $row["Name"];
		
					// DEAL WITH COURSES FIRST TIME STUDENTS
			
					foreach($this->core->cleanPost['courses'] as $course){
						$sqx = $sqx . "INSERT INTO `course-electives` (`ID`, `StudentID`, `CourseID`, `EnrolmentDate`, `Approved`,`PeriodID`) 
						VALUES (NULL, '$userid', '$course', NOW(), '1', '$periodid'); ";
					}
				
					if ($this->core->database->mysqli->multi_query($sqx) == TRUE) {
						do {
		   
							if ($result = $this->core->database->mysqli->store_result()) {
								$result->free();
							}
	  
								if ($this->core->database->mysqli->more_results()) {
								}
						} while ($this->core->database->mysqli->next_result());

						echo '<span class="successpopup">YOUR COURSES HAVE BEEN SAVED - '.$name.'</span>';
					}

					$no = TRUE;
				}
	
			}
	
	
			if($weeks == "1" && $no != TRUE || $weeks == "2" && $no != TRUE || $weeks == "3"  && $no != TRUE || $weeks == "4"  && $no != TRUE){
				if($group == "A" || $group == "B"){
					$sqlp = "SELECT `periods`.ID as PID, `periods`.Name as Name  FROM `periods`, `basic-information` 
					WHERE `basic-information`.ID = '$userid' 
					AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
					AND `Delivery` = `StudyType`
					AND `Weeks` = '$weeks'
					AND `Intake` != '$intake'
					LIMIT 1";
				}else{
					$userid = substr($userid,1,8);
					$sqlp = "SELECT `periods`.ID as PID, `periods`.Name as Name  FROM `periods`, `basic-information` 
					WHERE `basic-information`.ID = '1$userid' 
					AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
					AND `Delivery` = `StudyType`
					AND `Weeks` = '$weeks'
					AND `Intake` = 'M'
					LIMIT 1";
				}
			
	
				$runp = $this->core->database->doSelectQuery($sqlp);
				
				if ($runp->num_rows < 1){
					echo '<span class="errorpopup">No matching period for you CONTACT ICT</span>';
					$no = TRUE;
					return;
				}
			

				while ($row = $runp->fetch_assoc()) {
					$periodid = $row["PID"];
					$name = $row["Name"];
					$listing = FALSE;


					$_SESSION['period'] = $periodid;
					$_SESSION['weeks'] = $weeks;
					$_SESSION['group'] = $group;

					if($boarding == "YES"){	
						$_SESSION['boarding'] = TRUE;
					}

					// UPDATE COURSES FOR RECURRING STUDENTS

					$sql = "SELECT * FROM `course-electives` WHERE `StudentID` = '$userid'";
					$ran = $this->core->database->doSelectQuery($sql);
				
					if ($ran->num_rows > 0) {
						$listing = TRUE;
						echo '<span class="successpopup">YOUR COURSES WERE SET FOR THIS PERIOD - '.$name.'</span>';

						$sqli = "INSERT INTO `course-electives` SELECT NULL, `StudentID`, `CourseID`, NOW(), '1', '$periodid' FROM `course-electives` WHERE `StudentID`='$userid' AND `PeriodID` != '$periodid';";
						$this->core->database->doInsertQuery($sqli);
					} else {
						echo '<span class="successpopup">NO EXISTING COURSES FOUND. PLEASE REGISTER</span>';
						//$this->courseRegister();
						//return;
					}


				}

			}

		}

		if($boarding == "YES"){	
			$_SESSION['weeks'] = $weeks;
			$_SESSION['period'] = $periodid;
			$_SESSION['group'] = $group;

			if($boarding == "YES"){	
				$_SESSION['boarding'] = TRUE;
			}


			include $this->core->conf['conf']['viewPath'] . "accommodation.view.php";
			$housing = new accommodation();
			$housing->buildView($this->core);
			$housing->applyAccommodation();
		} else {

			$uid = $this->core->userID;
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$balance = $payments->getBalance($uid);

			if($balance<=0){
				echo '<span class="successpopup">
					You are all done! Click below to download and PRINT your confirmation slip.<br>
					If you have printed this document you will not need to pass through accounts or the lab.<br>
					YOU HAVE FINISHED REGISTERING
				      </span>';

				echo'<div class="toolbar">'.
					'<a style="width: 100%; font-size: 14pt; height: 40pt; margin-left: -15pt; width: 530pt; background-color: #000;" href="' . $this->core->conf['conf']['path'] . '/confirmation/print/'.$item.'">Click here to print confirmation slip</a>
				</div>';
			} else {
				echo '<span class="warningpopup">Please settle your balance of K'.$balance.' please log in again after depositing the money through ZANACO billmuster and click "PAYMENTS" in the menu. If your balance is 0 or below you can then press on the button "PRINT CONFIRMATION SLIP" which will allow you to come to campus without doing any registration. </span>';
			}

			$sql = "INSERT INTO `reporting` (`ID`, `StudentID`, `DateTime`, `PeriodID`) VALUES (NULL, '$uid', NOW(), '$periodid');";
			$this->core->database->doInsertQuery($sql);
			

		}
	} 


	// REGISTRATION FOR RETURNING STUDENTS (REPORTING)

	public function returningRegister($item){

		$studentID =  $this->core->userID;

		$sql = "SELECT * FROM `reporting` WHERE `StudentID` = '$studentID'";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0){
			echo '<span class="warningpopup">You have already reported for this term. If anything is incorrect please report to ICT.</span>';
			//echo '<span class="successpopup">If you forgot to apply for a room <a href="'.$this->core->conf['conf']['path'].'/accommodation/apply">click here</a></span>';

			$uid = $this->core->userID;
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$balance = $payments->getBalance($uid);

			if($balance<=0){
				echo '<span class="successpopup">
					You are all done! Click below to download and PRINT your confirmation slip.<br>
					If you have printed this document you will not need to pass through accounts or the lab.<br>
					YOU HAVE FINISHED REGISTERING
				      </span>';

				echo'<div class="toolbar">'.
					'<a style="width: 100%; font-size: 14pt; height: 40pt; margin-left: -15pt; width: 530pt; background-color: #000;" href="' . $this->core->conf['conf']['path'] . '/confirmation/print/'.$item.'">Click here to print confirmation slip</a>
				</div>';
			} else {
				echo '<span class="warningpopup">Please settle your balance of K'.$balance.' please log in again after depositing the money through ZANACO billmuster and click "PAYMENTS" in the menu. If your balance is 0 or below you can then press on the button "PRINT CONFIRMATION SLIP" which will allow you to come to campus without doing any registration. </span>';
			}

			return;
		} 




		echo'<script>
			function validateForm() {
			    var x = document.forms["register"]["boarding"].value;
			    var y = document.forms["register"]["group"].value;
			    var z = document.forms["register"]["weeks"].value;
			    if (x == "" || y == "" || z == "") {
			        alert("PLEASE MAKE SURE YOU SELECT AN OPTION FOR EACH QUESTION");
			        return false;
			    }
			}
		</script>';

		echo'<form id="register" name="register" method="post"  onsubmit="return validateForm()" action="'.$this->core->conf['conf']['path'].'/register/confirm/">';

//		if(substr($studentID, 0, 4) == DATE(Y) || substr($studentID, 1, 4) == DATE(Y)){
		if($studentID == FALSE){
			$period = '42';
				
			if(substr($studentID, 0, 1) == 1){
				$masters = TRUE;
			}

			echo'<div class="heading">Please confirm your courses for this period are correct</div>';

			$syear = substr($studentID, 1, 4);
			$cyear = date("Y");
			$year =  $cyear - $syear;

			$year = 1;

			if($masters == TRUE){
				$sql = "SELECT DISTINCT `student-program-link`.Major as Major, `student-program-link`.Minor as Minor, `courses`.ID as CID, `programmes`.ProgramName, `programmes`.ID as ProgramID, `courses`.Name as CNAME, `courses`.CourseDescription as CDESC, `programmes`.ProgramType
				FROM `basic-information` as bi, `programmes-link`, `programmes`, `program-course-link`, `courses`, `student-program-link`
				WHERE `bi`.ID = '$studentID'
				AND `student-program-link`.StudentID = '$studentID'
				AND `programmes`.ID = (SELECT `student-program-link`.Major FROM `student-program-link` WHERE `student-program-link`.StudentID = '$studentID' ORDER BY ID DESC LIMIT 1)
				AND `program-course-link`.ProgramID = `programmes`.ID 
				AND `program-course-link`.CourseID = `courses`.ID 
				AND `courses`.Name LIKE '%'";
			}else{
				$sql = "SELECT DISTINCT `student-program-link`.Major as Major, `student-program-link`.Minor as Minor, `courses`.ID as CID, `programmes`.ProgramName, `programmes`.ID as ProgramID, `courses`.Name as CNAME, `courses`.CourseDescription as CDESC, `programmes`.ProgramType
				FROM `basic-information` as bi, `programmes-link`, `programmes`, `program-course-link`, `courses`, `student-program-link`
				WHERE `bi`.ID = '$studentID'
				AND `student-program-link`.StudentID = '$studentID'
				AND `programmes`.ID = (SELECT `student-program-link`.Major FROM `student-program-link` WHERE `student-program-link`.StudentID = '$studentID' ORDER BY ID DESC LIMIT 1)
				AND `program-course-link`.ProgramID = `programmes`.ID 
				AND `program-course-link`.CourseID = `courses`.ID 
				AND `courses`.Name LIKE '% $year%'
				OR `bi`.ID = '$studentID'
				AND `student-program-link`.StudentID = '$studentID'
				AND `programmes`.ID = (SELECT `student-program-link`.Minor FROM `student-program-link` WHERE `student-program-link`.StudentID = '$studentID' ORDER BY ID DESC  LIMIT 1)
				AND `program-course-link`.ProgramID = `programmes`.ID 
				AND `program-course-link`.CourseID = `courses`.ID 
				AND `courses`.Name LIKE '% $year%'
				OR `bi`.ID = '$studentID'
				AND `student-program-link`.StudentID = '$studentID'
				AND `programmes`.ProgramType = 4
				AND `program-course-link`.ProgramID = `programmes`.ID 
				AND `program-course-link`.CourseID = `courses`.ID 
				AND `courses`.Name LIKE '% $year%'
				ORDER BY ProgramID ASC";
			}


			$run = $this->core->database->doSelectQuery($sql);

			$arts = array(18,4,7,5,6,11,12,8,2);
			$science = array(2,9,10);

			while ($fetch = $run->fetch_assoc()){

				$name = $fetch['CNAME'];
				$cdesc = $fetch['CDESC'];
				$cid = $fetch['CID'];

				echo '<li><b>'. $name . ' - '.$cdesc.'</b></li>';

				echo'<input type="hidden" name="courses[]" value="'.$cid.'">';
			}


			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$studentID'";

			$run = $this->core->database->doSelectQuery($sql);
			while ($fetch = $run->fetch_assoc()){
				$delivery = $fetch['StudyType'];
			}


			if($delivery == "Distance"){
				echo'<div class="successpopup">YOU ARE REGISTERING AS A RETURNING DISTANCE EDUCATION STUDENT</div>';
			
				echo'<div class="heading">Will you be in boarding?</div>
				<div class="label">Will you need boarding? </div>
				<select name="boarding">
					<option value="">SELECT HERE</option>
					<option value="YES">YES</option>
					<option value="NO">NO</optiom>
				</select>
				<input type="hidden" name="weeks" value="2">';
			}else{
				echo'<div class="successpopup">YOU ARE REGISTERING AS A RETURNING FULL-TIME STUDENT</div>';
				echo'<div class="heading">Will you be in boarding?</div>
				<div class="label">Will you need boarding? </div>
				<select name="boarding">
					<option value="">SELECT HERE</option>
					<option value="YES">YES</option>
					<option value="NO">NO</optiom>
				</select>';
			}
		} else {


			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$studentID'";

			$run = $this->core->database->doSelectQuery($sql);
			while ($fetch = $run->fetch_assoc()){

				$delivery = $fetch['StudyType'];
				echo'<input type="hidden" value="'.$delivery.'" name="delivery">';

			}

			if($delivery == "Distance"){

				echo'<div class="successpopup">YOU ARE REGISTERING AS A RETURNING DISTANCE EDUCATION STUDENT</div>';
				echo'<div class="heading">How many weeks are you staying</div>
				<div class="label">For how long? </div>
				<select name="weeks">
					<option value="">SELECT NUMBER OF WEEKS HERE!</option>
					<option value="2">2 weeks</option>
					<option value="3">3 weeks</option>
					<option value="4">4 weeks</option>
				</select>

				<div class="heading">Will you be in boarding?</div>
				<div class="label">Boarding? </div>
				<select name="boarding">
					<option value="">SELECT HERE</option>
					<option value="YES">YES</option>
					<option value="NO">NO</optiom>
				</select>

				<div class="heading">GROUP?</div>
				<div class="label">Group? </div>
				<select name="group">
					<option value="">SELECT HERE</option>
					<option value="A">GROUP A</option>
					<option value="B">GROUP B</optiom>
				</select>';
			} else {


			$sql = "SELECT DISTINCT `courses`.`CourseDescription`, `courses`.Name, `periods`.`Year`, `periods`.`Semester`, `course-electives`.`CourseID`, `PeriodID`
			FROM `course-electives`
			LEFT JOIN `periods` ON `course-electives`.`PeriodID` = `periods`.ID
			LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.ID 
			WHERE `course-electives`.StudentID  = '$studentID' 
			AND `course-electives`.Approved IN (1)
			ORDER BY `course-electives`.PeriodID DESC";



				$set = TRUE;
	
				$run = $this->core->database->doSelectQuery($sql);
				while ($fetch = $run->fetch_assoc()){

					$name = $fetch['Name'];
					$cdesc = $fetch['CourseDescription'];
					$cid = $fetch['CourseID'];
					$periods = $fetch['PeriodID'];
					if($period == $periods || $period ==""){
						echo '<li><b>'. $name . ' - '.$cdesc.'</b></li>';
	
						echo'<input type="hidden" name="courses[]" value="'.$cid.'">'; 
						$set = TRUE;
						$period = $periods;
					}
					

				}

				if($set == FALSE){
					echo'<div class="warningpopup">YOU MUST FIRST REGISTER COURSES</div>';
				}
				

				echo'<div class="heading">You are in year</div>
				<div class="label">What year are you in?</div>
				<select name="year">
					<option value="1">1st Year</option>
					<option value="2">2nd Year</option>
					<option value="3">3rd Year</option>
					<option value="4">4th Year</option>
				</select>';


			
	
				echo'<div class="successpopup">YOU ARE REGISTERING AS A RETURNING FULL-TIME STUDENT</div>';
				
			}


		}

		echo'<div class="heading">Is all this information correct?</div>
		</p><p>
		
		<div style="appearance: button; font-size: 18px; height: 35px; font-weight: bold; padding: 5px; padding-left: 20px; padding-right: 20px; padding-bottom: 10px; border: 1px solid #000; background-color: #e81f1f; width: 100px;float: left; color: #FFFFFF;" class="input"> 
		<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true">
		<a href="' . $this->core->conf['conf']['path'] . '/register/course" style="font-size: 18px; font-weight: bold; float:right; padding-left: 10px; color: #000;"> NO</a></div> 
		
		<button onclick="onclick="this.form.submit();" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px;  padding-left: 20px; padding-bottom: 10px; padding-right: 20px; border: 1px solid #000; background-color: #39c541"> <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"> YES</button>
		</p>
		</form>';
	

	}


	public function groupbillRegister(){
	

		echo "Starting batch";

		$input = "20131072,
			  20131547";


	$document = explode("\n", $input);

	foreach($document as $line){

		$linearray = str_getcsv($line,',','"');

		$studentid = $linearray[0];

		echo "BILLINGSTUDENT $studentid <br>";

		$this->billStudent($studentid);
		}

	}

	public function previewRegister($item){
		$this->showbillStudent($item);
	}

	public function showbillStudent($item){

		$sql = "SELECT `StudyType`, `YearOfStudy` 
			FROM `student-data-other`, `basic-information` 
			WHERE `StudentID` LIKE '$item' 
			AND `basic-information`.ID = '$item' 
			ORDER BY `student-data-other`.`ID` DESC LIMIT 1";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$delivery = $row[0];
			$studentyear = $row[1];
		}


		if($delivery == "Fulltime"){
			$newbilled = "20163";
			$lastbilled = "20162";
		}else if($delivery == "Distance"){
			$newbilled = "201613";
			$lastbilled = "20162";
		}

		$sql = "SELECT * FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID  
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$item'
			WHERE `fee-package`.`Name` = `fee-package-charge-link`.ChargeType
			ORDER BY `fee-package-charge-link`.ID DESC";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		$total = 0;
		$totalx = 0;
		$set = FALSE;

		while ($fetch = $run->fetch_row()) {
			
			if ($fetch[1] != $previous) {

				$method = $fetch[4];

				if($fetch[0] != $previous & $i != 0){
					echo '</table></div><div>';
					$totalx = $total;
				}

				$description = $fetch[2];

				echo '<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;"><h2>Fee Package</h2><br>
				<table width="768" border="0" cellpadding="5" cellspacing="0">
				<tr>
				<td width="205"><strong>Package description</strong></td>
				<td><b>' . $fetch[2] . ' </b></td>
				</tr>
				</table>';

				$i++;

				echo '<br /><table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr>
				<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
				<td width="200" bgcolor="#EEEEEE">Description</td>
				<td bgcolor="#EEEEEE" >Cost</td>
				</tr>'; 
			}


			echo'<tr>
			<td><strong>' . $fetch[7] . '</strong></td>
			<td>' . $fetch[8] . ' </td>
			<td><b>' . $fetch[9] . ' KR</b></td>
			</tr>';

	
			$total =  $total + $fetch[9];
			
			$previous = $fetch[1];
			$set = TRUE;
		}


		// ADD UP HERE WITH JS

		echo'<tr>
			<td><strong>Course fees</strong></td>
			<td>By selected credits</td>
			<td><b><span id="totalcost">0</span> KR</b></td>
			</tr>';





		if($set == TRUE && $totalx != 0){
			echo'</table></div></div>';
		}
		if($set == TRUE && $totalx == 0){
			echo '</table></div>';
			$totalx = $total;
		}

		$totaldouble = $total-$totalx;

		$total = $totalx;



		echo'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;"><br />
		<h2>Total Due Fees</h2><br><table width="750" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees due</strong></td>
			<td width="200px" bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><h2><span id="totalfees">' . $total . '</span> KR</h2></td>

		  </tr>
		</table></div><br>';

	}

	private function approvalRegister($item) {

		$sql = "SELECT DISTINCT `courses`.ID,`course-electives`.ID as CEID, FirstName, MiddleName, Sex, GovernmentID, `courses`.Name, CourseDescription, `course-electives`.StudentID
			FROM `course-electives`, `courses`, `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Requesting' 
			AND `basic-information`.ID = $item
			AND `course-electives`.StudentID = `basic-information`.ID
			AND `courses`.ID = `course-electives`.CourseID";

		$run = $this->core->database->doSelectQuery($sql);
		$i = 1;

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/register/course/delete/all">Delete all courses and register again</a></div>';

		echo '<p class="title">Registered courses</p>';

		echo '<table id="active" class="table table-bordered  table-hover">
				<thead>
					<tr>
						<th bgcolor="#EEEEEE" width="30px" data-sort"string"><b> #</b></th>
						<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Course name</b></th>
						<th bgcolor="#EEEEEE"><b> <b>Course code</b></th>
						<th bgcolor="#EEEEEE" width="100px"><b> Credits</b></th>
						<th bgcolor="#EEEEEE" width="150px"><b> Options</b></th>
					</tr>
				</thead>
			<tbody>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$study = $fetch['Name'];
			$firstname = $fetch['FirstName'];
			$middlename = $fetch['MiddleName'];
			$surname = $fetch['Surname'];
			$sex = $fetch['Sex'];
			$uid = $fetch['StudentID'];
			$nrc = $fetch['GovernmentID'];
			$course = $fetch['Name'];
			$gradeno = $fetch['GradeNo'];
			$cid = $fetch['ID'];
			$description = $fetch['CourseDescription'];
			$credits = $fetch['CourseCredit'];
			$approved = $fetch['Approved'];
			$apid = $fetch['CEID'];

			if ($approved == 0) {
				$class = 'class="info"';
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/register/course/delete/' . $apid .'?userid='.$uid.'"> <b>Remove </b></a>';
			} elseif ($approved == 1){
				$class = 'class="success"';
				$next = '<b>Locked</b>';
			} elseif ($approved == 2){
				$class = 'class="danger"';
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/register/course/delete/' . $apid .'?userid='.$uid.'"> <b>Remove</b></a>';
			}

			echo '<tr '.$class.'>
				<td>'.$i.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/course/show/' . $cid . '"><b>' . $description .'</b></a>  </td>
				<td>' . $uid . '</td>
				<td><b>' . $credits . '</b></td>
				<td>'.$next.'</td>
				</tr>';


			$i++;
			$total = $total+$credits;


		}

		echo '<tr class="warning">
			<td></td>
			<td colspan="2"><b>Total number of credits</b></td>

			<td colspan="2"><b>' . $total . '</b></td>
		
			</tr>';
		echo '</tbody>
		</table>';

	}



	public function billStudent($item){

		$sql = "SELECT `StudyType`, `YearOfStudy` 
			FROM `student-data-other`, `basic-information` 
			WHERE `StudentID` LIKE '$item' 
			AND `basic-information`.ID = '$item' 
			ORDER BY `student-data-other`.`ID` DESC LIMIT 1";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$delivery = $row[0];
			$studentyear = $row[1];
		}


		if($delivery == "Fulltime"){
			$newbilled = "20169";
			$lastbilled = "20165";
		}else if($delivery == "Distance"){
			$newbilled = "201612";
			$lastbilled = "20168";
		}

		$sql = "SELECT * FROM `fee-package`
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID  
			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `fee-package-charge-link` ON `fee-package-charge-link`.StudentID = '$item'
			WHERE `fee-package`.`Name` = concat( ChargeType, '-$newbilled-$studentyear')
			OR `fee-package`.`Name` = 'DES-$newbilled-$studentyear' AND `basic-information`.StudyType = 'Distance'
			ORDER BY `fee-package-charge-link`.ID DESC";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		$total = 0;
		$totalx = 0;
		$set = FALSE;

		while ($fetch = $run->fetch_row()) {
			
			if ($fetch[1] != $previous) {

				$method = $fetch[4];

				if($fetch[0] != $previous & $i != 0){
					echo '</table></div><div>';
					$totalx = $total;
				}

				$description = $fetch[2];

				echo '<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;"><h2>Fee Package</h2><br>
				<table width="768" border="0" cellpadding="5" cellspacing="0">
				<tr>
				<td width="200px"><strong>Fee package</strong></td>
				<td><b>' . $fetch[1] . '</b></td>
				<td></td>
				</tr>
				<tr>
				<td><strong>Package description</strong></td>
				<td><b>' . $fetch[2] . ' </b></td>
				</tr>
				</table>';

				$i++;

				echo '<br /><table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr>
				<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
				<td width="200" bgcolor="#EEEEEE">Description</td>
				<td bgcolor="#EEEEEE" >Cost</td>
				</tr>'; 
			}


			echo'<tr>
			<td><strong>' . $fetch[7] . '</strong></td>
			<td>' . $fetch[8] . ' </td>
			<td><b>' . $fetch[9] . ' KR</b></td>
			</tr>';

	
			$total =  $total + $fetch[9];
			
			$previous = $fetch[1];
			$set = TRUE;
		}

		if($set == TRUE && $totalx != 0){
			echo'</table></div></div>';
		}
		if($set == TRUE && $totalx == 0){
			echo '</table></div>';
			$totalx = $total;
		}

		$totaldouble = $total-$totalx;

		$total = $totalx;


		echo'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;"><br />
		<h2>Total Due Fees</h2><br><table width="750" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees due</strong></td>
			<td width="200px" bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><h2>' . $total . '</h2></td>

		  </tr>
		</table></div><br>';


		// UPDATE THE BALANCE WITH LATEST FEE PACKAGE
		$sql = "SELECT * FROM `fee-package-charge-link` WHERE `StudentID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			$billed = $fetch[3];
			//echo "<br>Last actual billing was $billed new is $newbilled and correct last is billing is $lastbilled<br>";
		}
		    
		if($billed == $newbilled){
			echo "YOU WERE BILLED ALREADY <br>";
		} else if($billed == $lastbilled){  

			$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`) VALUES (NULL, '$item', '$total', NOW(), '$description');";
			$this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `balances` SET `Amount`=`Amount`+'$total', `LastUpdate`=NOW() WHERE `StudentID` = '$item'";
			$this->core->database->doInsertQuery($sql);

			$sql = "INSERT INTO `fee-package-charge-link`
				(ID, StudentID, ChargeType, ChargedTerm) VALUES (NULL, $item, '$yeard', $newbilled)
				ON DUPLICATE KEY UPDATE `ChargedTerm`='$newbilled'";

			$this->core->database->doInsertQuery($sql);
			
			echo '<span class="successpopup">Updated balance to include new bill of '.$total.'</span>';
		} else {
			echo '<span class="errorpopup">NO BILLING TOOK PLACE, PLEASE INFORM ACCOUNTS</span>';
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
			$semester ="2";
		}else if($date_month <=6){
			$year = $date_year; 
			$semester = "1";
		}

		$sql = "SELECT * FROM `periods` WHERE `Year` = '$year' AND `Semester` = '$semester'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
		}
		return $item;
	}

	private function getDates(){
		$d1=new DateTime("NOW");
		$data_now= (int)$d1->format("Y");
		$date_year = (int)$d1->format("Y");
		$date_month = (int)$d1->format("m");
	
		//$p_year=$date_year;
		//$m_year=$date_year-1;
		$_academicyear1=""; 
		$_semester1=""; 
		if($date_month >=7){
			$dates['academicyear'] = $date_year; 
			$dates['semester'] = "2";
		}else if($date_month <=6){
			$dates['academicyear'] = $date_year; 
			$dates['semester'] = "1";
		}

		return $dates;
	}

	public function getBillSage($item, $total, $confirm) {
		$userid = $item;

		$periodid = $this->getPeriod();
		$sql = "SELECT * FROM `reporting` WHERE `PeriodID` = '$periodid' AND `StudentID` = '$userid'";
		$run = $this->core->database->doSelectQuery($sql);

		
		while ($fetch = $run->fetch_assoc()) {
			// ALREADY BILLED
				// return FALSE;
		}

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
		(SELECT count(DISTINCT CourseID) FROM `course-electives` WHERE StudentID = a.StudentID
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


			if($total != '' && $total != 0){
				$courses = $total;
			}
			if($leveldefinition == 'MS'){
				$courses = 5;
			}
			
			if($code == 40300 || $schoolcode == 204){
				$courses = 5;
			}				

			
			if($confirm == "confirm"){
				$url = "http://41.63.16.6:8081/AccPackService/studentbillsage?studentID=$userid&levelDefinition=$leveldefinition&Code=$code&modeOfStudy=$mode&name=$name&year=$year&semester=$semester&numCourses=$courses&school=$school&schoolCode=$schoolcode&accommodation=No&catering=No";
			}else{
				$url = "http://41.63.16.6:8081/AccPackService/studentbillget?studentID=$userid&levelDefinition=$leveldefinition&Code=$code&modeOfStudy=$mode&name=$name&year=$year&semester=$semester&numCourses=$courses&school=$school&schoolCode=$schoolcode&accommodation=No&catering=No";
	
				
			}

	
			$output = file_get_contents($url);

			if($output == "Success"){
				return TRUE;
			}
		}
	}

	public function newRegister($item){
			// REGISTRATION FOR NEW STUDENTS MU

			$userid = $this->core->userID;
			if($item != '' && $this->core->role >= 100){
				$userid = $item;
			}
	
			$periodid = $this->getPeriod();

			$sql = "SELECT * FROM `basic-information` WHERE ID = '$userid' AND `Status` = 'New'";
			$run = $this->core->database->doSelectQuery($sql);
			
							
			include $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($item);
			
			$sqlpayroll = "SELECT * FROM `ac_payroll` WHERE student_id = '$userid' ";
			$runpayroll = $this->core->database->doSelectQuery($sqlpayroll);
			
			//Check payment before registration AND if exempted Via Payroll OR Government Loan
			//if(($actual >= 0) || ($runpayroll->num_rows == 0)){
			//	echo '<div class="errorpopup" style="border: 2px solid #000; padding: 15px; background-color: #e8c5c5;">YOU HAVE NOT MADE APPROPRIATE PAYMENTS AND MAY NOT REGISTER. <br>FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
			//	return;
			//}else{
				
				
				if ($run->num_rows != 0){

					$owner = $this->core->userID;
					$name = $owner . "-conf-" .date('Y-m-d').'.html';
					$path = "datastore/output/confirmations/";
					$filename = $path. $name;

					include $this->core->conf['conf']['classPath'] . "security.inc.php";
					$security = new security();
					$security->buildView($this->core);


					if(empty($item)){
						$item = $this->core->userID;
						$studentID = $item;
						$studentNo = $item;
					}

					$start = substr($studentID, 0, 4);


					$sql = "SELECT Firstname, MiddleName, Surname, `basic-information`.Status, Sex, `basic-information`.Status, `basic-information`.StudyType, `study`.Name
						FROM `basic-information`
						LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
						LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
						WHERE `basic-information`.ID = '$item'";
				
				
					$run = $this->core->database->doSelectQuery($sql);

					while ($fetch = $run->fetch_row()) {
						$firstname = $fetch[0] . " " .  $fetch[2];
						$status  = $fetch[5];
						$delivery  = $fetch[6];
						$program  = $fetch[7];
		
						$today =  date("Y-m-d");
						$admin = $this->core->userID;

						$name = $security->qrSecurity($name, $owner, $actual, $firstname);

						$output .='<div style="position: absolute; right: 0px; font-size: 7pt; text-align: center; float:right; ">
						<img src="/datastore/output/secure/'.$name.'.png"><br>'.$name.'
						</div>
						<center><div style="width: 200px;"><a href="'. $this->core->conf['conf']['path'] .'"><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /></a></div>
						<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">REGISTRATION CONFIRMATION SLIP </div></div>
						<div style=" font-size: 12px;">PRINTED BY ' . $admin . ' ON '. $today .'</div>
						</center><hr>';


						$output .='<h2>REGISTRATION STATEMENT</h2>';
						$output .="<span style=\"font-size: 12pt; color: #333;\">This confirmation slip is printed for <b>$firstname $lastname (<u>$item</u>)</b> a <b>$delivery student</b> ($status).<br><br>";

						$output .=' You are in the program: <b>'.$program.'</b>';
					
					}

				

					// SET PASSWORD
					$password = $this->generatePassword(5);
					$hash = $this->hashPassword($userid, $password);
					$sql = "INSERT INTO `access` (`ID`, `RoleID`, `Username`, `Password`) VALUES ($userid, '10', $userid, '$hash') 
						ON DUPLICATE KEY UPDATE `Password` = '$hash'";
			
					$this->core->database->doInsertQuery($sql);

					$output .='<h2>HOW TO ACCESS YOUR PROFILE</h2>';
					$output .='<p>To update your information visit the university information system: <b>https://edurole.mu.ac.zm/</b> and login with:</p>';
					$output .='<b>USERNAME: '.$userid.'</b><br>';
					$output .='<b>PASSWORD: '.$password.'</b>';

					$sql = "SELECT DISTINCT `student-study-link`.StudyID as STUDY, `courses`.ID as CID, `study`.Name, `study`.ID as SID, `courses`.Name as CNAME, `courses`.CourseDescription as CDESC
					FROM `basic-information` as bi, `course-year-link`, `courses`, `student-study-link`, `study`
					WHERE `bi`.ID = '$userid'
					AND `student-study-link`.StudentID = `bi`.ID 
					AND `student-study-link`.StudyID =  `course-year-link`.StudyID 
					AND `course-year-link`.StudyID = `study`.ID 
					AND `course-year-link`.CourseID = `courses`.ID 
					AND `course-year-link`.`Year` = '1'
					AND `course-year-link`.Semester = '1'
					AND `courses`.Name LIKE '%'";

					$run = $this->core->database->doSelectQuery($sql);

					$output .='<p></p><h2>YOUR REGISTERED COURSES</h2>';

					while ($fetch = $run->fetch_assoc()){
		
						$name = $fetch['CNAME'];
						$cdesc = $fetch['CDESC'];
						$course = $fetch['CID'];
		
						$output .='<li><b>'. $name . ' - '.$cdesc.'</b></li>';

						$sqx = "INSERT INTO `course-electives` (`ID`, `StudentID`, `CourseID`, `EnrolmentDate`, `Approved`, `PeriodID`) 
							VALUES (NULL, '$userid', '$course', NOW(), '1', '$periodid'); ";

						$this->core->database->doInsertQuery($sqx);
			
					}

			
					$output .='<p></p><h2>YOUR FINANCIAL STATUS</h2>';


					//$bill = $this->getBillSage($userid, 5, FALSE);
					//$billsage = $this->getBillSage($userid, 5, "confirm");

					$sql = "SELECT *, `billing-details-temp`.Amount as AMT
						FROM `billing-temp`, `billing-details-temp`
						WHERE `billing-details-temp`.BillingID = `billing-temp`.ID
						AND `StudentID` = '$userid'
						AND `PeriodID` = '$periodid'";
			

					$run = $this->core->database->doSelectQuery($sql);
				
					$output .='<table border="0" cellpadding="3" cellspacing="0" class="table table-bordered table-striped table-hover">';

					$total = 0;

					while ($row = $run->fetch_assoc()) {
						$amount = $row['AMT'];
						$name = $row['Desc'];
						$total = $total + $amount;
					}

					$output .='<tr><td><b>BILLED AMOUNT</b></td>
						<td><b>K'.$total.'</b></td>
						</tr></table>';

					$actual = $actual+$total;

					$output .='Our financial records indicate your new balance will become: <b> '.$actual.' ZMW </b>';
					$output .='<hr>PRINTED BY ' . $admin . ' ON '. $today;
					$output .='<br><b>PLEASE KEEP THIS DOCUMENT FOR FUTURE REFERENCE</b>';

					// ADD TO REPORTED
					$sql = "INSERT INTO `reporting` (`ID`, `StudentID`, `DateTime`, `PeriodID`) VALUES (NULL, '$userid', NOW(), '$periodid');";
					$this->core->database->doInsertQuery($sql);

					// APPROVE STATUS
					$sql = "UPDATE `basic-information` SET `Status` = 'Approved' WHERE `ID` = '$userid'";
					$this->core->database->doInsertQuery($sql);
				

					file_put_contents($filename, $output);


					$output .='<script type="text/javascript">
						window.print();
					</script>';


					echo $output;

					return;

				}
			//}
	}

	public function courseRegister($item) {

		$userid = $this->core->userID;
		if($item != '' && $this->core->role >= 100){
			$userid = $item;
		}

		if($this->core->cleanGet['userid'] != ''){
			$userid = $this->core->cleanGet['userid'];
		}


			// PAYMENT VERIFICATION FOR GRADES
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($userid);

		

		$periodid = $this->getPeriod();
		

		$sql = "SELECT * FROM `basic-information`, `study`, `student-study-link` 
			WHERE `basic-information`.ID = '$userid' 
			AND `basic-information`.`Status` IN ('Approved', 'New')
			AND `student-study-link`.StudentID = `basic-information`.ID
			AND `student-study-link`.StudyID = `study`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0){
			echo'<div class="alert alert-warning" role="alert">You cannot register your courses yet as you have no study set, please see your division coordinator to have that fixed!</div>';
			return FALSE;
		}
		
		$studyID = '';
		while ($fetch = $run->fetch_assoc()) {
			$studyID= $fetch['StudyID'];
			$studytype = $fetch['StudyType'];
		}
		
		$sql_exl = "SELECT * FROM `comments` WHERE StudentID = '$userid' 
			AND StudyID='$studyID' AND Comment='EXCLUDE' ";

		$run_exl = $this->core->database->doSelectQuery($sql_exl);

		if ($run_exl->num_rows > 0){
			echo'<div class="alert alert-warning" role="alert">You cannot register your courses because you are currently on <b>Exclude</b>, please see your school coordinator/ Academic office for advice!</div>';
			if ($this->core->role == 1000 || $this->core->role == 104 ) {
				echo'<div class="alert alert-warning" role="alert"><a href="' . $this->core->conf['conf']['path'] . '/register/removeexclude/' . $userid . '"><b>Click here to remove EXCLUDE status</b></a></div>';
			}
			return FALSE;
		}
		
		
		
		$add = FALSE;

		if($item == "submit"){
			$periodid = $this->getPeriod();

		//Late registration 	
		$sqlLT = "SELECT * FROM `periods` WHERE ID = '$periodid'";

		$runLT = $this->core->database->doSelectQuery($sqlLT);
		if($this->core->role == 104 ||  $this->core->role == 1000){
			
			echo '<div class="alert alert-danger" role="alert">Registration is closed but you have access</div>'; 
		
		} else {
			while ($fetch = $runLT->fetch_assoc()){
				if($fetch['CourseRegEndDate'] < date('Y-m-d')){
					echo'<div class="alert alert-warning" role="alert">Course Registration for current semester is closed!</div>';
					return FALSE;
				}	
			}
			
		}
		//End of late registration

			
			
			/***************temporary fix to sort out wrong registration**************/
			$sql_payroll_exempt= "SELECT * FROM ac_registration_exemption WHERE student_id = '$userid'";
			//$sql_payroll_exempt= "SELECT * FROM ac_payroll WHERE student_id = '$userid'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
			
				//echo'<div class="warningpopup">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				//return FALSE;
				if($actual > 100){
					echo '<div class="alert alert-danger" role="alert">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE <b>K'.number_format($actual).'</b> BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
					return;
				}
			} else {
				
				echo'<div class="alert alert-warning" role="alert">Payment exempted</div>';
				
			}
			/*----part of the temporary fix to be removed once registration closes-----
			if($actual > 100){
				echo '<div class="errorpopup">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
				return;
			}
			*/
			$courses = $this->core->cleanPost['courses'];

			$_SESSION['courses'] = $courses;

			$cc = 0;
			foreach($courses as $course){
				$out = $out .  $course . ',';
				$cc++;
			}

			if($cc > 8 && $this->core->role != 104){
				echo'<div class="alert alert-danger" role="alert">You have selected more than 8 courses. Please see division HoD to register more than 8 courses.</div>';
				return;
			}

			/*
			if($studytype == 5){
				$cc = 5;
			}
			*/

			$out = rtrim($out,',');

			$sql = "select `courses`.ID, `courses`.Name, `courses`.CourseDescription from `courses` WHERE `courses`.ID IN ($out)";
			$run = $this->core->database->doSelectQuery($sql);

			echo '<form id="coursessave" name="coursessave" method="post" action="'. $this->core->conf['conf']['path'] . '/register/course/save?userid='.$userid.'">';
			echo '<p class="title"> You have selected the following courses: </p>';
			
			while ($fetchs = $run->fetch_assoc()) {
				echo '<ol start="1" style="color: #000; font-size: 12px;"><b>' . $fetchs['Name'] . '</b> - ' . $fetchs['CourseDescription'] . '</ol>';
			
			}

			//$output  =  $this->getBillSage($item, $cc, FALSE);
			
		/*
			$sql = "SELECT *, `billing-details-temp`.Amount as AMT FROM `billing-temp`, `billing-details-temp`
				WHERE `billing-details-temp`.BillingID = `billing-temp`.ID AND `StudentID` = '$userid'
				AND `PeriodID` = '$periodid'"; */
		/*		
			$sql = "SELECT `courses`.ID, `courses`.Name,IF(`courses`.Name=(SELECT DISTINCT `CourseNo` FROM grades WHERE `CourseNo`=`courses`.Name AND grades.StudentNo = '$userid'),'Repeat','New') AS `Status` 
			FROM `courses` WHERE `courses`.ID IN ($out) ORDER BY `courses`.Name ASC"; */
			
			$sql = "SELECT feecode,feedescription,fee FROM sis_student_fee_codes WHERE 
				programno=(SELECT ShortName FROM study WHERE ID=
				(SELECT StudyID FROM `student-study-link` WHERE StudentID='$userid')) AND 
				feedescription LIKE 'Tuition%'
				UNION
				SELECT feecode,feeName,fee FROM sis_student_fee_codes WHERE feedescription LIKE 'misc%'
				UNION
				SELECT feecode,CONCAT('Repeat course ',x.`Name`) AS feedescription,fee FROM
				(SELECT a.`Name`,IF(a.Name=(SELECT DISTINCT `CourseNo` FROM grades WHERE `CourseNo`=a.`Name` AND grades.StudentNo = '$userid'),'DeCTF','EX001') AS `Status` FROM `courses` a WHERE a.ID IN ($out) ) as x LEFT JOIN sis_student_fee_codes ON x.`Status`=sis_student_fee_codes.feecode
				WHERE x.Status='DeCTF'
				UNION
				SELECT feecode,CONCAT(sis_student_fee_codes.feedescription,' ',x.`Name`) AS feedescription,fee FROM
				(SELECT a.`Name`,'EX001' AS `Status` FROM `courses` a WHERE a.ID IN ($out) ) as x LEFT JOIN sis_student_fee_codes ON x.`Status`=sis_student_fee_codes.feecode";
			
			//echo $sql; 


			$run = $this->core->database->doSelectQuery($sql);
			
			echo'<br/> <h2>Your Invoice</h2><br/>';
			echo'<table border="0" cellpadding="3" cellspacing="0" class="table table-bordered table-striped table-hover">
				<tr>
					<td>Fee Code</td>
					<td>Name</td>
					<td>Amount</td>
				</tr>';

			$total = 0;
			
			$i =1;
			while ($row = $run->fetch_assoc()) {
				$amount = $row['fee'];
				$name = $row['feedescription'];
				$code = $row['feecode'];
				$total = $total + $amount;

				echo'<tr>
					<td><b>'.$code.'</b></td>
					<td><b>'.$name.'</b></td>
					<td>K'.number_format($amount,2).'</td>
					</tr>';
				echo'<input type="hidden" name="invamount'.$i.'" value="'.$amount.'"/>';
				echo'<input type="hidden" name="invname'.$i.'" value="'.$name.'"/>';
				echo'<input type="hidden" name="invcode'.$i.'" value="'.$code.'"/>';
				$i++;
			}
			echo'<input type="hidden" name="count" value="'.$i.'"/>';
			
				echo'<tr>
					<td colspan="2"><b>TOTAL</b></td>
					<td ><b>K'.number_format($total,2).'</b></td>
					</tr>';
			echo'</table>';



			echo '</p> <p style="font-size: 15px;"><b>Is This Information Accurate?</b></p>';
			
			echo'<script>
				function goBack() {
					window.history.back()
				}
			</script>';

			echo '<p><button onclick="goBack()" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px; padding-left: 20px; padding-right: 20px; padding-bottom: 10px; border: 1px solid #000; background-color: #e81f1f"> <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"> NO</button> 
				<button onclick="this.form.submit();" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px;  padding-left: 20px; padding-bottom: 10px; padding-right: 20px; border: 1px solid #000; background-color: #39c541"> <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"> YES</button></p>
				</form>';

		}else if($item == "delete"){
			
			$cid = $this->core->subitem;

			if($this->core->role < 100){
				$userid = $this->core->userID;
			}else{
				$userid = $this->core->cleanGet['userid'];
			}
	
			if(isset($cid)){
				if($cid == "all"){
					$sqld = "UPDATE  `course-electives` SET `Approved` = '0'  WHERE `StudentID` = $userid AND `Approved` = '1' AND `PeriodID` = '$periodid';";
				}else{
					$sqld = "UPDATE  `course-electives` SET `Approved` = '0'  WHERE `StudentID` = '$userid' AND `CourseID` = '$cid';";
				}
				
			}else{
				$sqld = "UPDATE  `course-electives` SET `Approved` = '0' WHERE `StudentID` = $userid AND `Approved` = '1' AND `PeriodID` = '$periodid'; ";
			}

			$this->core->database->doInsertQuery($sqld);

			echo '<span class="successpopup">Your course(s) were disapproved, please go back to approval and add new ones</span>';
			$add = TRUE;
			
		}else if($item == "save"){
		$periodid = $this->getPeriod();
		
		//echo "<h2>".$periodid."</h2>"; 
		//Late registration 	
		$sqlLT = "SELECT * FROM `periods` WHERE ID = '$periodid' ";

		$runLT = $this->core->database->doSelectQuery($sqlLT);
		
		if($this->core->role == 104 ||  $this->core->role == 1000){
			
			echo '<div class="alert alert-warning" role="alert">Registration is closed but you have access</div>'; 
		
		} else {
			while ($fetch = $runLT->fetch_assoc()){
				if($fetch['CourseRegEndDate'] < date('Y-m-d')){
					echo'<div class="alert alert-warning" role="alert">Course Registration for current semester is closed!</div>';
					return FALSE;
				}	
			}
			
		}
		
		//End of late registration

			
			/***************temporary fix to sort out wrong registration**************/
			$sql_payroll_exempt= "SELECT * FROM ac_registration_exemption WHERE student_id = '$userid'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
			
				//echo'<div class="warningpopup">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				//return FALSE;
				if($actual > 100){
					echo '<div class="alert alert-danger" role="alert">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE <b>K'.number_format($actual).'</b> BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
					return;
				}
			} else {
				
				echo'<div class="alert alert-danger" role="alert">Payment exempted</div>';
				
			}
			/*----part of the temporary fix to be removed once registration closes-----
			if($actual > 100){
				echo '<div class="errorpopup">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
				return;
			}
			*/

			$courses = $_SESSION['courses'];
			$period = $this->getPeriod();
			$boarding = $_SESSION['boarding'];
			$roleID = $_SESSION['role'];
			
			$approval=0;
			if ($roleID > 10){
				$approval=1;
			}
			
			foreach($courses as $course){
				$sqx = $sqx . "INSERT INTO `course-electives` (`ID`, `StudentID`, `CourseID`, `EnrolmentDate`, `Approved`, `PeriodID`) 
					VALUES (NULL, '$userid', '$course', NOW(), '$approval', '$periodid'); ";
			}

			if ($this->core->database->mysqli->multi_query($sqx) == TRUE) {
				do {
					if ($result = $this->core->database->mysqli->store_result()) {
						$result->free();
					}
  
        				if ($this->core->database->mysqli->more_results()) {
        				}
				} while ($this->core->database->mysqli->next_result());

				echo '<span class="successpopup">YOUR COURSES HAVE BEEN SAVED <br>';
				echo '<a href="'.$this->core->conf['conf']['path'].'/information/show/'.$userid.'">RETURN TO PROFILE</a> </span>';
			}
			//ADD invoice
			$sqlInvp = "SELECT * FROM `periods` WHERE ID = '$period' ";
			
			//echo $sqlInvp;
			$runInvp = $this->core->database->doSelectQuery($sqlInvp);
			while ($fetchInvp = $runInvp->fetch_assoc()){
			
				$academicyear = $fetchInvp['Year'];
				$semester = $fetchInvp['Semester'];
			
			}
			
			$x = $this->core->cleanPost['count'];			
			$i = 1;			
			While ($i < $x ){
				$amount = $this->core->cleanPost['invamount'.$i];
				$name = $this->core->cleanPost['invname'.$i];
				$code = $this->core->cleanPost['invcode'.$i];
				

				// ADD TO REPORTED
				$sql = "INSERT INTO `acct_invoice`(`transactionDate`, `feecode`, `description`, `amount`, `studentNo`, `academicYear`, `semester`, `editedById`) VALUES ( NOW(), '$code', '$name','$amount','$userid' ,'$academicyear', '$semester', '$period');";
				//echo $sql;
				$this->core->database->doInsertQuery($sql); 
				
				$i++;
			}
			echo '<span class="successpopup">Your invoice has been generated <br>';
			


			// ADD TO REPORTED
			$sql = "INSERT INTO `reporting` (`ID`, `StudentID`, `DateTime`, `PeriodID`) VALUES (NULL, '$userid', NOW(), '$periodid');";
			$this->core->database->doInsertQuery($sql);


			if($boarding == TRUE){
				include $this->core->conf['conf']['viewPath'] . "accommodation.view.php";
				$housing = new accommodation();
				$housing->buildView($this->core);
				$housing->applyAccommodation();
			}

		}else{
			
			/***************temporary fix to sort out wrong registration**************/
			$sql_payroll_exempt= "SELECT * FROM ac_registration_exemption WHERE student_id = '$userid'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
			
				//echo'<div class="warningpopup">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				//return FALSE;
				if($actual > 100){
					echo '<div class="alert alert-danger" role="alert">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE <b>K'.number_format($actual).'</b> BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
					return;
				}
			} else {
				
				echo'<div class="alert alert-danger" role="alert">Payment exempted</div>';
				
			}
			/*----part of the temporary fix to be removed once registration closes-----
			if($actual > 100){
				echo '<div class="errorpopup">YOU HAVE AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
				return;
			}
			*/			
			echo "<script>
				jQuery(document).ready(function(){
					var totalcost = 0;
					var totalcredit = 0;
 
					var maxcredit = 5;
					var fees = parseInt($('#totalfees').text());
	
					jQuery('[id^=sp]').change(function() { 
						var checked = jQuery(this).is(':checked');
		
						var result = jQuery(this).attr('id').split('.');
						var course = '#cp' + result[1];
						var credit = '#cc' + result[1];


						if (totalcredit > maxcredit) {	
								
								totalcredit = totalcredit - creditvalue;
								totalcost = totalcost - costvalue;
	

								alert('You have selected more credits than possible you currently have ' + totalcredit + ' selected.');
								$(this).attr('checked', false);
						
						}

 
					});
				});
				</script>"; 

			$center = $this->core->cleanGet['center'];
/*
			if($center == ''){

				include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
				$select = new optionBuilder($this->core);
				$centers = $select->showCenters(NULL);
	

				echo '<form id="centersubmit" name="coursessubmit" method="GET" action="'. $this->core->conf['conf']['path'] . '/register/course/select?userid='.$userid.'">
					<div class="heading">EXAM CENTER</div>
					<div class="label">Where are you writing exams from?</div>
					<input type="hidden" name="userid" value="'.$userid.'">
					<select name="center">
						'.$centers.'
					</select>';

				echo '<br><input type="submit"  value="Save center"></form>';
				return;

			} else {
				echo '<div class="successpopup">Your exam center has been set as '.$center .'</div>';

				$sql = "INSERT  INTO `student-data-other` (`ID`, `StudentID`, `YearOfStudy`, `ExamCentre`,`DateTime`, `PeriodID`) 
					VALUES (NULL, '$userid', '', '$center', NOW(), '$periodid') ON DUPLICATE KEY UPDATE `ExamCentre`='$center',`DateTime`= NOW() ";
				$this->core->database->doInsertQuery($sql);

			} */

		//Late registration 	
		$sqlLT = "SELECT * FROM `periods` WHERE ID = '$periodid'";

		$runLT = $this->core->database->doSelectQuery($sqlLT);
		if($this->core->role == 104 ||  $this->core->role == 1000){
			
			echo '<div class="alert alert-success" role="alert">Registration is closed but you have access</div>'; 
		
		} else {
			while ($fetch = $runLT->fetch_assoc()){
				if($fetch['CourseRegEndDate'] < date('Y-m-d')){
					echo'<div class="alert alert-danger" role="alert">Course Registration for current semester is closed!</div>';
					return FALSE;
				}	
			}
			
		}
		//End of late registration



			$sql = "SELECT * FROM `course-electives` 
				WHERE `course-electives`.`PeriodID` = '$periodid' 
				AND `course-electives`.StudentID = '$userid'";
 
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) { 
				echo '<div class="alert alert-danger" role="alert" >You have already registered for this term.</div>';
				return FALSE;
			}


			$sql = "SELECT * FROM `course-electives` 
				WHERE StudentID = '$userid' 
				AND `PeriodID` IN (SELECT `periods`.ID
				FROM `periods`, `basic-information` 
				WHERE `basic-information`.ID = '$userid' 
				AND CURDATE() BETWEEN `CourseRegStartDate` AND  `CourseRegEndDate`
				AND `Delivery` = `StudyType`)";


			$run = $this->core->database->doSelectQuery($sql);

			if ($run->num_rows > 0){
				echo '<span class="errorpopup">You have already submitted your course registration</span>';

				$this->approvalRegister($userid);

				return;

			}




			$sql = "SELECT * FROM `basic-information` WHERE ID = '$userid' AND `Status` = 'Approved'";
			$run = $this->core->database->doSelectQuery($sql);

			if ($run->num_rows == 0){
				echo '<span class="errorpopup">You will have to register online first. Start by clicking on your study below:</span>';

				include $this->core->conf['conf']['viewPath'] . "intake.view.php";
				$intake = new intake();
				$intake->buildView($this->core);
				$intake->adminIntake();

				return;
			}



			echo '<form id="coursessubmit" name="coursessubmit" method="post" action="'. $this->core->conf['conf']['path'] . '/register/course/submit?userid='.$userid.'">
			<div class="col-lg-12 greeter" style="">Select your courses</div>';
			echo '<p style="font-size: 15px;">Please select all of the courses you are taking to be able to sit for the examination.
				 Make sure you double check your information before submitting. When you are done selecting the courses press the button below the list "saying submit course registration"</p>';
			
			$sql = "SELECT * FROM `student-study-link` as sp, `study` as p
				WHERE sp.`StudentID` = '$userid' 
				AND sp.`StudyID` = p.`ID` 
				ORDER BY sp.`ID` DESC LIMIT 1";


			$run = $this->core->database->doSelectQuery($sql);
	
			while ($row = $run->fetch_assoc()) {
				$name = $row['Name'];
				$studyid =  $row['StudyID'];
				$studentid = $row['StudentID'];
				
				echo "<p><b>Your study ". $name . " has the following available courses:</b><br>";
			}


			$sql = "SELECT DISTINCT `courses`.ID, `courses`.Name, `courses`.CourseDescription, IF (`courses`.elective =0,'Compulsory','Elective') AS elective,IF(`courses`.Name=(SELECT DISTINCT `CourseNo` FROM grades WHERE `CourseNo`=`courses`.Name AND grades.StudentNo = '$userid'),'Repeat','New') AS `Status`,`course-year-link`.`Year`,`course-year-link`.Semester FROM `courses`, `course-year-link` 
				WHERE `course-year-link`.CourseID = `courses`.ID 
				AND `course-year-link`.StudyID = '$studyid' 
				AND `courses`.Name NOT IN (SELECT `CourseNo` FROM grades 
							WHERE grades.Grade IN ('A', 'B', 'C', 'A+', 'B+', 'C+',  'P',  'CP','S') 
							AND grades.StudentNo = '$userid')
				ORDER BY `course-year-link`.`Year`,`course-year-link`.Semester,`courses`.Name ASC ";
			
				
			$run = $this->core->database->doSelectQuery($sql);

			$i = 1;

				echo'<table border="0" cellpadding="3" cellspacing="0" class="table table-bordered table-striped table-hover">
            			<tr class="tableheader">'.
				'<td width="30px">#</td>'.
				'<td width="100px"><b>Course</b></td>'.
				'<td width="300px"><b>Course description</b></td>'.
				'<td width="100px"><b>Session</b></td>'.
				'<td width="100px"><b>Status</b></td>'.
				'</tr>';
				$i=0;

				while ($fetchs = $run->fetch_assoc()) {
					$elective = $fetchs['elective'];
					$status = $fetchs['Status'];
					
					if($status == 'Repeat'){
						$checked="checked";
					}else{
						$checked="";
					}
					
					echo '<tr>
						<td><input type="checkbox" name="courses[]" value="' . $fetchs['ID'] . '" id="sp.'.$i.''.$fetchs['ID'].'" style="width: 20px;" '.$checked.'/> </td>
						<td><b><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetchs['ID'] . '"> ' . $fetchs['Name'] . '</b></a></td>
						<td>' . $fetchs['CourseDescription'] . '</td>
						<td><div id="cc'.$i.'"> Year: ' . $fetchs['Year'].' Sem :'.$fetchs['Semester'] . '</div></td>
						<td><div id="cp'.$i.'">'.$elective. ' ' . $status . '</div></td>
					      </tr>';
					$i++;
				}

				if ($i == 0) {
					echo 'No courses have been added to the program yet. Please <a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' .$studyid. '">add some.</a>';		
				}

				echo'</table>';
				if ($i > 0) {
					echo'<br> <input type="submit" value="Submit course registration"></form>';		
				}else{
					echo ' <a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $studyid . '">add some.</a>';
				}
				

			}

	}

	private function generatePassword($length, $charset = 'abcdefghijklmnopqrstuvwxyz0123456789') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	public function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}


	public function studyRegister($item) {
		echo'<div id="templatepath" style="display:none">' . $this->core->fullTemplatePath .'</div>';
		echo'<div id="path" style="display:none">' . $this->core->conf['conf']['path'] .'</div>';

		if ($item) {

			if($_GET['existing'] == 'yes'){
				$existing = TRUE;
			}

			$sql = "SELECT `study`.ID, `study`.Name FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ID = $item";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {


				if($existing=="yes"){
					echo'<p class="title">REGISTRATION FOR FIRST TIME STUDENTS</p>';
					echo '<form id="enroll" name="enroll" method="post" action="' . $this->core->conf['conf']['path'] . '/register/submit" enctype="multipart/form-data" >
					<input type="hidden" name="studyid" value="' . $fetch['0'] . '">
					<p><br>Please complete the following form entirely to successfully complete your registration. <br> <b> Click on the steps below to continue.</b></p>';
				}else{
					echo'<p class="title">Application form for new students</p>';
					echo '<form id="enroll" name="enroll" method="post" action="' . $this->core->conf['conf']['path'] . '/register/submit" enctype="multipart/form-data" >
					<input type="hidden" name="studyid" value="' . $fetch['0'] . '">
					<p>You are requesting admission to the following programme: <b> ' . $fetch[1] . ' </b> 
					<br>Please complete the following form entirely to successfully complete your application. <br> <b> Click on the steps below to continue. </b></p>';
				}

				

				include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

				$study = $fetch[0];

				$optionBuilder = new optionBuilder($this->core);

				$paymenttypes = 	$optionBuilder->showPaymentTypes();
				$programs = 		$optionBuilder->showStudies();

				include $this->core->conf['conf']['formPath'] . "register.form.php";

			}

			include $this->core->conf['conf']['libPath'] . "/edurole/javascript/footer.js";

		} else {

			$this->core->throwError('No study was selected, please <a href="' . $this->core->conf['conf']['path'] . '/intake">select one</a>');

		}
	}
	
	public function moodleresetRegister($item) {
			$periodid = $this->getPeriod();
				
			$sqlItm = "UPDATE `course-electives` SET  `Moodle`='0' WHERE (`StudentID`='$item' AND `PeriodID`='$periodid')";
								
			$runItm = $this->core->database->doInsertQuery($sqlItm);
			
			if($runItm){
				echo '<b>Update Success, check your moodle courses within the next 5 minutes</b> </br><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'" >Back To Student Profile</a>';
			}else{
				echo '<b>Update failed, please register for current semester</b> </br><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'" >Back To Student Profile</a>';
			}
	}
	public function removeexcludeRegister($item) {
			$periodid = $this->getPeriod();
				
			$userID = $this->core->userID;
			
			$sqlItm = "UPDATE `comments` SET  `Comment`='PROCEED', `UserID`='$userID', Updated= NOW() WHERE (`StudentID`='$item' AND `Comment`='EXCLUDE')";
								
			$runItm = $this->core->database->doInsertQuery($sqlItm);
			
			echo '<b>Update Success, please register for current semester</b> </br><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'" >Back To Student Profile</a>';
			
	}
}

?>
