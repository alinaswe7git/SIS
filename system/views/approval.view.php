<?php
class approval {

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

	public function menuApproval() {
		
		$sql = 'SELECT `basic-information`.StudyType, COUNT(DISTINCT `course-electives`.StudentID) FROM `course-electives`, `basic-information`
			WHERE `course-electives`.StudentID = `basic-information`.ID
			GROUP BY `basic-information`.StudyType ORDER BY StudyType';

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		echo '<div class="toolbar">';

		while ($fetch = $run->fetch_row()) {
			echo '<a href="' . $this->core->conf['conf']['path'] . '/approval/'.$fetch[0].'">'.ucwords($fetch[0]).' students ('.$fetch[1].')</a>';
		}

		echo'</div>';
	}


	public function fulltimeApproval() {
		$this->menuApproval();
		$this->manageApproval("Fulltime");
	}

	public function distanceApproval() {
		$this->menuApproval();
		$this->manageApproval("Distance");
	}

	public function partimeApproval() {
		$this->menuApproval();
		$this->manageApproval("Partime");
	}

	public function blockApproval() {
		$this->menuApproval();
		$this->manageApproval("Block");
	}

	public function addApproval($item) {
		$uid = $this->core->userID;
		$period = $this->core->cleanGet["period"];
		$course = $this->core->cleanGet["course"];
		
		if($this->checkThreshold($item,Null,$period) == FALSE){
			return;
		}
		

		$sql = "INSERT INTO `course-electives` (`ID`, `StudentID`, `CourseID`, `EnrolmentDate`, `Approved`, `PeriodID`,`ApprovedBy`) 
		VALUES (NULL, '$item', '$course', NOW(), '1', '$period', '$uid')";

		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirectsub("approval", "show", $item, $period);
	}

	public function approveApproval($item) {
		$uid = $this->core->userID;
		$period = $this->core->cleanGet["period"];
		$elective = $this->core->subitem;
		if($this->checkThreshold($item,Null,$period) == FALSE){
			return;
		}
				
			//Code checking for payment 0 balance when HOD tries to Add
			// PAYMENT VERIFICATION FOR GRADES
			//require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			//$payments = new payments();
			//$payments->buildView($this->core);
			//$actual = $payments->getBalance($item);
			
			/***************payment 0 balance when HOD tries to Add **************/
			//$sql_payroll_exempt= "SELECT * FROM ac_payroll WHERE student_id = '$item'";
	
			//$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			//if($run_payroll_exempt->num_rows == 0){
			
				//echo'<div class="warningpopup">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				//return FALSE;
				//if($actual > 100){
					//echo '<div class="errorpopup">STUDENT HAS AN OUTSTANDING BALANCE FROM A PREVIOUS TERM AND MAY NOT REGISTER. <br>YOU NEED TO SETTLE K'.number_format($actual).' BEFORE REGISTRATION, FOR ANY QUERIES SEE THE ACCOUNTS DEPARTMENT.</div>'; 
					//return;
				//}
			//} else {
				
				//echo'<div class="warningpopup">Payment exempted</div>';
				
			//}
			/*----End -----*/
		
		
		if($elective == "all"){
			$sql = "UPDATE `course-electives` SET `Approved` = '1', `ApprovedBy`='$uid' WHERE `course-electives`.`StudentID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->redirect("approval", "manage", $period);

			include $this->core->conf['conf']['viewPath'] . "register.view.php";
			$registration = new register();
			$registration->buildView($this->core);

			//$billsage = $registration->getBillSage($item, 0, "confirm");

			$sql = "INSERT INTO `reporting` (`ID`, `StudentID`, `DateTime`, `PeriodID`) VALUES (NULL, '$item', NOW(), '$period');";
			$this->core->database->doInsertQuery($sql);
		}else{
			$sql = "UPDATE `course-electives` SET `Approved` = '1' , `ApprovedBy`='$uid' WHERE `course-electives`.`ID` = $elective;";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->redirectsub("approval", "show", $item, $period);
		}
	}
	
