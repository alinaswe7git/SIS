<?php
class claim {

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
		$userid = $this->core->userID;
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/claim/manage">Assesment Claims</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/claim/courselecturing/'.$userid.'?type=new">Add Assesment</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/claim/courselecturing/'.$userid.'?type=newlecturing">Add Lecturing</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/claim/managelecturing">Lecturing Claims</a>';
		if($this->core->role == 1000){
			echo '<a href="' . $this->core->conf['conf']['path'] . '/claim/report/Assesment">Report</a>';
		}
		echo '</div>';
	}
	private function reportMenu(){
		$userid = $this->core->userID;
		echo '<div class="toolbar">';
		
		if($this->core->role == 1000){
			echo '<a href="' . $this->core->conf['conf']['path'] . '/claim/report/Lecturing">Lecturing Report</a>';
			echo '<a href="' . $this->core->conf['conf']['path'] . '/claim/report/Assesment">Assesment Report</a>';
		}
		echo '</div>';
		
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$courses = $select->showCourses();
		$schools = $select->showSchools(null);
		$periods = $select->showPeriods(null);
		$claims = $select->showClaimcategory(null);
		
		echo'<form id="narrow" name="narrow" method="get" action="">
			<div class="toolbar">';
		
			echo	'<div ><select name="period" class="submit" style="width: 150px; margin-top: -17px;">
					<option value="">PERIOD</option>
					'. $periods .'
					</select>
					
					<select name="course" class="submit" style="width: 150px; margin-top: -17px;">
					<option value="">COURSE</option>
					'. $courses .'
					</select>
					
					Start: <input type="date" name="start" style="width: 120px; margin-top: -17px;"/>
					End: <input type="date" name="end" style="width: 120px; margin-top: -17px;" />
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/>
				</div>
			</div>
		</form> <br> <hr>';
	}


	public function showClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		$editable = $this->core->cleanGet['edit'];
		
		
		if($this->core->role == 1000 && $item != 'hidden'){
			$sql = "SELECT a.ID,b.ID AS ItemID,a.Status,a.CreatedDate,d.Name as Course,CONCAT(c.Name,'-(K ',c.Rate,')') as Category,CONCAT(e.FirstName,' ',e.Surname) as Lname, (b.NumberOfStudents * c.Rate)
					Claim,a.ApproverOne,a.ApproverTwo,a.ApproverThree,b.NumberOfStudents
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.`Status`  IN ('Pending', 'Entry')
					AND a.ID=b.ClaimID
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
					<th bgcolor="#EEEEEE" width="70px"><b>Claim</b></th>
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
			$claimcategory = $row['Category'];
			$author = $row['Lname'];
			$claim = $row['Claim'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$person="";
			
			if($editable == TRUE){
				$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/edit/'.$id.'">Edit</a></b> <br>
				<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>';
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$claimcategory.'</td>
				<td> '.$course.'</td>
				<td> '.$numberOfStudents.'</td>
				<td> '.$date.'</td>
				<td> K'.$claim.'</td>
				<td> '.$status.'</td>
				</tr>';
			$totalClaim+=$claim;
			$results = TRUE;


		}
		
		echo'<tr>
					<td colspan=7>
					Author :'.$author.'
					<b>Total Claim: K'.$totalClaim.'</b>
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

	
	public function manageClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		
		
		if($this->core->role == 1000 && $item != 'hidden'){
			$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.`Status`  IN ('Pending', 'Entry', 'Approved')
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
		} else if ($this->core->role == 104 && $item != 'hidden'){
			$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.SchoolID IN(SELECT DISTINCT(SchoolID) FROM staff WHERE EmployeeNo='$userid') 
					AND a.`Status`  IN ('Pending', 'Approved')
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
		}else if ($this->core->role == 100 && $item != 'hidden'){
			$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.UserID ='$userid'
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
		}else{
			$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.UserID ='$userid'
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Lecturer</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Claim</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$id = $row['ID'];
			$date = $row['CreatedDate'];
			$course = $row['Course'];
			$author = $row['Lname'];
			$claim = $row['Claim'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$claimType = $row['ClaimType'];
			$lecturer = $row['UserID'];

			if($userid == $lecturer){
				$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/show/'.$id.'?edit=TRUE">Edit claim</a>';
			} else {
				$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/show/'.$id.'">View claim details</a>';
			}

			$person="";
			if($status == "Pending"){
				if(empty($hod)){
					$person='HOD';
				}else if(empty($dean)){
					$person='Dean';
				}else if(empty($dvc)){
					$person='DVC';
				}

				if($item != "hidden"){
					$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/approve/'.$id.'?person='.$person.'&type='.$claimType.'">Awaiting '.$person.' approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>
					Author: ' . $author;
				}
					
			} elseif($status == "Approved") {
				$status =  '<b>' . $status . '</b><br> Author: ' .$author.' </br><a href="'. $this->core->conf['conf']['path'] .'/claim/print/'.$id.'?type='.$claimType.'">Print</a>';
			} else{
				$status =  '<b>' . $status . '</b><br> Author: ' .$author;
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$author.' <br> '.$edit.'</td>
				<td> '.$course.'</td>
				<td> '.$date.'</td>
				<td> K'.$claim.'</td>
				<td> '.$status.'</td>
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
	
	public function managelecturingClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		
		
		if($this->core->role == 1000 && $item != 'hidden'){
			$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
			ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType  
			FROM claims a,`claim-lectures` b,courses d,`basic-information` e 
			WHERE a.`Status` IN ('Pending', 'Entry','Approved') AND a.ClaimType='Lecturing' 
			AND a.ID=b.ClaimID  AND b.CourseID=d.ID AND a.UserID=e.ID 
			GROUP BY b.ClaimID ORDER BY CreatedDate";
		} else if ($this->core->role == 104 && $item != 'hidden'){
			$sql = " SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
			ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType  FROM claims a,
			`claim-lectures` b,courses d,`basic-information` e 
			WHERE a.SchoolID IN(SELECT DISTINCT(SchoolID) 
			FROM staff WHERE EmployeeNo='$userid') AND a.`Status` IN ('Pending','Approved')
			AND a.ClaimType='Lecturing' AND a.ID=b.ClaimID  AND b.CourseID=d.ID AND a.UserID=e.ID 
			GROUP BY b.ClaimID ORDER BY CreatedDate";
		}else if ($this->core->role == 100 && $item != 'hidden'){
			$sql = " SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
			ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType  
			FROM claims a, `claim-lectures` b,courses d,`basic-information` e 
			WHERE a.UserID ='$userid' AND a.ClaimType='Lecturing' AND a.ID=b.ClaimID  AND b.CourseID=d.ID AND a.UserID=e.ID 
			GROUP BY b.ClaimID ORDER BY CreatedDate";
		}else{
			$sql = " SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
			ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
			FROM claims a, `claim-lectures` b,courses d,`basic-information` e 
			WHERE a.UserID ='$userid' AND a.ClaimType='Lecturing' AND a.ID=b.ClaimID  AND b.CourseID=d.ID AND a.UserID=e.ID 
			GROUP BY b.ClaimID ORDER BY CreatedDate";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Lecturer</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Hours</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$id = $row['ID'];
			$date = $row['CreatedDate'];
			$course = $row['Course'];
			$author = $row['Lname'];
			$claim = $row['Hours'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$claimType = $row['ClaimType'];
			$lecturer = $row['UserID'];

			if($userid == $lecturer){
				$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/viewlecturing/'.$id.'?edit=TRUE">Edit claim</a>';
			} else {
				$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/viewlecturing/'.$id.'">View claim details</a>';
			}

			$person="";
			if($status == "Pending"){
				if(empty($hod)){
					$person='HOD';
				}else if(empty($dean)){
					$person='Dean';
				}else if(empty($dvc)){
					$person='DVC';
				}

				if($item != "hidden"){
					$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/approve/'.$id.'?person='.$person.'&type='.$claimType.'">Awaiting '.$person.' approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>
					Author: ' . $author;
				}
					
			} elseif($status == "Approved") {
				$status =  '<b>' . $status . '</b><br> Author: ' .$author.' </br><a href="'. $this->core->conf['conf']['path'] .'/claim/print/'.$id.'?type='.$claimType.'">Print</a>';
			} else{
				$status =  '<b>' . $status . '</b><br> Author: ' .$author;
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$author.' <br> '.$edit.'</td>
				<td> '.$course.'</td>
				<td> '.$date.'</td>
				<td> '.$claim.'</td>
				<td> '.$status.'</td>
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
	public function newClaim($item) {

		//if($item != ''){
		//	$this->manageClaim("hidden");
		//} else {
			$this->viewMenu();
		//}


		$userid = $this->core->userID;
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$courses = $select->showClaimCourses($userid);


		$schools = $select->showSchools(null);
		$periods = $select->showPeriods(null);
		//$claims = $select->showClaimcategory(null);

		//if($item == ''){
			$button = 'Create Claim';
		//} else {
		//	$button = 'Update Claim';
		//	$submit = '</p><button onclick="' . $this->core->conf['conf']['path'] .'/claim/submit/'.$item.'" class="submit">FINALIZE SUBMISSION</button>';
		//}

		include $this->core->conf['conf']['formPath'] . "newclaim.form.php";
	
	}
	public function newlecturingClaim($item) {

		//if($item != ''){
		//	$this->manageClaim("hidden");
		//} else {
			$this->viewMenu();
		//}


		$userid = $this->core->userID;
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$courses = $select->showClaimCourses($userid);


		$schools = $select->showSchools(null);
		$periods = $select->showPeriods(null);
		//$claims = $select->showClaimcategory(null);

		//if($item == ''){
			$button = 'Create Claim';
		//} else {
		//	$button = 'Update Claim';
		//	$submit = '</p><button onclick="' . $this->core->conf['conf']['path'] .'/claim/submit/'.$item.'" class="submit">FINALIZE SUBMISSION</button>';
		//}

		include $this->core->conf['conf']['formPath'] . "newlecturingclaim.form.php";
	
	}
	
	public function addsessionClaim($item) {
		
		if ($item < 85){
			
			for($i=0; $i < $item ;$i++){
				$c =$i+1;
				echo '<tr><td><b>Session '.$c.'</b></td></tr>';
				echo '<tr>
					<td>Time in : <input name="timein'.$i.'" type="time" value=""  min="06:00" max="21:00" required/> </td>
					<td>Time out :<input name="timeout'.$i.'" type="time" value="" min="06:00" max="21:00" required/> </td>
					<td>Date :<input name="lecturedate'.$i.'" type="date" value=""  required/> </td>
					</tr>
					';
				echo '<tr><td></td></tr>';
			
			}
		}else{
			echo 'Too many sessions for a single semester';
		}
		
	}
	public function addclaimitemClaim($item) {
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$claims = $select->showClaimcategory(null);
		if ($item < 4){
			
			for($i=0; $i < $item ;$i++){
				$c =$i+1;
				echo '<tr>
					<td>Item '.$c.'</td>
					<td><select name="category'.$i.'" id="category'.$i.'">
							'.$claims.'
						</select></td>
					<td></td>
				</tr>';
			
			}
		}else{
			echo 'Too many sessions for a single semester';
		}
		
	}
	public function addlecturecourseClaim($item) {
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$claims = $select->showCourses(null);
		if ($item < 8){
			
			for($i=0; $i < $item ;$i++){
				$c =$i+1;
				echo 'Course '.$c.'  :
						<select name="course'.$i.'" id="course'.$i.'" style="width: 250px">
							'.$claims.'
						</select></br>';
			
			}
		}else{
			echo 'Too many courses for a single lecturer';
		}
		
	}
	public function editsessionClaim($item) {
		
			$session = $this->core->cleanGet["session"];
			$claim = $this->core->cleanGet["claim"];
			
			$sqld = "SELECT * FROM `claim-lectures` WHERE `ID` = '$item' ";
			$rund = $this->core->database->doSelectQuery($sqld);
			
			while ($rowd = $rund->fetch_assoc()) {
				$cid = $rowd['ID'];
				$timein = $rowd['TimeIN'];
				$timeout = $rowd['TimeOUT'];
				$lectureDate = $rowd['LectureDate'];
				$courseID = $rowd['CourseID'];
				$students = $rowd['NumberOfStudents'];
				
				echo '<form id="editsession" name="editsession" method="post" action="'.$this->core->conf['conf']['path'] .'/claim/savesession/'.$cid.'?update=1&claim='.$claim.'">';
				echo '<table><tr><td><b>Session '.$session.'</b></td></tr>';
				echo '<tr>
					<td>Time in : <input name="timein" type="time" value="'.$timein.'"  min="06:00" max="21:00" required/> </td>
					<td>Time out :<input name="timeout" type="time" value="'.$timeout.'" min="06:00" max="21:00" required/> </td>
					<td>Date :<input name="lecturedate" type="date" value="'.$lectureDate.'"  required/> </td>
					</tr>
					';
				echo '<tr><td></td></tr></table>';
				echo '<button onclick="' . $this->core->conf['conf']['path'] .'/claim/savesession/'.$cid.'?update=1&claim='.$claim.'" class="submit">Save</button>';	
				echo '</form>';
			}
			
				
		
	}
	public function editClaim($item) {

		$userid = $this->core->userID;
		$sql = "SELECT * FROM `claims` WHERE ID = '$item' AND `Status` = 'Entry'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$id = $row['ID'];
		}
			
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$courses = $select->showClaimCourses($userid);
		$schools = $select->showSchools(null);
		$periods = $select->showPeriods(null);
		$claims = $select->showClaimcategory(null);

		if($item == ''){
			$button = 'Create Claim';
		} else {
			$button = 'Update Claim';
			//$submit = '</p><button onclick="' . $this->core->conf['conf']['path'] .'/claims/submit" class="submit">viewlecturing</button>';
		}

		include $this->core->conf['conf']['formPath'] . "editclaim.form.php";
	
	}

	public function submitClaim($item) {
		
		if (isset($_GET['type'])){
			$type    = $this->core->cleanGet["type"];
		}
		
		$sql = "UPDATE `claims` SET `Status`='Pending' WHERE `ID` = '$item';";
		$this->core->database->doInsertQuery($sql);
		
		if ($type == 'Lecturing'){
			$this->managelecturingClaim();
		}else{
			$this->manageClaim();
		}
	}

	public function saveClaimItem($item,$student=NULL) {

		$userid   = $this->core->userID;
		if (isset($_GET['update'])){
			$course     = $this->core->cleanPost['course'];
			$students   = $this->core->cleanPost['students'];
			$mode       = $this->core->cleanPost['delivery'];
			$category   = $this->core->cleanPost['category']; 

			$sql = "INSERT INTO `claim-items` SELECT NULL, '$item', '$category', '$course', '$mode', '$students', `Rate` FROM `claim-category` WHERE `ID` = '$category'";
			$this->core->database->doInsertQuery($sql);
			
		}else{
			$course     = $this->core->cleanPost['course'];
			$mode       = $this->core->cleanPost['delivery'];
			$sessions   = $this->core->cleanPost['sessions'];
			
			if ($student == NULL ){
				$students   = $this->core->cleanPost['students'];
			}else{
				$students   = $student;
			}
			
			for($i=0; $i < $sessions; $i++){
				
				$category   = $this->core->cleanPost["category$i"]; 
				
				$sql = "INSERT INTO `claim-items` SELECT NULL, '$item', '$category', '$course', '$mode', '$students', `Rate` FROM `claim-category` WHERE `ID` = '$category'";
				$this->core->database->doInsertQuery($sql);
			}
		}
		return;
	}
	
	public function savelecturercourseClaim($item) {

		$type  = '';
		
		if (isset($_GET['type'])){
			$type  = $this->core->cleanGet["type"];
		}
		
		$userid   = $this->core->userID;
		$sessions   = $this->core->cleanPost['sessions'];
					
		for($i=0; $i < $sessions; $i++){
			
			$course   = $this->core->cleanPost["course$i"]; 
			
			$sql = "INSERT INTO `claim-lecturer-course`(`LecturerID`, `CourseID`, `DateTime`, `UserID`) VALUES ('$userid' , '$course', NOW(), '$userid')";
			$this->core->database->doInsertQuery($sql);
		}
		echo '<span class="successpopup">Your courses have been updated</span>';
		
		$this->courselecturingClaim($userid);
	}
	public function savesessionClaim($item,$student=NULL) {
		
		$userid   = $this->core->userID;
		if (isset($_GET['update'])){
					
			$cid    = $this->core->cleanGet["claim"];
			
			$timein     = $this->core->cleanPost["timein"];
			$timeout   = $this->core->cleanPost["timeout"];
			$lecturedate = $this->core->cleanPost["lecturedate"];
			
			//echo $timein.'</br>'.$item ;
			
			
			$sql = "UPDATE `claim-lectures` SET `TimeIN` ='$timein' , `TimeOUT`='$timeout', `LectureDate`='$lecturedate' WHERE ID = $item ";
			$this->core->database->doInsertQuery($sql);
			
			echo '<span class="successpopup">You claim has been updated</span>';
			
			$this->viewlecturingClaim($cid);
		
		}else{
			$course     = $this->core->cleanPost['course'];
			$sessions   = $this->core->cleanPost['sessions'];
			if ($student == NULL ){
				$students   = $this->core->cleanPost['students'];
			}else{
				$students   = $student;
			}
			
			
			for($i=0; $i < $sessions; $i++){
				$timein     = $this->core->cleanPost["timein$i"];
				$timeout   = $this->core->cleanPost["timeout$i"];
				$lecturedate       = $this->core->cleanPost["lecturedate$i"];
				
				//echo $timein.'</br>';
				
				$sql = "INSERT INTO `claim-lectures`(`ClaimID`, `TimeIN`, `TimeOUT`, `LectureDate`, `CourseID`, `NumberOfStudents`) 
				VALUES ('$item', '$timein', '$timeout', '$lecturedate', $course, $students)";
				$this->core->database->doInsertQuery($sql);
			}
			return;
		}
	}

	public function saveClaim($item) {

		$userid = $this->core->userID;
		$school = $this->core->cleanPost['school'];
		$period = $this->core->cleanPost['period'];
		$name   = $this->core->cleanPost['name'];
		$claimtype   = $this->core->cleanPost['claimtype'];

		$students = $this->checkstudentsClaim($courseid);
		
		$sql = "INSERT INTO `claims` (`ID`, `UserID`, `Status`, `CreatedDate`, `ConfirmDate`, `SchoolID`, `ApproverOne`, `ApproverTwo`, `ApproverThree`, `ApproverOneDate`, `ApproverTwoDate`, `ApproverThreeDate`, `Username`, `PeriodID` ,`ClaimType`) 
		VALUES (NULL, '$userid', 'Entry', NOW(), NULL, '$school', NULL, NULL, NULL, NULL, NULL, NULL, '$name', '$period','$claimtype');";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">You claim has been added</span>';

		$sql = "SELECT `ID` FROM `claims` WHERE `UserID` = '$userid' AND `Status` = 'Entry'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$cid = $row['ID'];
		}
		$this->saveClaimItem($cid,$students);
		
		$this->viewClaim($cid);
		
	}
	public function savelecturingClaim($item) {

		$userid = $this->core->userID;
		$school = $this->core->cleanPost['school'];
		$period = $this->core->cleanPost['period'];
		$name   = $this->core->cleanPost['name'];
		$sessions   = $this->core->cleanPost['sessions'];
		$claimtype   = $this->core->cleanPost['claimtype'];
		$courseid   = $this->core->cleanPost['course'];
		
		$students = $this->checkstudentsClaim($courseid);

		$sql = "INSERT INTO `claims` (`ID`, `UserID`, `Status`, `CreatedDate`, `ConfirmDate`, `SchoolID`, `ApproverOne`, `ApproverTwo`, `ApproverThree`, `ApproverOneDate`, `ApproverTwoDate`, `ApproverThreeDate`, `Username`, `PeriodID`,`ClaimType`) 
		VALUES (NULL, '$userid', 'Entry', NOW(), NULL, '$school', NULL, NULL, NULL, NULL, NULL, NULL, '$name', '$period','$claimtype');";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">You claim has been added</span>';

		$sql = "SELECT `ID` FROM `claims` WHERE `UserID` = '$userid' AND `Status` = 'Entry'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$cid = $row['ID'];
		}

		$this->savesessionClaim($cid,$students);
	
		$this->viewlecturingClaim($cid);

	}
	
	public function courselecturingClaim($item) {
			
			$type  = '';
			
			if (isset($_GET['type'])){
				$type  = $this->core->cleanGet["type"];
			}
			
			echo "<H3>Your courses are listed below, if no courses are listed or incorrect courses are listed, please <b>remove or add</b> correct information before proceeding to add.</H3></br>";
			
			echo "<table><thead><tr><th>#</th><th>Course</th><th>Date/Time</th><th>Added by</th><th>Action</th></tr></thead>";
		
			$sqld = "SELECT a.*,(SELECT CONCAT (CourseDescription,' (',Name,')') FROM courses WHERE ID=a.CourseID) as Course,
			(SELECT CONCAT (FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.LecturerID) as User,
			(SELECT CONCAT (FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.UserID) as UserAdd
 			 FROM `claim-lecturer-course` a WHERE a.`LecturerID` = '$item' ";
			$rund = $this->core->database->doSelectQuery($sqld);
			$i=1;
			$output='';
			if ($rund->num_rows > 0 ){
				while ($rowd = $rund->fetch_assoc()) {
					$cid = $rowd['ID'];
					$date = $rowd['DateTime'];
					$course = $rowd['Course'];
					$user = $rowd['User'];
					$userAdd = $rowd['UserAdd'];
					
					$output.= "<tr><td>$i</td><td><b>$course</b></td><td>$date</td><td>$userAdd</td>
					<td><a href='".$this->core->conf['conf']['path'] .'/claim/lecturedelete/'.$cid."?uid=".$item."&type=".$type."'>Remove</a></td>
					</tr>";
					$i++;
				}
			}else{
				
				$output.= "<tr><td colspan=5><b>No data found please add some information before proceeding</b></td>
					</tr>";
			}
			echo $output;
			echo "</tbody></table>";
			
			echo "</br></br><H3><b>Add</b> more courses using the form below</H3></br>";
			
			echo '<form id="savelecturercourse" name="savelecturercourse" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/savelecturercourse/'.$item.'?type='.$type.'">';
			
			include $this->core->conf['conf']['formPath'] . "lecturercourseclaim.form.php";
			
			echo '<button onclick="' . $this->core->conf['conf']['path'] .'/claim/savelecturercourse/'.$item.'?type='.$type.'" class="submit">Add Courses</button>';
			echo '</form>';
			echo '<form id="submit" name="submit" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/'.$type.'/'.$item.'">';
			echo '<button onclick="' . $this->core->conf['conf']['path'] .'/claim/'.$type.'/'.$item.'?type='.$type.'" class="submit">Proceed to adding</button>';
			echo "</form>";
	}
	
	public function viewlecturingClaim($item) {

			echo "<table><thead><tr><th>School </th><th>Period </th></tr></thead>";
			
			$sql = "SELECT a.*,(SELECT Description FROM schools WHERE ID=a.SchoolID) as School,
			(SELECT CONCAT(Year,'-',Name) FROM periods WHERE ID=a.PeriodID) as Period
			FROM `claims` a WHERE a.`ID` = '$item'";
			
			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$id = $row['ID'];
				$userID = $row['UserID'];
				$status = $row['Status'];
				$schoolID = $row['SchoolID'];
				$periodID = $row['PeriodID'];
				$school = $row['School'];
				$period= $row['Period'];
				$type= $row['ClaimType'];
			}
			echo "<tbody><tr><td>$school</td><td>$period</td></tr>";
			echo "<tr><td colspan='2'></td></tr>";
			
		
			$sqld = "SELECT a.*,(SELECT CONCAT (CourseDescription,' (',Name,')') FROM courses WHERE ID=a.CourseID) as Course
 			 FROM `claim-lectures` a WHERE a.`ClaimID` = '$item' ";
			$rund = $this->core->database->doSelectQuery($sqld);
			$i=1;
			$top='';
			$output='';
			while ($rowd = $rund->fetch_assoc()) {
				$cid = $rowd['ID'];
				$timein = $rowd['TimeIN'];
				$timeout = $rowd['TimeOUT'];
				$lectureDate = $rowd['LectureDate'];
				$course = $rowd['Course'];
				$students = $rowd['NumberOfStudents'];
				$claimID = $rowd['ClaimID'];
				
				
				$top = "<tr><td colspan=4>Course: <b>$course</b> Status: <b>$status</b> </td></tr>";
				
				$output.= "<tr><td><b>Session $i </b> having <b>$students students </b></td><td>$timein</td><td>$timeout</td><td>$lectureDate</td>
				<td><a href='".$this->core->conf['conf']['path'] .'/claim/editsession/'.$cid."?claim=".$item."&session=".$i."'>Edit</a></td>
				</tr>";
				$i++;
			}
			echo $top;
			echo "<tr><td><b>Session</b></td><td><b>Time in</b></td><td><b>Time Out</b></td><td><b>Date</b></td><td><b>Action</b></td></tr>";
			echo $output;
			echo "</tbody></table>";
			
			if(isset($_GET['person'])){
				echo '<form id="cancelsession" name="cancelsession" method="post" action="'.$this->core->conf['conf']['path'].'/claim/process/'.$item.'?response=reject&type=Lecturing">';
				echo '<button class="submit">Reject</button>';
				echo '</form>';
				echo '<form id="submit" name="submit" method="post" action="'. $this->core->conf['conf']['path'] .'/claim/process/'.$item.'?response=accept&type=Lecturing">';
				echo '<button class="submit">Approve & Print</button>';
				echo "</form>";
			
			}else{
				echo '<form id="cancelsession" name="cancelsession" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/delete/'.$item.'?type='.$type.'">';
				echo '<button onclick="' . $this->core->conf['conf']['path'] .'/claim/delete/'.$item.'?type='.$type.'" class="submit">CANCEL SUBMISSION</button>';
				echo '</form>';
				echo '<form id="submit" name="submit" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/submit/'.$item.'">';
				echo '<button onclick="' . $this->core->conf['conf']['path'] .'/claim/submit/'.$item.'?type='.$type.'" class="submit">FINALIZE SUBMISSION</button>';
				echo "</form>";
				
			}
	}
	public function viewClaim($item) {
		

		$sql = "SELECT a.ID,b.ID AS ItemID,a.Status,a.CreatedDate,d.Name as Course,CONCAT(c.Name,'-(K ',c.Rate,')') as Category,CONCAT(e.FirstName,' ',e.Surname,'(',e.ID,')') as Lname, (b.NumberOfStudents * c.Rate)
				Claim,a.ApproverOne,a.ApproverTwo,a.ApproverThree,b.NumberOfStudents
				FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
				WHERE a.`Status`  IN ('Pending', 'Entry')
				AND a.ID=b.ClaimID
				AND a.ID = '$item'
				AND b.CategoryID=c.ID
				AND b.CourseID=d.ID
				AND a.UserID=e.ID
				ORDER BY CreatedDate";

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
					<th bgcolor="#EEEEEE" width="70px"><b>Claim</b></th>
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
			$claimcategory = $row['Category'];
			$author = $row['Lname'];
			$claim = $row['Claim'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$person="";
			
			if($editable == TRUE){
				$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/edit/'.$id.'">Edit</a></b> <br>
				<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>';
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$claimcategory.'</td>
				<td> '.$course.'</td>
				<td> '.$numberOfStudents.'</td>
				<td> '.$date.'</td>
				<td> K'.$claim.'</td>
				<td> '.$status.'</td>
				</tr>';
			$totalClaim+=$claim;
			$results = TRUE;


		}
		
		echo'<tr>
					<td colspan=7>
					Author :'.$author.'
					<b>Total Claim: K'.$totalClaim.'</b>
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
		if(isset($_GET['person'])){
			echo '<form id="cancelsession" name="cancelsession" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/process/'.$item.'?response=reject&type=Assesment">';
			echo '<button  class="submit">Reject</button>';
			echo '</form>';
			echo '<form id="submit" name="submit" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/process/'.$item.'?response=accept&type=Assesment">';
			echo '<button  class="submit">Approve & Print</button>';
			echo "</form>";
			
		}else{
			echo '<form id="cancelsession" name="cancelsession" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/delete/'.$item.'?type='.$type.'">';
			echo '<button  class="submit">CANCEL SUBMISSION</button>';
			echo '</form>';
			echo '<form id="submit" name="submit" method="post" action="' . $this->core->conf['conf']['path'] .'/claim/submit/'.$item.'?type='.$type.'">';
			echo '<button  class="submit">FINALIZE SUBMISSION</button>';
			echo "</form>";
			
		}
		
	}
	
	public function approveClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		
		if (isset($_GET['type'])){
			$type  = $this->core->cleanGet["type"];
			$person  = $this->core->cleanGet["person"];
		}
		
		echo $type.' '.$person;
		
		if ($type=='Lecturing'){
			$this->viewlecturingClaim($item);
		}elseif($type=='Assesment'){
			$this->viewClaim($item);
		}
	
	}	
	
	public function processClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		
		if (isset($_GET['response'])){
			$type  = $this->core->cleanGet["type"];
			$response  = $this->core->cleanGet["response"];
		}
				
		if ($response=='accept'){
			
			$sql1 = "UPDATE `claims` SET `Status`='Approved', ApproverOne='$userid', ApproverOneDate = Now() WHERE ID = $item";
			$result1 = $this->core->database->doInsertQuery($sql1);
			


			$this->printClaim($item);
			
		}elseif($response=='reject'){
			
			$sql1 = "UPDATE `claims` SET `Status`='Entry' , ApproverOne='$userid', ApproverOneDate = Now() WHERE ID = $item";
			$result1 = $this->core->database->doInsertQuery($sql1);
			
			if($type=='Lecturing'){
				
				$this->managelecturingClaim();
				
			}elseif($type=='Assesment'){
				
				$this->manageClaim();
			}
						
		}
	
	}

	public function printClaim($item) {

		if (isset($_GET['type'])){
			$type  = $this->core->cleanGet["type"];
		}
		
		
	
		echo'<div id="printablediv">
		<table width="100%"><tr><td colspan=3><center><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /><br>
		<font size=5>'.$this->core->conf['conf']['organization'].'</font><br>
		<font size=4>Pursuing the frontiers of knowledge</font></center>
		</td></tr>
		<tr><td>Fax: +260 215 228003<br>Tel: +260 215 228004<br> Email: registrar@mu.ac.zm</td><td align="right">
		Great North Road<br>
		P O Box 80415<br>
		<b> KABWE</td></tr>
		<tr><td colspan="3"><hr size=2></td></tr>
		</table>';
		
		if($type=='Lecturing'){
			$top='';
			$output='';
			
			
			$sql = "SELECT a.*,a.ApproverOneDate as ApproverOneDate,
			(SELECT Description FROM schools WHERE ID=a.SchoolID) as School,
			(SELECT CONCAT(Year,'-',Name) FROM periods WHERE ID=a.PeriodID) as Period,
			CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.UserID),' Create Date: ',a.CreatedDate) as author,
			CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.ApproverOne),' Approval Date: ',a.ApproverOneDate) as approver
			FROM `claims` a WHERE a.`ID` = '$item'";
			
			$run = $this->core->database->doSelectQuery($sql);
			while ($row = $run->fetch_assoc()) {
				$id = $row['ID'];
				$userID = $row['UserID'];
				$status = $row['Status'];
				$schoolID = $row['SchoolID'];
				$periodID = $row['PeriodID'];
				$school = $row['School'];
				$author = $row['author'];
				$approver = $row['approver'];
				$period= $row['Period'];
				$type= $row['ClaimType'];
			}
			$top.= '<table id="results" class="table table-bordered table-striped table-hover" ><thead><tr><th>School </th><th>Period </th></tr></thead>';
			$top.= "<tbody><tr><td>$school</td><td>$period</td></tr>";
			$top.= "<tr><td colspan='2'></td></tr>";
			
		
			$sqld = "SELECT a.*,(SELECT CONCAT (CourseDescription,' (',Name,')') FROM courses WHERE ID=a.CourseID) as Course,ABS(ROUND((TIMEDIFF(a.TimeIN, a.TimeOUT)) / 10000)) as Hours
 			 FROM `claim-lectures` a WHERE a.`ClaimID` = '$item' ";
			$rund = $this->core->database->doSelectQuery($sqld);
			$i=1;
			$hrs=0;
			
			while ($rowd = $rund->fetch_assoc()) {
				$cid = $rowd['ID'];
				$timein = $rowd['TimeIN'];
				$timeout = $rowd['TimeOUT'];
				$hours = $rowd['Hours'];
				$lectureDate = $rowd['LectureDate'];
				$course = $rowd['Course'];
				$students = $rowd['NumberOfStudents'];
				$claimID = $rowd['ClaimID'];
				
				
				
				$hrs+=$hours;
				$output.= "<tr><td><b>Session $i </b> having <b>$students students </b></td><td>$timein</td><td>$timeout</td><td>$lectureDate : $hours hrs</td>
				</tr>";
				$i++;
			}
			$top .= "<tr><td colspan=4>Course: <b>$course</b> Status: <b>$status</b> </td></tr>";
			echo $top;
			echo "<tr><td><b>Session</b></td><td><b>Time in</b></td><td><b>Time Out</b></td><td><b>Date</b></td></tr>";
			
			echo $output;
			echo "<tr><td colspan=4><b>Total Hours: </b> $hrs</td></tr>";
			
			echo'<tr><td colspan=7>Author :'.$author.'</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Approved By :'.$approver.'</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Signature :_______________________________________________________</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Dean Signature By :_______________________________________________________</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>DVC Signature By :_______________________________________________________</td></tr>';
			echo "</tbody></table>";
			
				
		}elseif($type=='Assesment'){
			
			$top='';
			$mid='';
			$output='';
			
			$sql = "SELECT a.ID,b.ID AS ItemID,a.Status,a.CreatedDate,d.Name as Course,CONCAT(c.Name,'-(K ',c.Rate,')') as Category,CONCAT(e.FirstName,' ',e.Surname,' (',e.ID,')' ,' Create Date: ',a.CreatedDate) as Lname, (b.NumberOfStudents * c.Rate)Claim,a.ApproverOne,a.ApproverTwo,a.ApproverThree,b.NumberOfStudents,
			(SELECT Description FROM schools WHERE ID=a.SchoolID) as School,
			(SELECT CONCAT(Year,'-',Name) FROM periods WHERE ID=a.PeriodID) as Period,
			CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.ApproverOne),' Approval Date: ',a.ApproverOneDate) as approver
				FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
				WHERE a.`Status`  IN ('Pending', 'Entry','Approved')
				AND a.ID=b.ClaimID
				AND a.ID = '$item'
				AND b.CategoryID=c.ID
				AND b.CourseID=d.ID
				AND a.UserID=e.ID
				ORDER BY CreatedDate";

			$run = $this->core->database->doSelectQuery($sql);

			if(!isset($this->core->cleanGet['offset'])){
				$mid .='<table id="results" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th bgcolor="#EEEEEE" width="30px"></th>
						<th bgcolor="#EEEEEE" width=""><b>Item</b></th>
						<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
						<th bgcolor="#EEEEEE" width="70px"><b>Students</b></th>
						<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
						<th bgcolor="#EEEEEE" width="70px"><b>Claim</b></th>
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
				$claimcategory = $row['Category'];
				$school = $row['School'];
				$period = $row['Period'];
				$author = $row['Lname'];
				$claim = $row['Claim'];
				$status = $row['Status'];
				$hod = $row['ApproverOne'];
				$approver = $row['approver'];
				$dean = $row['ApproverTwo'];
				$dvc = $row['ApproverThree'];
				$person="";
				
				if($editable == TRUE){
					$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/edit/'.$id.'">Edit</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>';
				}
									
				$mid .='<tr>
					<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
					<td> '.$claimcategory.'</td>
					<td> '.$course.'</td>
					<td> '.$numberOfStudents.'</td>
					<td> '.$date.'</td>
					<td> K'.$claim.'</td>
					<td> '.$status.'</td>
					</tr>';
				$totalClaim+=$claim;
				$results = TRUE;


			}
			$top.= '<table id="results" class="table table-bordered table-striped table-hover" ><thead><tr><th>School </th><th>Period </th></tr></thead>';
			$top.= "<tbody><tr><td>$school</td><td>$period</td></tr></tbody></table>";
			echo $top;
			echo $mid;
			echo'<tr><td colspan=7>Author :'.$author.'  <b>Total Claim: K'.$totalClaim.'</b></td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Approved By :'.$approver.'</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Signature :_______________________________________________________</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>Dean Signature By :_______________________________________________________</td></tr>';
			echo'<tr><td colspan=7></td></tr>';
			echo'<tr><td colspan=7>DVC Signature By :_______________________________________________________</td></tr>';

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
		echo '<form> 
        <input type="button" value="Print" 
               onclick="window.print()" /> 
		</form>';
		
		
	}	
	
	public function approveviewClaim($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/approve/'.$id.'?person='.$person.'">Awaiting '.$person.' approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>
					Author: ' . $author;
		
	
	}
	public function deleteClaim($item) {

		if (isset($_GET['type'])){
			$type    = $this->core->cleanGet["type"];
		}else {
			$type    = 'Assesment';
		}
		
		if ($type == 'Lecturing'){
				
			
			$sql1 = "DELETE FROM `claims` WHERE ID = $item";
			
			$result1 = $this->core->database->doInsertQuery($sql1);
			
			$sql2 = "DELETE FROM `claim-lectures`  WHERE ClaimID = $item ";
			
			$result2 = $this->core->database->doInsertQuery($sql2);
			
			if ($result1 && $result2) {
				echo '<span class="successpopup">Your claim has been cancelled</span>';
				
			}else{
				
				echo '<span class="failure">Your claim failed to cancel</span>';
			}
			
			$this->viewMenu();
		
		}else{
				
			//echo $item;
			$sql1 = "DELETE FROM `claims` WHERE ID = $item";
			$result1 = $this->core->database->doInsertQuery($sql1);
			
			$sql2 = "DELETE FROM `claim-items`  WHERE ClaimID = $item ";
			$result2 = $this->core->database->doInsertQuery($sql2);
			
			if ($result1 && $result2) {
				echo '<span class="successpopup">Your claim has been cancelled</span>';
				
			}else{
				
				echo '<span class="failure">Your claim failed to cancel</span>';
			}
			
			$this->viewMenu();
			
		}
	
	}
	public function lecturedeleteClaim($item) {

		if (isset($_GET['type']) && isset($_GET['uid'])){
			$type    = $this->core->cleanGet["type"];
			$uid    = $this->core->cleanGet["uid"];
		}
		
			$sql1 = "DELETE FROM `claim-lecturer-course` WHERE ID = $item";
			
			$result1 = $this->core->database->doInsertQuery($sql1);
			
			echo '<span class="successpopup">Course has been removed</span>';
			$this->courselecturingClaim($uid);	
	}
	
	public function checkstudentsClaim($item) {

		$userid = $this->core->userID;
		$number = 0;
		$studentcount = 0;
		$students = $this->core->cleanPost['students'];
		$period = $this->core->cleanPost['period'];
		$delivery = $this->core->cleanPost['delivery'];
		
		$sql = "SELECT COUNT(DISTINCT StudentID) as ID FROM `course-electives` a, `basic-information` b 
		WHERE a.StudentID = b.ID AND b.StudyType= '$delivery' AND a.`CourseID` = '$item' AND a.`PeriodID` = '$period'";
		$run = $this->core->database->doSelectQuery($sql);
		
		while ($row = $run->fetch_assoc()) {
			$studentcount = $row['ID'];
		}
		if($students >= $studentcount){
			$number = $studentcount;
		}else{
			$number = $students;
		}
		
		return $number;
	}
	
	
	public function reportClaim($item) {

		$this->viewMenu();
		$this->reportMenu();
		$userid = $this->core->userID;
		
		if (!empty($_GET['period'])){
			$period = $_GET['period'];
		}
		if (!empty($_GET['course'])){
			$course = $_GET['course'];
		}
		if (!empty($_GET['start'])){
			$start = $_GET['start'];
		}
		if (!empty($_GET['end'])){
			$end = $_GET['end'];
		}
		
		if ($item == 'Assesment'){
			if (!empty($_GET['period'])){
				
				$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.`Status`  IN ('Pending', 'Entry', 'Approved')
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					AND a.PeriodID=$period 
					AND b.CourseID=$course
					AND a.CreatedDate BETWEEN $start AND $end
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
			}else{
				$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,'-',c.Name)as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname, SUM(b.NumberOfStudents * c.Rate) as Claim, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType 
					FROM claims a, `claim-items` b,`claim-category` c,courses d,`basic-information` e
					WHERE a.`Status`  IN ('Pending', 'Entry', 'Approved')
					AND a.ID=b.ClaimID
					AND b.CategoryID=c.ID
					AND b.CourseID=d.ID
					AND a.UserID=e.ID
					GROUP BY b.ClaimID
					ORDER BY CreatedDate";
			}
		
		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Lecturer</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Claim</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$id = $row['ID'];
			$date = $row['CreatedDate'];
			$course = $row['Course'];
			$author = $row['Lname'];
			$claim = $row['Claim'];
			$status = $row['Status'];
			$hod = $row['ApproverOne'];
			$dean = $row['ApproverTwo'];
			$dvc = $row['ApproverThree'];
			$claimType = $row['ClaimType'];
			$lecturer = $row['UserID'];

			$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/show/'.$id.'">View claim details</a>';
			

			$person="";
			if($status == "Pending"){
				if(empty($hod)){
					$person='HOD';
				}else if(empty($dean)){
					$person='Dean';
				}else if(empty($dvc)){
					$person='DVC';
				}

				if($item != "hidden"){
					$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/approve/'.$id.'?person='.$person.'&type='.$claimType.'">Awaiting '.$person.' approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>
					Author: ' . $author;
				}
					
			} elseif($status == "Approved") {
				$status =  '<b>' . $status . '</b><br> Author: ' .$author.' </br><a href="'. $this->core->conf['conf']['path'] .'/claim/print/'.$id.'?type='.$claimType.'">Print</a>';
			} else{
				$status =  '<b>' . $status . '</b><br> Author: ' .$author;
			}
								
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$author.' <br> '.$edit.'</td>
				<td> '.$course.'</td>
				<td> '.$date.'</td>
				<td> K'.$claim.'</td>
				<td> '.$status.'</td>
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
			
		}else{
			if (!empty($_GET['period'])){
				
				$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
				ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType  
				FROM claims a,`claim-lectures` b,courses d,`basic-information` e 
				WHERE a.`Status` IN ('Pending', 'Entry','Approved') 
				AND a.ClaimType='Lecturing' 
				AND a.ID=b.ClaimID  
				AND b.CourseID=d.ID 
				AND a.UserID=e.ID 
				AND a.PeriodID=$period 
				AND b.CourseID=$course
				AND a.CreatedDate BETWEEN $start AND $end
				GROUP BY b.ClaimID ORDER BY CreatedDate";
			
			}else{
				$sql = "SELECT a.ID,a.Status,a.CreatedDate,CONCAT(d.Name,' (',b.NumberOfStudents,')')as Course,CONCAT(e.FirstName,' ',e.Surname) as Lname,
				ABS(ROUND(SUM(TIMEDIFF(b.TimeIN, b.TimeOUT)) / 10000)) as Hours, a.UserID, a.ApproverOne,a.ApproverTwo,a.ApproverThree,a.ClaimType  
				FROM claims a,`claim-lectures` b,courses d,`basic-information` e 
				WHERE a.`Status` IN ('Pending', 'Entry','Approved') AND a.ClaimType='Lecturing' 
				AND a.ID=b.ClaimID  AND b.CourseID=d.ID AND a.UserID=e.ID 
				GROUP BY b.ClaimID ORDER BY CreatedDate";
			}
			$run = $this->core->database->doSelectQuery($sql);

			if(!isset($this->core->cleanGet['offset'])){
				echo'<table id="results" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th bgcolor="#EEEEEE" width="30px"></th>
						<th bgcolor="#EEEEEE" width=""><b>Lecturer</b></th>
						<th bgcolor="#EEEEEE" width="70px"><b>Course</b></th>
						<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
						<th bgcolor="#EEEEEE" width="70px"><b>Hours</b></th>
						<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
					</tr>
				</thead>
				<tbody>';
			}

			while ($row = $run->fetch_assoc()) {
				$results == TRUE;

				$id = $row['ID'];
				$date = $row['CreatedDate'];
				$course = $row['Course'];
				$author = $row['Lname'];
				$claim = $row['Hours'];
				$status = $row['Status'];
				$hod = $row['ApproverOne'];
				$dean = $row['ApproverTwo'];
				$dvc = $row['ApproverThree'];
				$claimType = $row['ClaimType'];
				$lecturer = $row['UserID'];

				$edit= '<a href="'. $this->core->conf['conf']['path'] .'/claim/viewlecturing/'.$id.'">View claim details</a>';
				

				$person="";
				if($status == "Pending"){
					if(empty($hod)){
						$person='HOD';
					}else if(empty($dean)){
						$person='Dean';
					}else if(empty($dvc)){
						$person='DVC';
					}

					if($item != "hidden"){
						$status = '<b><a href="'. $this->core->conf['conf']['path'] .'/claim/approve/'.$id.'?person='.$person.'&type='.$claimType.'">Awaiting '.$person.' approval</a></b> <br>
						<a href="'. $this->core->conf['conf']['path'] .'/claim/delete/'.$id.'">Cancel</a> <br>
						Author: ' . $author;
					}
						
				} elseif($status == "Approved") {
					$status =  '<b>' . $status . '</b><br> Author: ' .$author.' </br><a href="'. $this->core->conf['conf']['path'] .'/claim/print/'.$id.'?type='.$claimType.'">Print</a>';
				} else{
					$status =  '<b>' . $status . '</b><br> Author: ' .$author;
				}
									
				echo'<tr>
					<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
					<td> '.$author.' <br> '.$edit.'</td>
					<td> '.$course.'</td>
					<td> '.$date.'</td>
					<td> '.$claim.'</td>
					<td> '.$status.'</td>
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
