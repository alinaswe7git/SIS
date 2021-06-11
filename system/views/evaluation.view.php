<?php
class evaluation {

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
		if($this->core->role == 1000){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/evaluation/manage">Menu</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/evaluation/new/1">Course evaluation questions</a>';
			//echo '<a href="' . $this->core->conf['conf']['path'] . '/evaluation/report/Assesment">Report</a>';
			echo '</div>';
		}  		
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
	
	public function showEvaluation($item) {
			
		include $this->core->conf['conf']['viewPath'] . "password.view.php";
		$password = new password();
		$password->buildView($this->core);
		//$reset = $password->reset($item);
		
		$students = array(201907001	,201907003	,201907004	,201907007	,201907008	,201907009	,201907010	,201907012	,
							201907013	,201907015	,201907016	,201907018	,201907019	,201907020	,201907021	,201907022	,
							201907024	,201907027	,201907028	,201907029	,201907030	,201907031	,201907032	,201907033	,
							201907034	,201907036	,201907037	,201907038	,201907039	,201907040	,201907041	,201907042	,
							201907043	,201907044	,201907045	,201907046	,201907047	,201907050	,201907051	,201907052	,
							201907053	,201907055	,201907056	,201907057	,201907058	,201907059	,201907060	,201907061	,
							201907062	,201907063	);
							
		foreach ($students as $item){	
			echo $item.' Student</br>';	
		  //$url = "https://edurole.mu.ac.zm/password/reset/$userid";
		 // $reset = $password->resetPassword($item);
		 // $output = file_get_contents($url);
		 // var_dump($output);
		}
		
			
	}
	