	private function checkThreshold($userid, $num_courses = NULL, $period = NULL){
		
		//$sql_thresh = "SELECT threshold FROM ac_threshold WHERE name=(SELECT StudyType FROM `basic-information` WHERE ID ='$userid')
		//AND school_id=(SELECT ParentID FROM `study` WHERE ID =(SELECT StudyID FROM `student-study-link` WHERE StudentID = '$userid'))";
		
		//$run_thresh = $this->core->database->doSelectQuery($sql_thresh);
		//while ($fetch_thresh = $run_thresh->fetch_assoc()) {
		//	$threshold = $fetch_thresh['threshold'];
		//}
		if($period == NULL){
			$period = $this->getPeriod();
		}
		
		include $this->core->conf['conf']['viewPath'] . "register.view.php";
		$registration = new register();
		$registration->buildView($this->core);
		
		$registration->getBillSage($userid, $num_courses, FALSE);
		
		$sql_bill = "SELECT * FROM `billing-temp` WHERE StudentID='$userid' AND PeriodID=".$period ;
		$run_bill = $this->core->database->doSelectQuery($sql_bill);
		
		$bill  = 0;
		
		while ($fetch_bill = $run_bill->fetch_assoc()) {
			$bill  = $fetch_bill['Amount'];
		}
		
		if($bill == 0){
			echo'<div class="alert alert-danger" role="alert">Billing template for this semester has <b>not set</> for this program by Accounts Department, Please inform the Accounts Department to set it up.</div>';
			return FALSE;
		}
		
		if($bill=="21450" || $bill == "35250"){
			$bill = 9750;
			
		}
		
		$threshold = round($bill/2);
		
		
		include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$actual = $payments->getBalance($userid);
		$actual = round($actual);
			
		if($actual < 0){
			$actual = $actual;
			
				
		}
			/*/Quick fix for somhs 
			    $bill_sub=0;
				
				$sql_School = "SELECT a.ParentID FROM `student-study-link` b, study a WHERE b.StudentID='$userid' AND b.StudyID= a.ID ";
				$run_School = $this->core->database->doSelectQuery($sql_School);
							
				while ($fetch_School = $run_School->fetch_assoc()) {
					$ParentID  = $fetch_School['ParentID'];
				}
				
				if ($ParentID == 204 OR $ParentID == 205 OR $ParentID == 202 OR $ParentID == 201 OR $ParentID == 203  OR $ParentID == 206 ){
					
					$bill_sub  = 11700;
				}
				
				$actual = $actual - $bill_sub;
			//End of fix to be disabled after supp students are fully registered	*/

		
		$calc = $threshold+$actual;
		echo'<div class="alert alert-danger" role="alert">STUDENT BALANCE: K'.$actual.' BILL 50%: K'.$threshold.'  BALANCE AFTER BILL: K'.$calc.'</div>';
		

		if ($calc >= 1){
		//if ($threshold >= $actual){
			
			$sql_payroll_exempt= "SELECT * FROM ac_payroll WHERE student_id = '$userid'";
	
			$run_payroll_exempt = $this->core->database->doSelectQuery($sql_payroll_exempt);
			
			if($run_payroll_exempt->num_rows == 0){
			
				echo'<div class="alert alert-danger" role="alert">You cannot approve courses this students balance is currently '.$actual.', therefore the student has not met the 50% threshhold required to proceed with registration. TELL THE STUDENT TO PROCEED TO ACCOUNTS.</div>';
				return FALSE;
				
			} else {
				
				echo'<div class="alert alert-danger" role="alert">Paying via payroll or exempted</div>';
				
			}
		} 
		
		return TRUE;
	}

	public function rejectApproval($item) {
		$elective = $this->core->subitem;
		$period = $this->core->cleanGet["period"];

		if($elective == "all"){
			$sql = "DELETE FROM `course-electives`  WHERE `course-electives`.`StudentID` = $item AND `PeriodID` = '.$period.';";
			$this->core->redirect("approval", "manage", $period);
		}else{
			$sql = "DELETE FROM `course-electives` WHERE `course-electives`.`ID` = $elective;";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->redirectsub("approval", "show", $item, $period);
		}

		$run = $this->core->database->doInsertQuery($sql);
	}

