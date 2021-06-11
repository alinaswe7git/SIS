<?php
class sign {

	public $core;
	public $view;
	public $limit;
	public $offset;
	public $pager = FALSE;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}


	public function buildView($core) {
		$this->core = $core;

		$this->limit = 50;
		$this->offset = 0;

		include $this->core->conf['conf']['classPath'] . "users.inc.php";


		if(empty($this->core->item)){
			if(isset($this->core->cleanGet['uid'])){
				$this->core->item = trim($this->core->cleanGet['uid']);
			}
		}
		if(isset($this->core->cleanGet['offset'])){
			$this->offset = $this->core->cleanGet['offset'];
		}
		if(isset($this->core->cleanGet['limit'])){
			$this->limit = $this->core->cleanGet['limit'];
			$this->pager = TRUE;
		}
	} 


	public function searchSign($item) {
		$listType = "list";

		if(isset($this->core->cleanGet['studies'])){
			$studies = $this->core->cleanGet['studies'];
		}
		if(isset($this->core->cleanGet['programmes'])){
			$programmes = $this->core->cleanGet['programmes'];
		}
		if(isset($this->core->cleanGet['search'])){
			$search = $this->core->cleanGet['search'];
		}
		if(isset($this->core->cleanGet['q'])){
			$q = $this->core->cleanGet['q'];
		}
		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}

		if(isset($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];
		}

		if(isset($this->core->cleanGet['group'])){
			$group = $this->core->cleanGet['group'];
		}

		if(isset($this->core->cleanGet['studentfirstname'])){
			$firstName = $this->core->cleanGet['studentfirstname'];
		}
		if(isset($this->core->cleanGet['studentlastname'])){
			$lastName = $this->core->cleanGet['studentlastname'];
		}
		if(isset($this->core->cleanGet['listtype'])){
			$listType = $this->core->cleanGet['listtype'];
		}
		if(isset($this->core->cleanGet['year'])){
			$year = $this->core->cleanGet['year'];
		}
		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}
		if(isset($this->core->cleanGet['examcenter'])){
			$center = $this->core->cleanGet['examcenter'];
		}
		if(isset($this->core->cleanGet['period'])){
			$period = $this->core->cleanGet['period'];
		}

		if (isset($lastName) || isset($firstName)) {
			$this->bynameExport($firstName, $lastName, $listType);
		}elseif (isset($center)){
			$this->bycenterExport($center);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyExport($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramExport($q, $listType, $year, $mode ,$period);
		} else if ($this->core->action == "search" && isset($q) && $search == "course" || $this->core->action == "students" && isset($q) && $search == "course") {
			$this->bycourseExport($q, $listType, $mode,$period);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showExport($item);
		} else if ($this->core->action == "search" && isset($card)) {
			$this->showcardExport($card);
		} else if ($this->core->action == "search" && isset($year) && isset($mode)) {
			$this->byintakeExport($year, $mode, $group);
		}else{
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	private function bycenterExport($item, $listType = "list") {
	
		if(empty($item)){
			$this->searchInformation();
		} else {
			$year = $this->core->cleanGet['year'];
			$stype = $this->core->cleanGet['mode'];
			$period = $this->core->cleanGet['period'];
			
			$sql = "SELECT count(DISTINCT a.CourseID) AS Courses,c.ID,c.ID, c.FirstName,c.MiddleName,c.Surname,c.GovernmentID,
					c.Sex,c.MobilePhone,c.`Status`,c.StudyType,b.ExamCentre
					FROM `course-electives` a 
					LEFT JOIN `student-data-other` b ON a.StudentID= b.StudentID
					LEFT JOIN `basic-information` c ON a.StudentID= c.ID
					LEFT JOIN `student-study-link` d ON a.StudentID= d.StudentID
					LEFT JOIN `study` e ON e.ID= d.StudyID
					WHERE a.PeriodID='$period' 
					AND a.PeriodID=b.PeriodID
					AND c.StudyType='$stype'
					AND b.ExamCentre LIKE '%$item%'
					GROUP BY a.StudentID
					ORDER BY e.ParentID,d.StudyID,c.Surname,c.FirstName";

			if ($listType == "profiles") {
				$this->showInfoProfile($sql, FALSE);
			} elseif ($listType == "list") {
				$this->showInfoList($sql);
			}
		}
	}

	private function byintakeExport($year, $mode) {
		if (is_numeric($year)) {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $year . "%' AND `StudyType` LIKE '" . $mode . "' ORDER BY `ID` ASC";
		} else if($year == "all") {
			$sql = "SELECT * FROM `basic-information` WHERE `StudyType` LIKE '" . $mode . "'";
		}

		$this->showInfoList($sql);
	}

	
	public function showExport($item) {
		if(empty($item)){
			$this->searchExport();
		} else {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			$this->showInfoProfile($sql, FALSE);
		}
	}


	private function byprogramExport($program, $listType, $year, $mode, $period) {
		if ($program != "" && is_numeric($program)) {
			
			if($year == "%"){
				$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,`basic-information`.GovernmentID,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link`
					WHERE `student-study-link`.StudentID = `basic-information`.ID 
					AND `course-electives`.StudentID = `basic-information`.ID 
					AND `course-year-link`.StudyID=`student-study-link`.StudyID
					AND `course-year-link`.CourseID=`course-electives`.CourseID
					AND `student-study-link`.StudyID = '$program' 
					AND `course-electives`.PeriodID=$period
					AND `basic-information`.StudyType='$mode'
					GROUP BY `basic-information`.ID
					ORDER BY `basic-information`.`Surname`,`basic-information`.Firstname";

			} else {
				$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,`basic-information`.GovernmentID,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link`
					WHERE `student-study-link`.StudentID = `basic-information`.ID 
					AND `course-electives`.StudentID = `basic-information`.ID 
					AND `course-year-link`.StudyID=`student-study-link`.StudyID
					AND `course-year-link`.CourseID=`course-electives`.CourseID
					AND `student-study-link`.StudyID = '$program' 
					AND `course-electives`.PeriodID=$period
					AND `basic-information`.StudyType='$mode'
					GROUP BY `basic-information`.ID
					HAVING MAX(`course-year-link`.`Year`) = $year
					ORDER BY `basic-information`.`Surname`,`basic-information`.Firstname";
			}
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			
			$this->showInfoList($sql);
		}
	}

	private function bycourseExport($course, $listType, $studytype ,$period){

		if ($course != "" && is_numeric($course)) {

			if(empty($studytype)){ $studytype = "%"; }

			$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`, `course-electives` ,`student-study-link` ,`study`
					WHERE `basic-information`.ID = `course-electives`.StudentID AND `study`.ID=`student-study-link`.StudyID
					AND `course-electives`.CourseID = '$course'
					AND `course-electives`.Approved IN (1)
					AND `course-electives`.PeriodID='$period'
					AND `basic-information`.`StudyType` LIKE '$studytype'
					AND `student-study-link`.StudentID=`basic-information`.ID
					GROUP BY `course-electives`.`StudentID` 
					ORDER BY `study`.ParentID,`student-study-link`.StudyID,`basic-information`.Surname,`basic-information`.Firstname
					";

			if(empty($_GET['offset'])){
				/* adding period
					
				*/
				$sqlp = "SELECT * FROM `periods`
					WHERE `periods`.ID = '$period'";
				$runp = $this->core->database->doSelectQuery($sqlp);
				$rowp = $runp->fetch_assoc();
				
				///end
				$sqlx = "SELECT * FROM `courses`
					WHERE `courses`.ID = '$course'";
	
				$runx = $this->core->database->doSelectQuery($sqlx);
	
				while ($row = $runx->fetch_assoc()) {
					echo '<div class="heading"><h2>'.$row['Name'].' - '.$row['CourseDescription'].' (Year '.$rowp['Year'].' '.$rowp['Name'].' )</h2>  </div>';
				}
			}


			$this->showInfoList($sql);
		} else {
			$this->core->throwError($this->core->translate("You have not selected a course"));
		}
	
	
	}

	private function showInfoProfile($sql, $personal) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$uid = $row[4];
			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];


			echo '<div style=""><div style="-moz-transform: rotate(-90deg); -webkit-transform: rotate(-90deg);">
				<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
				<tr><td><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td></tr>
				<tr><td width=""><b>' . $streetname . '</b></td></tr>';

				if($postalcode != "" && $postalcode != " "){ echo'<tr><td><b>PO. BOX ' . $postalcode . '</b></td></tr>';	}

				echo'<tr><td><b>' . $town . '</b></td></tr>
				<tr><td><b>' . $country . '</b></td></tr>
			</table>
			</div></div>';
		
		}

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}
	}

	private function showInfoList($sql) {


		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		echo'<table class="table table-bordered table-striped table-hover">';
		//echo'<table class="table table-bordered table-striped table-hover">
		//<tr>
		//	<td>#</td>
		//	<td width="">Student Number</td>
		//	<td>Student Name</td>
		//	<td>NRC</td>
		//	<td width="200px">SIGNATURE</td>
		//	<td width="200px">Course Code</td>
		//	<td width="200px">DATE</td>
		//</tr>';

		while ($row = $run->fetch_assoc()) {
			$results = TRUE;
			
			$uid = $row['ID'];
			$nrc = $row['GovernmentID'];
			$sex = $row['Sex'];
			$firstname = $row['FirstName'];
			$middlename = $row['MiddleName'];
			$surname = $row['Surname'];
			$celphone = $row['MobilePhone'];
			$status = $row['Status'];
			$mode = $row['StudyType'];
			$courses = $row['Courses'];
			$examcenter= $row['ExamCentre'];
			$email= $row['PrivateEmail'];
			
			/***** Extra- code for the study******/
			$sqlx = "SELECT a.Name,a.ShortName,c.Description as school FROM `study` a, `student-study-link` b, schools c
					WHERE a.ID = b.StudyID AND b.StudentID=$uid AND a.ParentID=c.ID";
	
				$runx = $this->core->database->doSelectQuery($sqlx);
	
				while ($row = $runx->fetch_assoc()) {
					$studyName=$row['Name'];
					$studyShortName= $row['ShortName'];
					$school= $row['school'];
				}
			
			/*************************************/
			if(isset($_GET['year'])){
				
				$year="Year ".$_GET['year']; 
			}

			if($firstname == $middlename){
				$middlename ='';
			}
			
			if ($school!=$schoolCurrent){
				echo'<tr>
						<td colspan="8" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$school.'</td>
					</tr>';
			}
			if ($studyName!=$studyNameCurrent){
				$count = 1;
				echo '<h4 style="page-break-after: always;></h4>"';
				echo'<tr>
						<td colspan="8" style="font-size: 9pt; font-weight: bold; border: 1px solid #333; align: center; page-break-after: always;">'.$studyName.' '.$year.'</td>
					</tr>';
				echo'<tr>
					<td>#</td>
					<td width="">STUDENT NUMBER</td>
					<td>Name</td>
					<td>Sex</td>
					<td>NRC</td>
					<td width="200px">COURSE CODE</td>
					<td width="200px">DATE <br/>(DD-MM-YYYY)</td>
					<td width="200px">SIGNATURE</td>
				</tr>';
			}
			
			echo '<tr>
				<td>'.$count.'</td>
				<td width="">' . $uid . '</td>
				<td width="300px"><b>' . $surname . ' ' . $firstname . ' ' . $middlename . '</b></td>
				<td>' . $sex . '</td>
				<td>' . $nrc . '</td>
				<td> </td>
				<td> </td>
				<td> </td>
			      </tr>';

			$count++;
			$results = TRUE;
			
			$schoolCurrent=$school;
			$studyNameCurrent=$studyName;
		}

		echo'</table>';

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

		}
		
		echo "<h4>Invigilator's Name:_____________________________</h4>";
		echo "<h4>Signature:_____________________________________</h4>";
		echo "<h4>Date:_________________________________________</h4>";
		

	echo'<script type="text/javascript">
			window.print();
		</script>';


	}

	public function editInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		WHERE bi.`ID` = '" . $item . "'";

		$run = $this->core->database->doSelectQuery($sql);
 
		while ($row = $run->fetch_row()) {
			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$gender = $row[3];
			$dob = $row[6];
			$nationality = $row[8];
			$street = $row[9];
			$postal = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$celphone = $row[14];
			$disability = $row[15];
			$email = $row[17];
			$relation = $row[18];
			$status = $row[20];
			$role = $row[23];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showRoles($role);

			$selectstudy = new optionBuilder($this->core);
			$selectstudy = $selectstudy->showStudies(NULL);
		}

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}
}

?>