	public function manageEvaluation($item) {

		$this->viewMenu();
		$userid = $this->core->userID;
		$period = $this->getPeriod();
		
		if($this->core->role == 1000){
			$sql = "SELECT a.CourseID,(SELECT CONCAT(Name,' - ',CourseDescription)  FROM courses WHERE ID =a.CourseID) as 'Course',
					COUNT(CourseID) as 'Responces',
					(SELECT CONCAT(Year,'-',Name)  FROM periods WHERE ID =a.PeriodID) as 'Period',
					(SELECT GROUP_CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID = c.LecturerID))  FROM `claim-lecturer-course` c WHERE CourseID =a.CourseID GROUP BY CourseID ) as 'Assigned'
					FROM evaluation a WHERE a.`PeriodID` IN (57,'$period') AND a.CourseID <> 0
					GROUP BY CourseID ORDER BY Course;";
		}else if($this->core->role == 104){
			$sql = "SELECT a.CourseID,(SELECT CONCAT(Name,' - ',CourseDescription)  FROM courses WHERE ID =a.CourseID) as 'Course',
					COUNT(CourseID) as 'Responces',
					(SELECT CONCAT(Year,'-',Name)  FROM periods WHERE ID =a.PeriodID) as 'Period',
					(SELECT GROUP_CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID = c.LecturerID))  FROM `claim-lecturer-course` c WHERE CourseID =a.CourseID GROUP BY CourseID ) as 'Assigned'
					FROM evaluation a 
					WHERE a.`PeriodID` IN (57,'$period') AND 
					a.CourseID IN (SELECT CourseID FROM `course-year-link` WHERE StudyID IN(SELECT ID FROM study WHERE ParentID IN(SELECT DISTINCT(SchoolID) FROM staff WHERE EmployeeNo='$userid')) )
					AND a.CourseID <> 0
					GROUP BY CourseID,Period ORDER BY Course,Period;";
		}else{
			$sql = "SELECT a.CourseID,(SELECT CONCAT(Name,' - ',CourseDescription)  FROM courses WHERE ID =a.CourseID) as 'Course',
					COUNT(CourseID) as 'Responces',
					(SELECT CONCAT(Year,'-',Name)  FROM periods WHERE ID =a.PeriodID) as 'Period',
					(SELECT GROUP_CONCAT((SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID = c.LecturerID))  FROM `claim-lecturer-course` c WHERE CourseID =a.CourseID GROUP BY CourseID ) as 'Assigned'
					FROM evaluation a 
					WHERE a.`PeriodID` IN (57,'$period') AND 
					a.CourseID IN(SELECT CourseID FROM `claim-lecturer-course` WHERE LecturerID='$userid')
					AND a.CourseID <> 0
					GROUP BY CourseID,Period ORDER BY Course,Period;";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width=""><b>Course</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Period</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Assigned</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Responces</b></th>
					<th bgcolor="#EEEEEE" width=""></th>
				</tr>
			</thead>
			<tbody>';
		}
		$i=1;
		while ($row = $run->fetch_assoc()) {
			$results == TRUE;

			$id = $row['CourseID'];
			$responces = $row['Responces'];
			$course = $row['Course'];
			$period = $row['Period'];
			$assigned = $row['Assigned'];
			
			
			$assigned = $assigned != '' ? $assigned : 'None';
			
			$status = '<a href="'. $this->core->conf['conf']['path'] .'/evaluation/analysis/'.$id.'">View Analysis</a>';
			
			echo'<tr>
			<td>'.$i.'</td>
			<td> '.$course.'</td>
			<td> '.$period.'</td>
			<td> '.$assigned.'</td>
			<td> '.$responces.'</td>
			<td> '.$status.'</td>
			</tr>';
			
			$i++;	
			$results = TRUE;


		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('No information');
			}
		}


		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}
	}

	public function newEvaluation($item) {

		$this->viewMenu();

				
			echo'<style>
				input {
					width: 50px;
				}
				</style>';

		echo'<h1>Evaluation form</h1>
		<h2>PLEASE SELECT ANSWERS FOR ALL QUESTIONS BELOW:</h2><hr>';
		
		$userid = $this->core->userID;
		
		$category = $this->core->cleanGet["category"];
		$course = $this->core->cleanGet["course"];
		$period = $this->core->cleanGet["period"];
					
		$sqld = "SELECT * FROM `evaluation-question` WHERE `CategoryID` = '$item' ";
		$rund = $this->core->database->doSelectQuery($sqld);
		
		echo '<form id="save" name="save" method="post" action="'.$this->core->conf['conf']['path'] .'/evaluation/save/'.$userid.'">';
		
		echo '<input name="course" value="'.$course.'" type="hidden" /> ';
		echo '<input name="category" value="'.$item.'" type="hidden" /> ';
		echo '<input name="period" value="'.$period.'" type="hidden" /> ';
		echo '<table>';
		$i=1;		
		while ($rowd = $rund->fetch_assoc()) {
			$id = $rowd['ID'];
			$question = $rowd['Question'];
	
			
			echo '<tr>
				 <td>
					<b>'.$question.'</b> </br>
					 <input name="question'.$i.'" value="'.$id.'" type="hidden" />
					 <input name="mark'.$id.'" value="0" type="radio" checked="checked" > Not selected </br>
					 <input name="mark'.$id.'" value="5" type="radio" /> (5) Excellent </br>
					 <input name="mark'.$id.'" value="4" type="radio" /> (4) Good </br>
					 <input name="mark'.$id.'" value="3" type="radio" /> (3) Moderate </br>
					 <input name="mark'.$id.'" value="2" type="radio" /> (2) Poor </br>
					 <input name="mark'.$id.'" value="1" type="radio" /> (1) Very poor 
					 </br>
					 </br>
				 </td>
			 </tr>';
			
			$i++;
		}
		
		echo '<tr>
			<td>
				<b>ENTER YOUR COMMENTS ON THIS SUBJECT (MANDATORY):</b></br>
				<textarea name="comment" rows="4" cols="80" placeholder="Type here" required></textarea>

				<br><br>

				<input name="count" value="'.$i.'" type="hidden" />
				<button class="submit">SUBMIT YOUR EVALUATION</button>
			</td>
			</tr>
		</table>
		<hr><p>&nbsp;</p>';
	
		echo '</form>';
		
	
	}
		
	public function saveEvaluationItem($item) {

		$userid   = $this->core->userID;
		
		$count = $this->core->cleanPost['count'];
		$questionID = $this->core->cleanPost['question'];
		
		for($i=1; $i < $count; $i++){
			
			$question   = $this->core->cleanPost["question$i"]; 
			$mark   = $this->core->cleanPost["mark$question"]; 
			
			$sql = "INSERT INTO `evaluation-items`(`EvaluationID`, `QuestionID`, `Mark`) VALUES ( '$item', '$question', '$mark');";
			$this->core->database->doInsertQuery($sql);
		}
		
		return;
	}

	public function saveEvaluation($item) {

		$userid = $this->core->userID;
		$category = $this->core->cleanPost['category'];
		$course = $this->core->cleanPost['course'];
		$count = $this->core->cleanPost['count'];
		$comment = $this->core->cleanPost['comment'];
		
		$period = $this->core->cleanPost['period'];
		
		if(empty($period)){
			$period = $this->getPeriod();
		}
		
						
		$sql = "INSERT INTO `evaluation`(`UserID`, `CourseID`, `CategoryID`, `PeriodID`, `DateTime`, `Comment`) 
				VALUES ('$userid', '$course' , '$category', '$period', NOW(),'$comment');";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">Your evaluation has been added</span>';

		$sql = "SELECT LAST_INSERT_ID() AS ID FROM `evaluation` WHERE `UserID`= '$userid'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$cid = $row['ID'];
		}
		$this->saveEvaluationItem($cid);
		
		$this->courseEvaluation();
		
	}
	public function courseEvaluation() {

		$userid = $this->core->userID;
		$period = $this->getPeriod();
		
		echo '<div><br> <h2>Student course evaluation  information</h2><br>
		<table width="500" height="" border="0" cellpadding="0" cellspacing="0">';
		

		echo'<tr><td colspan="2"><br><b>COURSE PROGRESSION: </b><br><br>';

		$sqls = "SELECT DISTINCT `courses`.`CourseDescription`, `courses`.Name,`courses`.ID, `periods`.`Year`, `periods`.`Semester`,`course-electives`.Approved AS Approved,`periods`.ID AS PID
		FROM `course-electives`
		LEFT JOIN `periods` ON `course-electives`.`PeriodID` = `periods`.ID
		LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.ID 
		WHERE `course-electives`.StudentID  = '$userid' 
		AND `course-electives`.Approved IN (1,0)
		AND `course-electives`.CourseID <> 0
		AND `course-electives`.PeriodID IN ('57','$period') ";

		$runo = $this->core->database->doSelectQuery($sqls);

		while ($fetchw = $runo->fetch_assoc()) {
			
			$sql = "SELECT COUNT(CourseID) AS course FROM `evaluation` WHERE `UserID`= '$userid' AND `PeriodID` IN (57,'$period') AND CourseID=".$fetchw['ID'];

			$run = $this->core->database->doSelectQuery($sql);
			
			$course =0;
			
			while ($fetch = $run->fetch_assoc()) {
				$course = $fetch['course'];
			}
			//echo $course;
			$evaluation = '';
			
			if($course == 0){
				$evaluation = '  <a href="' . $this->core->conf['conf']['path'] . '/evaluation/new/1?course='.$fetchw['ID'].'&period='.$fetchw['PID'].'">Click to evaluate</a>';
			}else{
				$evaluation = '  <font color="green">Done</font>';
			}
						
			
			if($year != $fetchw['Year'] . $fetchw['Semester']){
				echo '<b>' . $fetchw['Year'].' - Sem. '.$fetchw['Semester'] . '</b><br>';
			}
			if($fetchw['Approved'] == 1){
				echo'<li>'.$fetchw['Name'].'  - '.$fetchw['CourseDescription'].'</i>'.$evaluation.'<br>';
			}else{
				echo'<font color="grey"><li >'.$fetchw['Name'].'  - '.$fetchw['CourseDescription'].' <b>(Not Approved)</b></i></font> '.$evaluation.'<br>';
			}
			
			$year = $fetchw['Year'] . $fetchw['Semester'];
		}

		if($runo->num_rows == 0){
			echo '<h2>NO COURSES SELECTED</h2>';
		}	

		echo'</p></td></tr>';


		echo'</table></div>';
		

	}
	
	public function courselectureEvaluation($item) {
			
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
	
	public function analysisEvaluation($item) {

					
			
			$sql = "SELECT a.Question, 
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 5 
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '5',
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 4
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '4',
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 3
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '3',
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 2
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '2',
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 1
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '1',
					(SELECT COUNT(Mark) FROM `evaluation-items` WHERE QuestionID=a.ID AND Mark = 0
					AND EvaluationID IN (SELECT ID FROM evaluation WHERE CourseID = $item )) AS '0',
					(SELECT CONCAT(Name,' - ',CourseDescription)  FROM courses WHERE ID =$item) as 'Course',
					(SELECT COUNT(ID) FROM evaluation WHERE CourseID = $item ) AS 'total'
					
					FROM `evaluation-question` a";
			
			$run = $this->core->database->doSelectQuery($sql);
			
			$i=0;
			$avg =0; 			
			$tavg =0; 			
			$output='';						
			while ($row = $run->fetch_assoc()) {
				$total = $row['total'];
				$question = $row['Question'];
				$course = $row['Course'];
				$excellent = $row['5'];
				$good = $row['4'];
				$moderate = $row['3'];
				$poor = $row['2'];
				$vpoor= $row['1'];
								
				$point = 0;
				
				$pexcellent = round(($excellent/$total)*100,2);
				$pgood = round(($good/$total)*100,2);
				$pmoderate = round(($moderate/$total)*100,2);
				$ppoor= round(($poor/$total)*100,2);
				$pvpoor = round(($vpoor/$total)*100,2);
				
				//$avg = ($excellent != 0 ? 5)+($good != 0 ? 4)+($moderate != 0 ? 3)+($poor != 0 ? 2 )+($vpoor != 0 ? 1))/5;
				if ($excellent != 0){$point += $excellent * 5; }
				
				if ($good != 0){$point += $good * 4; }
				
				if ($moderate != 0){$point += $moderate * 3; }
				
				if ($poor != 0){$point += $poor * 2; }
				
				if ($vpoor != 0){$point += $vpoor * 1; }
				
				$avg = round((($point/($total * 5))* 5) ,2) ;
				
				$tavg += $avg;
								
				$output .= '<tr>
				 <td><div><b>'.$question.'</b></div> </br>
				
					<div class="">
					  <div class="label">(5) Excellent '.$pexcellent.'%</div><div class="label">
					   <progress max="100" value="'.$pexcellent.'"></progress> <b>'.$excellent.'</b> </div>
					</div><br>

					<div class="w3-light-grey">
					  <div class="label">(4) Good  '.$pgood.'%</div><div class="label">
					  <progress max="100" value="'.$pgood.'"></progress> <b>'.$good.'</b> </div>
					</div><br>
					
				    <div class="w3-light-grey">
					  <div class="label">(3) Moderate '.$pmoderate.'%</div><div class="label">
					  <progress max="100" value="'.$pmoderate.'"> </progress> <b>'.$moderate.'</b> </div>
					</div><br>
						
					<div class="w3-light-grey">
					  <div class="label" style="height">(2) Poor '.$ppoor.'%</div><div class="label">
					  <progress max="100" value="'.$ppoor.'"></progress> <b>'.$poor.'</b> </div>
					</div><br>
						
				    <div>
					  <div class="label" >(1) Very poor  '.$pvpoor.'%</div><div class="label">
					  <progress max="100" value="'.$pvpoor.'"></progress> <b>'.$vpoor.'</b> </div>
					  
					</div><br>
					<div>
					   <div> <b>Average '.$avg.'</b></div>
					</div><br>
					
				 <br>
				 </td>
				 </tr>';	
				
				$i++;				 
				
			}
			$score = ($i)*5;
			$per = round ((($tavg /$score)*100 ), 2);
			echo '<h2>Submitted answers: '.$total.' </h2><h2>Questions: '.$i.'</h2><h2>'.$course.'</h2><h2>Total Average Score: '.$per.' % </h2> <br>';
			
			echo "<h3>Course assigned to :  <ol>";
			$sqlLec = "SELECT (SELECT CONCAT(FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=c.LecturerID) AS 'Name'  
			FROM `claim-lecturer-course` c	WHERE c.CourseID = '$item' ";
			$runLec = $this->core->database->doSelectQuery($sqlLec);
			
			while ($rowLec = $runLec->fetch_assoc()) {
				
				$comment = $rowLec['Name'];
				
				//$comment = $comment == '' ? 'None' : $comment;
				
				echo "<li>$comment</li>";
				
			}
			
			
			
			echo "</ol></h3><table>";
			echo $output;
			echo "</table>";
			
			$sqlSum = "SELECT 'Clearly Positive' AS Senti , Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore >= 0.5 AND CourseID ='$item'  UNION
					SELECT 'Positive' AS Senti,Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore <= 0.4 AND SentimentScore >= 0.2 AND CourseID ='$item' UNION
					SELECT 'Neutral' AS Senti,Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore <= 0.1 AND SentimentScore >= -0.1 AND CourseID ='$item' UNION
					SELECT 'Negative' AS Senti,Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore <= -0.2 AND SentimentScore >= -0.4  AND CourseID ='$item' UNION 
					SELECT 'Clearly Negative' AS Senti,Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore < -0.5 AND CourseID ='$item' UNION 
					SELECT 'Unclassified',Count(DISTINCT `Comment`) AS Num FROM evaluation WHERE SentimentScore is null AND CourseID ='$item'";
			$runSum  = $this->core->database->doSelectQuery($sqlSum);
			
			echo "<H3>Comments analysis summary</H3>";
			
			echo "<table><thead><tr><th>Sentiments</th><th>Number</th></tr></thead><tbody>";
			
			while ($rowSum  = $runSum ->fetch_assoc()) {
				
				$Senti = $rowSum['Senti'];
				$Num = $rowSum['Num'];
				
				if ($Senti == 'Clearly Positive'){
					$color='#63FEA8';
				}elseif ($Senti == 'Positive'){
					$color='#B6FED2';
				}elseif ($Senti == 'Neutral'){
					$color='#FFFFFF';
				}elseif ($Senti == 'Negative'){
					$color='#FFD5CB';
				}elseif ($Senti == 'Clearly Negative'){
					$color='#FE8D74';
				}else{
					$color='#969696';
				}
				
				echo '<tr bgcolor="'.$color.'"><td>'.$Senti.' Comments</td><td><b>'.$Num.'</b></td></tr>';
				
				
			}
			echo "</tbody></table>";
			
			echo "<table><thead><tr><th>Comments</th></tr></thead<tbody><tr><td><ol>";
			
				
			$sqld = "SELECT DISTINCT `Comment`,`SentimentScore` FROM evaluation WHERE CourseID = '$item' ORDER BY SentimentScore DESC";
			$rund = $this->core->database->doSelectQuery($sqld);
			while ($rowd = $rund->fetch_assoc()) {
				
				$comment = $rowd['Comment'];
				$Senti = $rowd['SentimentScore'];
				
				if ($Senti >= 0.5){
					$color='#63FEA8';
				}elseif ($Senti <= 0.4 &&  $Senti >= 0.2){
					$color='#B6FED2';
				}elseif ($Senti <= 0.1 &&  $Senti >= -0.1){
					$color='#FFFFFF';
				}elseif ($Senti <= -0.2 &&  $Senti >= -0.4){
					$color='#FFD5CB';
				}elseif ($Senti <= -0.5 &&  $Senti >= -1){
					$color='#FE8D74';
				}else{
					$color='#969696';
				}
				
				echo '<li><span style="background-color: '.$color.';">*</span><b>'.$comment.'</b></li>';
				
			}
			
			echo "</ol></td></tr></tbody></table>";
			
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