	public function showApproval($item, $period) {
		

		if($period == ""){
			$period = $this->core->cleanGet["period"];
		} 

		$sql = "SELECT DISTINCT  `Firstname`, `Surname`, `study`.Name AS StudyName, `courses`.ID as CID,  `course-electives`.ID as CEID, `course-electives`.`StudentID`, `courses`.`Name`, `CourseCredit`, `CourseDescription`, `Approved` 
			FROM `course-electives`, `courses`, `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Approved' 
			AND `course-electives`.PeriodID = '$period'
			AND `basic-information`.ID = '$item'
			AND `course-electives`.StudentID = `basic-information`.ID
			AND `courses`.ID = `course-electives`.CourseID
			GROUP BY `course-electives`.CourseID";

		$run = $this->core->database->doSelectQuery($sql);
		
		$num_courses = $run->num_rows;
	
		
		if($this->checkThreshold($item, $num_courses,$period) == FALSE){
			
			return;
		}

		$i = 1;

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/manage/'.$period.'">BACK TO LIST</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/approve/'.$item.'/all?period='.$period.'" style="background-color: green;">APPROVE REGISTRATION</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$item.'/all?period='.$period.'" style="background-color: red;">REJECT REGISTRATION</a>'.

		'</div>';


		$total = 0;
		$i = 0;

		while ($fetch = $run->fetch_assoc()) {
			$uid = $fetch['StudentID'];
			$course = $fetch['Name'];
			$names = $fetch['Firstname'] . " " . $fetch['Surname'];
			$studyname = $fetch['StudyName'];
			$cid = $fetch['ID'];
			$description = $fetch['CourseDescription'];
			$credits = $fetch['CourseCredit'];
			$approved = $fetch['Approved'];
			$apid = $fetch['CEID'];


			if($i == 0){

				echo'<div class="greeter">Name: '.$names.'</div><div class="title"> Program: '.$studyname.' </div><br/><h2>REGISTERED COURSES</h2><br/>';

				echo '<table id="active" class="table table-bordered  table-hover">
					<thead>
					<tr>
						<th bgcolor="#EEEEEE" width="30px" data-sort"string"><b> #</b></th>
						<th bgcolor="#EEEEEE" width="100px" data-sort"string"><b> Course Code</b></th>
						<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Course Name</b></th>
						<th bgcolor="#EEEEEE"><b> <b>StudentID</b></th>
						<th bgcolor="#EEEEEE" width="150px"><b> Options</b></th>
					</tr>
				</thead>
				<tbody>';
			}

			if ($approved == 0) {
				$class = 'class="info"';
				$next = '';
				$next = $next.'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$uid.'/' . $apid .'?period='.$period.'"><b>Remove</b></a>';
			} elseif ($approved == 1){
				$class = 'class="success"';
				$next = ' <b>APPROVED </b> | ';
				$next = $next.'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$uid.'/' . $apid .'?period='.$period.'"><b>Remove</b></a>';
			} elseif ($approved == 2){
				$class = 'class="danger"';
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/approval/approve/'.$uid.'/' . $apid .'?period='.$period.'"> <b>Approve </b></a> | ';
				$next = $next.'<b>Remove</b>';
			}

			$i++;
			$total = $total+$credits;

			echo '<tr '.$class.'>
				<td>'.$i.'</td>
				<td><b>' . $course .'</b></td>
				<td><b>' . $description .'</b></td>
				<td>' . $uid . '</td>
				<td>'.$next.'</td>
				</tr>';

		}

		echo '<tr class="warning">
			<td></td>
			<td colspan="2"><b>Total number of courses</b></td>

			<td colspan="2"><b>' . $i . '</b></td>
		
			</tr>';


		echo '</tbody>
		</table>';


		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);

		$courselist = $select->showCourses($schoolid);

		echo '<form id="addCourse" name="addCourse" method="get" action="'. $this->core->conf['conf']['path'] .'/approval/add/'.$item.'">
			<input type="hidden" value="'.$period.'" name="period">
			<div class="toolbar">'.
			'<div class="toolbaritem" style="padding: 7px; width: 300px; overflow: hidden; white-space: pre; text-overflow: ellipsis; -webkit-appearance: none;">'.
				'<select name="course" style="font-size: 12pt;">
				 <option value="" selected>--SELECT COURSE--</option>
				 '.$courselist.'
				 </select>'.
			'</div>'.
			'<input type="submit" value="Add Another Course" style="height: 40px; ">'.
			'</div>'.
			'</form>';
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


	public function manageApproval($item) {
		$uid = $this->core->userID;

		if($item == ""){
			$item = "%";
		}

		$sqlDeleteZeroBill = "DELETE FROM `billing-temp` WHERE Amount = 0";
		$runDeleteZeroBill = $this->core->database->doInsertQuery($sqlDeleteZeroBill);

		$item = $this->getPeriod();

		
		$sql = "SELECT `SchoolID`, `schools`.`Description` 
			FROM `staff`
			LEFT JOIN `schools` ON `schools`.ID = `SchoolID`
			WHERE `EmployeeNo` = '$uid'";
			$run = $this->core->database->doSelectQuery($sql);
		

		while ($fetch = $run->fetch_assoc()) {
			$school = $fetch['SchoolID'];
			$name = $fetch['Description'];
		}

		if($school == "" && $this->core->role != 1000){
			echo '<div class="warningpopup">You do not have a school to manage</div>';
			return;
		}


		if($this->core->role == 1000){
			$school = '%';
			echo '<div class="greeter">ALL SCHOOLS</div>';
		} else {
			echo '<div class="greeter">'.$name.'</div>';
		}


		
		////tempfix for Medicine
		//if($school == '204' ||$school == '207' ){
			//echo '<div class="greeter">'.$name.'</div>';
		//	$item = 56;
		//}
		$period = $item;

		$sql = "SELECT COUNT(DISTINCT `course-electives`.CourseID) as CT, FirstName, Surname, `basic-information`.ID as StudentID, `study`.Name
			FROM `course-electives`, `courses`, `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Approved' 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `course-electives`.CourseID = `courses`.ID
			AND `study`.ParentID LIKE '$school'
			AND `course-electives`.PeriodID = $period
			AND `course-electives`.Approved = 0
			GROUP BY `course-electives`.StudentID
			ORDER BY `study`.Name ASC";


		$run = $this->core->database->doSelectQuery($sql);
		$i = 1;

		
		echo '<table id="active" class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th bgcolor="#EEEEEE" width="30px" data-sort"string"><b> #</b></th>
							<th bgcolor="#EEEEEE" data-sort"string"><b> Student Name</b></th>
							<th bgcolor="#EEEEEE"><b> <b>Student ID</b></th>
							<th bgcolor="#EEEEEE"><b> <b>Program</b></th>
							<th bgcolor="#EEEEEE"><b> Courses</b></th>
							<th bgcolor="#EEEEEE" width="100px"><b> Options</b></th>
						</tr>
					</thead>
					<tbody>';

		while ($fetch = $run->fetch_assoc()) {
			$study = $fetch['Name'];
			$firstname = $fetch['FirstName'];
			$middlename = $fetch['MiddleName'];
			$surname = $fetch['Surname'];
			$sex = $fetch['Sex'];
			$uid = $fetch['StudentID'];
			$nrc = $fetch['GovernmentID'];
			$grade = $fetch['GradeTotal'];
			$gradeno = $fetch['GradeNo'];
			$shortcode = $fetch['Shortcode'];
			$study = $fetch['Name'];
			$courses = $fetch['CT'];

			//$full = $study;
			//$study= (strlen($study) > 50) ? substr($study,0,50).'...' :$study;

				echo '<tr>
				<td>'.$i.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a>  </td>
				<td>' . $uid . '</td>
				<td>' . $study . '</td>
				<td><b>' . $courses . '</b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/approval/show/' . $uid . '?period='.$period.'"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif"> <b>Approval</b> </a></td>
				</tr>';


			$i++;



		}

		echo '</tbody>
		</table>';

	}

}

?>
