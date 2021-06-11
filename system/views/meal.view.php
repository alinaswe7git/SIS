<?php
class meal {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = FALSE;
		$this->view->footer = FALSE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	function printMeal() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$schools = $optionBuilder->showSchools();
		$periods = $optionBuilder->showPeriods();
		$programs = $optionBuilder->showStudies();
		$centres = $optionBuilder->showCentres();
		include $this->core->conf['conf']['formPath'] . "searchmealcard.form.php";
	}

	private function viewMenu($item){

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$periods = $select->showPeriods();

		echo'<form id="narrow" name="narrow" method="get" action="">
			<div class="toolbar">
				<div class="toolbaritem">Filter to show students from: 
					<select name="period" class="submit" style="width: 230px; margin-top: -17px;">
					'. $periods .'
					</select>
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/>
				</div>
			</div>
		</form>';
	}
	
	public function reportMeal($item){

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$periods = $select->showPeriods();
		
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		echo'<form id="narrow" name="narrow" method="get" action="">
				<div class="toolbaritem">Filter to show report from: 
					<input type="date" name="Start" class="submit" style="width: 230px; margin-top: -17px;"/>
					
					to :
					<input type="date" name="End" class="submit" style="width: 230px; margin-top: -17px;"/>
					<input type="submit" value="Run"  name="submit" style="width: 80px; margin-top: -15px;"/>
			   </div>
			
		</form> <br> <hr>';
		
		if(!empty($_GET['Start']) || !empty($_GET['End'])){
			
			$start = $_GET['Start'];
			$end = $_GET['End'];
		
			//echo $start." ".$end ;
			$sql = "SELECT a.DH,CONCAT(b.FirstName,' ',IF(b.FirstName=b.MiddleName,'',MiddleName),b.Surname) as Name,
					a.StudentID,a.Type,a.Date,a.Time,count(a.Type) as Served
					FROM meals a 
					LEFT JOIN `basic-information` b ON a.StudentID=b.ID
					LEFT JOIN bd_catering c ON b.ID = c.student_id
					WHERE a.Date BETWEEN '$start' AND '$end'
					GROUP BY a.Type, a.DH
					ORDER BY a.DH,a.Type";

			//echo $sql ;
			$run = $this->core->database->doSelectQuery($sql);

			echo 
			'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
			<h2>Meal Served </h2><h3> from :'.$start.' to:'.$end.'</h3> <br>
			<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
			'<tr class="heading">' .
			'<td width=""><b>Dinning</b></td>' .
			'<td width=""><b>Meals Type</b></td>' .
			'<td width=""><b>Collected</b></td>' .
			'</tr>';

			$i = 0;

			while ($fetch = $run->fetch_assoc()) {
				$dh=  $fetch['DH'];
				$type =  $fetch['Type'];
				$date = $fetch['Date'];
				$served = $fetch['Served'];
								
				echo '<tr>
					<td><b>'.$dh.'</b></td>
					<td>' . $type . '</td>
					<td>' . $served . '</td>
					</tr>';

				$total += $served;
				
			}


			echo '<tr class="heading"><td><b>Total</b></td>' .
			'<td width="60px"></td>' .
			'<td colspan="6"><b>'. $total.'</b></td>'.
			'</tr>';

			echo '</table>';
		}else{
			//echo $start." ".$end ;
			$sql = "SELECT a.DH,CONCAT(b.FirstName,' ',IF(b.FirstName=b.MiddleName,'',MiddleName),b.Surname) as Name,
					a.StudentID,a.Type,a.Date,a.Time,count(a.Type) as Served
					FROM meals a 
					LEFT JOIN `basic-information` b ON a.StudentID=b.ID
					LEFT JOIN bd_catering c ON b.ID = c.student_id
					WHERE a.Date ='$today'
					GROUP BY a.Type, a.DH
					ORDER BY a.DH,a.Type";

			//echo $sql ;
			$run = $this->core->database->doSelectQuery($sql);

			echo 
			'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
			<h2>Meal Served </h2><h3> from :'.$today.'</h3> <br>
			<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
			'<tr class="heading">' .
			'<td width=""><b>Dinning</b></td>' .
			'<td width=""><b>Meals Type</b></td>' .
			'<td width="200px"><b>Date</b></td>' .
			'<td width=""><b>Collected</b></td>' .
			'</tr>';

			$i = 0;

			while ($fetch = $run->fetch_assoc()) {
				$dh=  $fetch['DH'];
				$type =  $fetch['Type'];
				$date = $fetch['Date'];
				$served = $fetch['Served'];
				$dinning='';
				if($dh==1){
					$dinning='Main Dinning';
				}else if($dh==2){
					$dinning='Rock Dinning';
				}else{
					$dinning='Town Campus';
				}
				
				echo '<tr>
					<td><b>'.$dh.'</b></td>
					<td>' . $type . '</td>
					<td>' . $date . '</td>
					<td>' . $served . '</td>
					</tr>';

				$total += $served;
				
			}


			echo '<tr class="heading"><td><b>Total</b></td>' .
			'<td width="60px"></td>' .
			'<td colspan="6"><b>'. $total.'</b></td>'.
			'</tr>';

			echo '</table>';
		}
		
		
	}

	function deleteMeal($item){

		$sql = "DELETE FROM `mealcard` WHERE `ID` = $item";
		$this->core->database->doInsertQuery($sql);

		echo '<div class="successpopup">Mealcard Revoked</div>';

	}

	function manageMeal(){

		$this->viewMenu();
		$period = $this->core->cleanGet['period'];

		if($period == ''){
			echo'<div class="warningpopup">SELECT A PERIOD TO VIEW MEALCARDS</div>';
			return;
		}


		$sql = "SELECT *, `mealcard`.ID as MCID
			FROM `mealcard`, `basic-information` 
			WHERE `mealcard`.StudentID = `basic-information`.ID 
			AND `PeriodID` LIKE $period";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table id="messages" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"><b>#</b></th>
					<th bgcolor="#EEEEEE" width="30px"><b>ID</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Name</b></th>
					<th bgcolor="#EEEEEE" width="150px"><b>Printed on</b></th>
					<th bgcolor="#EEEEEE" width="100px"><b>Manage</b></th>
				</tr>
			</thead>
			<tbody>';


		if($run->num_rows == 0){
			echo'<div class="warningpopup">SELECT A PERIOD TO VIEW MEALCARDS</div>';
		}

		while ($fetch = $run->fetch_assoc()) {
			$mc =  $fetch['MCID'];
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
			$studentid = $fetch['ID'];
			$awarded = $fetch['Description'];
			$start = $fetch['Printed'];
			$i++;
	
       		 	echo'<tr>
				<td>'.$i.'</td>
				<td>'.$studentid.'</td>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show">'.$name.'</a></b></td>
				<td>'.$start.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/meal/delete/'.$mc.'">Revoke</a></td>
			</tr>';
		}

		echo'</table>';

	}

	function batchMeal($item) {
			
		$sql = "SELECT `student_id` FROM `bd_catering` WHERE `dh` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;

		
		while ($fetch = $run->fetch_array()){
			
			echo'<div style="page-break-after: always;"> </div> ';
			$x=0;
		
			$studentid = $fetch[0];
		
			$this->resultsMeal($studentid);

			$first = FALSE;
			$i++;
			$x++;
		}


	}

	function listMeal($item) {
		$uid = $_GET['uid'];

		$students = explode(",", $uid);

		foreach($students as $studentid){
			$studentid = trim($studentid);
			$this->resultsMeal($studentid);
		}
	}
	
	function resultsMeal($item, $period) {

		if(empty($period)){
			$period = $this->core->getCurrentPeriod();
		}

		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}

		if($item == ""){
			$studentID = $_GET['uid'];
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


		$sql = "SELECT DISTINCT bi.ID, bi.GovernmentID, bi.Firstname, bi.MiddleName, bi.Surname, bi.Status, bi.Sex, `courses`.Name, `courses`.ID, `courses`.CourseDescription, `courses`.CourseCredit, `student-data-other`.ExamCentre, `balances`.LastTransaction, `bi`.StudyType
			FROM `basic-information` as bi, `course-electives`, `courses`
			LEFT JOIN `student-data-other` ON `student-data-other`.StudentID = '$studentID'
			LEFT JOIN `balances` ON `balances`.StudentID = '$studentID'
			WHERE `bi`.ID = '$studentID' 
			AND Approved = '1'
			AND `course-electives`.StudentID = `bi`.ID
			AND `course-electives`.CourseID = `courses`.ID";
	

		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;

	
		$run = $this->core->database->doSelectQuery($sql);




		$housing = FALSE;

		$sqld = "SELECT * 
			FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
			WHERE `housing`.RoomID = `rooms`.ID 
			AND `rooms`.HostelID = `hostel`.ID 
			AND `basic-information`.ID = `housing`.StudentID 
			AND `basic-information`.ID = '$studentID'
			AND `housing`.PeriodID = `periods`.ID";

		$rund = $this->core->database->doSelectQuery($sqld);

		while ($fetch = $rund->fetch_assoc()) {

			$hostel = $fetch['HostelName'];
			$room = $fetch['RoomNumber'];
			$id = $fetch['RoomID'];	


			$housing = TRUE;
		}




	

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


		$sqlx = "SELECT DISTINCT bi.ID, bi.GovernmentID, bi.Firstname, bi.MiddleName, bi.Surname, bi.Status, bi.Sex, `courses`.Name, `courses`.ID, `courses`.CourseDescription, `courses`.CourseCredit, `student-data-other`.ExamCentre, `balances`.LastTransaction, `bi`.StudyType
			FROM `basic-information` as bi, `course-electives`, `courses`
			LEFT JOIN `student-data-other` ON `student-data-other`.StudentID = '$studentID'
			LEFT JOIN `balances` ON `balances`.StudentID = '$studentID'
			WHERE `bi`.ID = '$studentID' 
			AND Approved = '1'
			AND `course-electives`.StudentID = `bi`.ID
			AND `course-electives`.CourseID = `courses`.ID";
	

		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;

	
		$runx = $this->core->database->doSelectQuery($sqlx);

			while ($fetchx = $runx->fetch_assoc()){
				$courses = $courses. $fetchx['Name'] . "\n";
			}

		

		$count = 1;
		$currentid = TRUE;
		$total = 0; 
		



		// PAYMENT VERIFICATION FOR EXAM SLIP
		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$balance = $payments->getBalance($studentID);


		$start = TRUE;
		$n=0;
	
  
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
			$mode = $fetch["StudyType"]; 

			$name = $fetch["CourseDescription"];
			$credits = $fetch["CourseCredit"];

			$lasttrans = $fetch['LastTransaction'];

			if($status == "Requesting" || $status == "Approved" ){
				$status = "Fully registered";
			} else {
				$status = "NOT FULLY REGISTERED";
			}
			$n++;
			

			if($start == TRUE){
				// SECURITY
				$rand = rand(100000,999999);

				$owner = $this->core->userID;
				$secname = $studentID . "-".date('Y-m-d')."-".$rand;


				require_once $this->core->conf['conf']['classPath'] . "security.inc.php";
				$security = new security();
				$security->buildView($this->core);
			//	$qrname = $security->qrSecurity($secname, $owner, $studentID, $filename);
				$qrname = $security->qrSecurity($studentID, $studentID, $courses, $studentname, $balance);
				$start = FALSE;
			}

		

			// BEGIN PRINTING COURSES
			if($currentid == TRUE){
				echo'<div style="clear:left; width: 800px; padding-top:20px;  position: relative;  padding-left: 25px; min-height: 500px; display:block; margin-top: 15px; border-bottom: 5px solid #000; page-break-inside: avoid; ">

					<div style="float: left; width: 800px; position: relative; ">
					<center>
							<a href="'. $this->core->conf['conf']['path'] .'">
								<img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" />
							</a>
						</center>
					</div>
					<div style="font-size: 18pt; color: #000; margin-top: 15px; width: 800px; ">
						<center>
							'.$this->core->conf['conf']['organization'].'
							<div style="font-size: 13pt; font-weight: bold;">'.$mode.' STUDENT <br>TEMPORARY MEAL CARD 2018</div>
						</center>
					</div>
					<div style="width: 800px; margin-left: 20px; margin-top: 20px;">';


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
							<br> Gender: <b>'.$sex.'</b> 
							<br>  School: <b>'.$school.'</b>
							<br> Status: <b>'.$status.'</b>

						</div><br>
						<div style="float: left; width: 260px; height: 170px; border: solid 2px #000; text-align: center;">
							<br><br><br> DATE-STAMP
						
						</div>
					</div>

					<div style="clear: both; width: 800px; margin-left: 20px; padding-top: 20px;">
						<b style="font-size: 10px;">1. Meals will only be served on presentation of this card<br>
						2. A charge equivalent to boarding will be made if the card is lost and a new card will not be issued until this amount is paid.<br>
						3. The card can only be used by the person accommodated by the university and identified on this document
						 </b>
					</div>

					<div style="width: 600px; margin-left: 20px; margin-top: 20px;">';

					$currentid = FALSE;
					echo '<style>
					td {
					    border: 1px solid black;
					}
					</style>';
	
					echo'</div></div>
					<div style="clear:left; position: relative; width: 900px; padding-top:20px; min-height: 500px; display:block; margin-top: 15px; border: 0px solid #000; page-break-inside: avoid;  ">';
				
					$day = 17;
					echo'<div style="float: left; width: 170px;"><center><b>DAYS AUGUST</b></center>
							<table cellpadding="0" celspacing="0" style="font-size: 10px;">
									<tr><td><b>Day</b></td><td width="50px"><b>Breakfast</b></td><td width="50px"><b>Lunch</b></td><td width="50px"><b>Dinner</b></td></tr>';
									while($day < 31){
									$day++;
									echo'<tr><td><b>'.$day.'</b></td><td></td><td></td><td></td></tr>';
									}
									echo'</tr>
								</table></div>';
							
					$day = 0;
					echo'<div style="float: left; width: 160px;"><center><b>DAYS SEPTEMBER</b></center>
							<table cellpadding="0" celspacing="0" style="font-size: 10px;">
									<tr><td><b>Day</b></td><td width="50px"><b>Breakfast</b></td><td width="50px"><b>Lunch</b></td><td width="50px"><b>Dinner</b></td></tr>';
									while($day < 30){
									$day++;
									echo'<tr><td><b>'.$day.'</b></td><td></td><td></td><td></td></tr>';
									}
									echo'</tr>
								</table></div>';
				
					$day = 0;
					echo'<div style="float: left; width: 160px;"><center><b>DAYS OCTOBER</b></center>
							<table cellpadding="0" celspacing="0" style="font-size: 10px;">
									<tr><td><b>Day</b></td><td width="50px"><b>Breakfast</b></td><td width="50px"><b>Lunch</b></td><td width="50px"><b>Dinner</b></td></tr>';
									while($day < 30){
									$day++;
									echo'<tr><td><b>'.$day.'</b></td><td></td><td></td><td></td></tr>';
									}
									echo'</tr>
								</table></div>';
							
					$day = 0;
					echo'<div style="float: left; width: 160px;"><center><b>DAYS NOVEMBER</b></center>
							<table cellpadding="0" celspacing="0" style="font-size: 10px;">
									<tr><td><b>Day</b></td><td width="50px"><b>Breakfast</b></td><td width="50px"><b>Lunch</b></td><td width="50px"><b>Dinner</b></td></tr>';
									while($day < 30){
									$day++;
									echo'<tr><td><b>'.$day.'</b></td><td></td><td></td><td></td></tr>';
									}
									echo'</tr>
								</table></div>';
					$day = 0;
					echo'<div style="float: left; width: 160px;"><center><b>DAYS DECEMBER</b></center>
							<table cellpadding="0" celspacing="0" style="font-size: 10px;">
									<tr><td><b>Day</b></td><td width="50px"><b>Breakfast</b></td><td width="50px"><b>Lunch</b></td><td width="50px"><b>Dinner</b></td></tr>';
									while($day < 15){
									$day++;
									echo'<tr><td><b>'.$day.'</b></td><td></td><td></td><td></td></tr>';
									}
									echo'</tr>
								</table></div>';

			}



			$count++;
			$isset = TRUE;
			$total = $total+$credits;
		}
		
		

		if($isset == TRUE){
			
	
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
