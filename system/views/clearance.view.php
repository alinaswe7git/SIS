<?php
class clearance {

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
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/clearance/manage">Pending Clearances</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/clearance/manage/all">All Clearances</a>';
		echo '</div>';
	}


	public function showClearance($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		$editable = $this->core->cleanGet['edit'];
		
		
		if($this->core->role == 1000 && $item != 'hidden'){
			$sql = "SELECT a.ID,b.ID AS ItemID,a.Status,a.CreatedDate,d.Name as Course,CONCAT(c.Name,'-(K ',c.Rate,')') as Category,CONCAT(e.FirstName,' ',e.Surname) as Lname, (b.NumberOfStudents * c.Rate)
					Clearance,a.ApproverOne,a.ApproverTwo,a.ApproverThree,b.NumberOfStudents
					FROM clearance a, `clearance-items` b,`clearance-category` c,courses d,`basic-information` e
					WHERE a.`Status`  IN ('Pending', 'Entry')
					AND a.ID=b.ClearanceID
					AND a.ID = '$item'
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					ORDER BY CreatedDate";

		}

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Item</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Students</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Clearance</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}


		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$id = $row['ID'];
			$itemID = $row['ItemID'];
			$date = $row['CreatedDate'];
			$numberOfStudents = $row['NumberOfStudents'];
			$course = $row['Course'];
			$clearancecategory = $row['Category'];
			$author = $row['Lname'];
			$clearance = $row['Clearance'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$person="";
			
			if($editable == TRUE){
				$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/clearance/edit/'.$itemID.'">Edit</a></b> <br>
				<a href="'. $this->core->conf['conf']['path'] .'/clearance/delete/'.$itemID.'">Cancel</a> <br>';
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$clearancecategory.'</td>
				<td> '.$course.'</td>
				<td> '.$numberOfStudents.'</td>
				<td> '.$date.'</td>
				<td> K'.$clearance.'</td>
				<td> '.$status.'</td>
				</tr>';
			$totalClearance+=$clearance;
			$results = TRUE;


		}
		
		echo'<tr>
					<td colspan=7>
					Author :'.$author.'
					<b>Total Clearance: K'.$totalClearance.'</b>
					</td>
				</tr>';

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}
		}


		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}

	}

	
	public function manageClearance($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		$role = $this->core->role;

		if($role == 100){		// 1 Lecturer 
			$action = "`ApproverOne` IS NULL";
		} else if($role == 101){	// 2 Library
			$action = "`ApproverTwo` IS NULL";
		} else if($role == 102){	// 3 Accounts
			$action = "`ApproverThree` IS NULL";
		} else if($role == 850){	// 4 ICT
			$action = "`ApproverFour` IS NULL";
		} else if($role == 104){	// 5 HOD
			$action = "`ApproverFive` IS NULL";;
		} else if($role == 105){	// 6 DOS
			$action = "`ApproverSix` IS NULL";
		} else if($role == 107){	// 7 REGISTRAR
			$action = "`ApproverSeven` IS NULL";
		} else if($role == 1000){
			$action = "`clearance`.`status` = 'Pending'";
		}

		$sql = "SELECT *, `clearance`.Status as CS, `clearance`.ID as CID FROM `clearance`, `basic-information` WHERE `basic-information`.ID = `clearance`.UserID AND `clearance`.`Status` = 'Pending' AND $action";		

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Name</b></th>
					<th bgcolor="#EEEEEE" width=""><b>ID</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Balance</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);


		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$cid = $row['CID'];
			$date = $row['CreatedDate'];
			$course = $row['Course'];
			$author = $row['Lname'];
			$clearance = $row['Clearance'];
			$status = $row['CS'];

			$lecturer = $row['ApproverOne'];
			$library = $row['ApproverTwo'];
			$accounts = $row['ApproverThree'];
			$ict = $row['ApproverFour'];
			$hod = $row['ApproverFive'];
			$dos = $row['ApproverSix'];
			$registrar = $row['ApproverSeven'];

			$name = $row['FirstName'] . ' '.  $row['MiddleName'] . ' ' .$row['Surname'];


			$uid = $row['UserID'];

			$balance = $payments->getBalance($uid);


			$edit= '<a href="'. $this->core->conf['conf']['path'] .'/clearance/show/'.$id.'">View clearance details</a>';
			

			$person="";
			if($status == "Pending"){
				$pend = 0;
				if(empty($lecturer)){
					$person .='Lecturer,';
					$pend++;
				}
				if(empty($library)){
					$person .='Hostels,';
					$pend++;
				}
				if(empty($accounts)){
					$person .='Accounts,';
					$pend++;
				}
				if(empty($ict)){
					$person .='ICT,';
					$pend++;
				}
				if(empty($hod)){
					$person .='HOD,';
					$pend++;
				}
				if(empty($dos)){
					$person .='Accommodation,';
					$pend++;
				}
				if(empty($registrar)){
					$person .='Registrar,';
					$pend++;
				}

				$status = 'Pending with ' . $pend . ' offices';

				$person = substr($person, 0, -1);
					
			} else {
				$status =  '<b>' . $status . '</b><br> Author: ' .$author;
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> <b>'.$name.' </b><br> '.$edit.'</td>
				<td> '.$uid.'</td>
				<td> '.$date.'</td>
				<td> K'.$balance.'</td>
				<td> '.$status.' <br><b><a href="'. $this->core->conf['conf']['path'] .'/clearance/approve/'.$cid.'">CLEAR STUDENT</a></b></td>
				</tr>';
			$results = TRUE;


		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}
		}


		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}
	}

	public function requestClearance($item) {
		
		echo 'REQUESTING STUDENT CLEARANCE';

		include $this->core->conf['conf']['formPath'] . "clearance.form.php";
			
	}

	public function saveClearance($item) {

		$userid = $this->core->userID;
		$period = $this->core->cleanPost['period'];

		if($item == ""){
			$sql = "INSERT INTO `clearance` (`ID`, `UserID`, `Status`, `Message`, `CreatedDate`, `ConfirmDate`, `SchoolID`, `ApproverOne`, `ApproverTwo`, `ApproverThree`, `ApproverFour`, `ApproverFive`, `ApproverSix`, `ApproverSeven`, `ApproverOneDate`, `ApproverTwoDate`, `ApproverThreeDate`, `ApproverFourDate`, `ApproverFiveDate`, `ApproverSixDate`, `ApproverSevenDate`, `PeriodID`) 
			VALUES (NULL, '$userid', 'Pending', NULL, NOW(), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$period');";
			$this->core->database->doInsertQuery($sql);
	
			echo '<span class="successpopup">Your clearance request has been created</span>';
	
			$sql = "SELECT `ID` FROM `clearance` WHERE `UserID` = '$userid' AND `Status` = 'Entry'";
			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$cid = $row['ID'];
			}
		}

		
		$this->manageClearance();
	}


	public function approveClearance($item) {

		$userid = $this->core->userID;
		$role = $this->core->role;

		if($role == 100){		// 1 Lecturer 
			$action = "SET `ApproverOne` = '$userid', `ApproverOneDate` = NOW()";
		} else if($role == 101){	// 2 Library
			$action = "SET `ApproverTwo` = '$userid', `ApproverTwoDate` = NOW()";
		} else if($role == 102){	// 3 Accounts
			$action = "SET `ApproverThree` = '$userid', `ApproverThreeDate` = NOW()";
		} else if($role == 850){	// 4 ICT
			$action = "SET `ApproverFour` = '$userid', `ApproverFourDate` = NOW()";
		} else if($role == 104){	// 5 HOD
			$action = "SET `ApproverFive` = '$userid', `ApproverFiveDate` = NOW()";
		} else if($role == 105){	// 6 DOS
			$action = "SET `ApproverSix` = '$userid', `ApproverSixDate` = NOW()";
		} else if($role == 107){	// 7 REGISTRAR
			$action = "SET `ApproverSeven` = '$userid', `ApproverSevenDate` = NOW()";
		} else if($role == 1000){
			$action = "SET `ApproverSeven` = '$userid', `ApproverSevenDate` = NOW()";
		}

		$sql = "UPDATE `clearance` $action WHERE `ID` = '$item';";
		$this->core->database->doInsertQuery($sql);
	
		echo '<span class="successpopup">You have cleared the student</span>';

		$this->manageClearance();
	
	}	

	public function rejectClearance($item) {

		$this->viewMenu();
		$userid = $this->core->userID;

		$sql = "UPDATE `clearance` SET `Message` = '$message' WHERE `ID` = '$item';";
		$this->core->database->doInsertQuery($sql);

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
