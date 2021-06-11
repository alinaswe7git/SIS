<?php
class information {

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

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/balance">Balance</a>'.
		'</div>';
	}

	public function getBalance($item){

			$output = file_get_contents("http://192.168.0.6:8080/NipaBankInvoice_visa/studentbalance/$item");

			$p = xml_parser_create();
			xml_parse_into_struct($p, $output, $vals, $index);

			xml_parser_free($p);


			$balance = $vals[1]["value"];

			if( is_numeric($balance)){

			$sql = "INSERT INTO `balances` (`StudentID`, `Amount`, `LastUpdate`, `LastTransaction`, `Original`) VALUES ($item, $balance, NOW(), '', 0) ON DUPLICATE KEY UPDATE `Amount`= $balance;";
			$run = $this->core->database->doInsertQuery($sql);

			if($balance == '1000000000'){
				$balance = 0;
			}

			return $balance;
			} else { 
				return FALSE;
			}

	}


	public function pictureInformation($item) {

		if($this->core->role < 100 && $item != $this->core->userID){
			$uid = 'xxxx';
		}

		$uid = $item;
		if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
			$filename = '/data/website/datastore/identities/pictures/' . $uid . '.png_final.png';
		} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
			$filename = '/data/website/datastore/identities/pictures/' . $uid . '.png';
		} else {
			$filename = '/data/website/templates/default/images/noprofile.png';
		}

		$mime = mime_content_type($filename);

    		header("Content-type: $mime");
    		header('Content-Disposition: attachment; filename='.urlencode(basename($filename)));
    		header('Content-Length: ' . filesize($filename));

		$content = readfile($filename);

		exit;
	}


	public function buildView($core) {
		$this->core = $core;

		$this->limit = 5000;
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

	public function studentsInformation($item) {
		$this->searchInformation($item);
	}

	public function saveInformation($item){
		$users = new users($this->core);
		$users->saveEdit($this->core->item, TRUE);

		$this->core->throwSuccess($this->core->translate("The user account has been updated"));
		$this->editInformation($item);
	}

	public function personalInformation($item){
		$userid = $this->core->userID;

		$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $userid . "' AND ac.`ID` = bi.`ID`";

		$this->showInfoProfile($sql, TRUE);
	}

	public function searchInformation($item) {
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

		if(isset($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];
		}

		if(isset($this->core->cleanGet['group'])){
			$group = $this->core->cleanGet['group'];
		}



		if(isset($this->core->cleanGet['status'])){
			$status = $this->core->cleanGet['status'];
		}

		if(isset($this->core->cleanGet['studentfirstname'])){
			$firstName = $this->core->cleanGet['studentfirstname'];
		}
		if(isset($this->core->cleanGet['studentlastname'])){
			$lastName = $this->core->cleanGet['studentlastname'];
		}
		/*isset check and clean get the html id and push it to 
		the variable $username which will be used in the top search bar*/
		if(isset($this->core->cleanGet['searchItem'])){
			$userName = $this->core->cleanGet['searchItem'];
		}
		if(isset($this->core->cleanGet['listtype'])){
			$listType = $this->core->cleanGet['listtype'];
		}
		if(isset($this->core->cleanGet['year'])){
			$year = $this->core->cleanGet['year'];
		}else{
			$year = '%';
		}
		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}
		if(isset($this->core->cleanGet['examcenter'])){
			$center = $this->core->cleanGet['examcenter'];
		}
		if(isset($this->core->cleanGet['role'])){
			$role = $this->core->cleanGet['role'];
		}
		if(isset($this->core->cleanGet['period'])){
			$period = $this->core->cleanGet['period'];
		}

		if (isset($lastName) || isset($firstName)) {
			$this->bynameInformation($firstName, $lastName, $listType);
		} else if ($this->core->action == "search" && $search == "exclude" && isset($mode) && isset($period) && isset($status) ) {
			$this->bystatusInformation($listType, $mode,$period, $status);
		}else if ($this->core->action == "search" && isset($center) && $search == "center"){
			$this->bycenterInformation($center);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyInformation($q, $listType, $year, $mode,$period);
		} else if ($this->core->action == "search" && isset($q) && $search == "reported" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyInformation($q, $listType, $year, $mode,$period);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramInformation($q, $listType, $year, $mode,$period);
		} else if ($this->core->action == "search" && isset($q) && $search == "course" || $this->core->action == "students" && isset($q) && $search == "course") {
			$this->bycourseInformation($q, $listType, $mode,$period);
		} else if ($this->core->action == "search" && isset($role)) {
			$this->showroleInformation($role);
		} else if ($this->core->action == "search" && isset($card)) {
			$this->showcardInformation($card);
		} else if ($this->core->action == "search" && isset($year) && isset($mode) && isset($status)) {
			$this->byintakeInformation($year, $mode, $status);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showInformation($item);
		}else{
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);
			$centres = $select->showCentres(null);
			$roles   = $select->showRoles(null);
			$periods = $select->showPeriods(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	public function showcardInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$sql = "SELECT * FROM `basic-information` as `bi`, `accesscards` WHERE `CardID` LIKE '" . $item . "' AND UserID = `bi`.ID";
			$this->showInfoProfile($sql, FALSE);
		}
	}


	public function showroleInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
		
			$sql = "SELECT * FROM `basic-information` as `bi`, `access`, `roles` WHERE `roles`.`ID` = '" . $item . "' AND `access`.RoleID = `roles`.ID AND `access`.ID = `bi`.ID";
			$this->showInfoList($sql);
		}
	}

	//added arguments $firstname & $lastname, because it was throwing an error without them for some reason
	public function showInformation($item) {
		$user = $this->core->userID;
		if(empty($item)){
			$this->searchInformation($item);
		} else {
			if($this->core->role == 1027){
				$sql = "SELECT * FROM `basic-information`, `external-students` 
				WHERE `basic-information`.`ID` = `external-students`.StudentID 
				AND (`basic-information`.`ID` LIKE '$item' OR `external-students`.ReferenceID LIKE '$item')
				AND `external-students`.UserID = '$user'";
			} else {
				$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			}
			$this->showInfoProfile($sql, FALSE);
		}
	}
	
	//added arguments $firstname & $lastname, because it was throwing an error without them for some reason
	public function barsearchInformation($searchItem) {
		$searchItem = $this->core->cleanGet['searchItem'];
		if(empty($searchItem)){
			//$this->searchInformation($firstName,$lastName);
			echo "Please enter some input in ther search input";
		} else {
			if($this->core->role == 1027){
				$sql = "SELECT * FROM `basic-information`
				WHERE (`basic-information`.`ID` LIKE '%$searchItem%' OR `basic-information`.`FirstName` LIKE '%$searchItem%' 
				OR `basic-information`.`Surname` LIKE '%$searchItem%' OR `basic-information`.`MiddleName` LIKE '%$searchItem%')";
				
			} else {
				$sql = "SELECT * FROM `basic-information`
				WHERE (`basic-information`.`ID` LIKE '%$searchItem%' OR `basic-information`.`FirstName` LIKE '%$searchItem%' 
				OR `basic-information`.`Surname` LIKE '%$searchItem%' OR `basic-information`.`MiddleName` LIKE '%$searchItem%')";
			}
			//echo $sql;
			$this->showInfoList($sql);
		}
	}

/*created new function show user info used to test 
	code instead of showuserinfo, currently not used, delete if nessesary */
	public function showUserInfo($item) {
		$user = $this->core->userID;
		if(empty($item)){
			$this->searchInformation($firstName);
		} else {
			if($this->core->role == 1027){
				$sql = "SELECT * FROM `basic-information`, `external-students` 
				WHERE `basic-information`.`ID` = `external-students`.StudentID 
				AND (`basic-information`.`ID` LIKE '$item' OR `external-students`.ReferenceID LIKE '$item')
				AND `external-students`.UserID = '$user'";
			} else {
				$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			}
			$this->showInfoProfile($sql, FALSE);
		}
	}

/* show users is the funtion to be used to for the searchbar and contains
 a function which should allow both first name and last name search */
	public function showUsers($userName) {
		$user = $this->core->userID;
		if(empty($userName)){
			$this->searchInformation($userName);
		} else {
			if($this->core->role == 1027){
				$sql = "SELECT `basic-information`.ID, `FirstName`, `MiddleName`, `Surname`
     	 		FROM  `basic-information`
      			WHERE FirstName LIKE '%{$userName}%' OR MiddleName LIKE '%{$userName}%' OR ID LIKE '%{$userName}%' OR Surname LIKE '%{$userName}%'";

			} else {
				$sql = "SELECT `basic-information`.ID, `FirstName`, `MiddleName`, `Surname`
     	 		FROM  `basic-information`
      			WHERE FirstName LIKE '%{$userName}%' OR MiddleName LIKE '%{$userName}%' OR ID LIKE '%{$userName}%' OR Surname LIKE '%{$userName}%'";
			}
			$this->showInfoProfile($sql, FALSE);
		}
	}


	public function bycenterInformation($item, $listType = "list") {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$year = $this->core->cleanGet['year'];
			$stype = $this->core->cleanGet['mode'];
			$period = $this->core->cleanGet['period'];
			
				$sql = "SELECT count(DISTINCT a.CourseID) AS Courses,c.ID,c.ID, c.FirstName,c.MiddleName,c.Surname, c.Sex,c.MobilePhone,c.`Status`,c.StudyType,b.Description AS ExamCentre,MAX(f.Year) as CurrYear 
					FROM `course-electives` a 
					LEFT JOIN `basic-information` c ON a.StudentID= c.ID 
					LEFT JOIN `student-study-link` d ON a.StudentID= d.StudentID 
					LEFT JOIN `study` e ON e.ID= d.StudyID
					LEFT JOIN schools b ON d.Status=b.ID
					LEFT JOIN `course-year-link` f ON f.StudyID= e.ID AND f.CourseID = a.CourseID 
					WHERE a.PeriodID='$period' 
					AND c.StudyType='$stype'
					AND d.Status ='$item'
					GROUP BY a.StudentID
					ORDER BY e.ParentID,d.StudyID,CurrYear,c.Surname,c.FirstName";
			//echo $sql; 
			if ($listType == "profiles") {
				$this->showInfoProfile($sql, FALSE);
			} elseif ($listType == "list") {
				$this->showInfoList($sql);
			}
		}
	}

	private function bynameInformation($firstName, $lastName, $listType) {
		if (empty($firstName)) {
			$firstName = "%";
		}
		if (empty($lastName)) {
			$lastName = "%";
		}

		$user = $this->core->userID;

		if($this->core->role == 1027){
				$sql = "SELECT * FROM `external-students`, `basic-information`
				WHERE `basic-information`.`ID` = `external-students`.StudentID 
				AND `Surname` LIKE '%" . $lastName . "%' AND `Firstname` LIKE '%" . $firstName . "%'
				AND `external-students`.UserID = '$user'";
		} else {
				$sql = "SELECT * FROM `student-study-link`,`study`,`basic-information` WHERE 
				`student-study-link`.StudentID = `basic-information`.ID AND
				`student-study-link`.StudyID = `study`.ID AND
				`basic-information`.`Surname` LIKE '%" . $lastName . "%' AND `basic-information`.`Firstname` LIKE '%" . $firstName . "%'
				ORDER BY `study`.ParentID,`student-study-link`.StudyID,`basic-information`.Surname,`basic-information`.Firstname";
		}
		
		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function byintakeInformation($year, $mode, $status) {

		if($mode == "Masters"){
			$mode = "Distance";
			$year = "1". $year;
		}

		if($status == ''){
			$status = '%';
		}

		if (is_numeric($year)) {
			$sql = "SELECT `basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
			FROM `basic-information`,`student-study-link` ,`study` WHERE 
			`basic-information`.`ID` LIKE '" . $year . "%' 
			AND `basic-information`.`StudyType` LIKE '" . $mode . "'
			AND `basic-information`.`Status` LIKE '$status'
			AND `student-study-link`.StudentID=`basic-information`.ID
			AND `study`.ID=`student-study-link`.StudyID
			ORDER BY `study`.ParentID,`student-study-link`.StudyID,`basic-information`.StudyType,`basic-information`.Surname,`basic-information`.Firstname";
		} else if($year == "all") {
			$sql = "SELECT `basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,
					`basic-information`.`Status`,`basic-information`.StudyType
			FROM `basic-information`,`student-study-link` ,`study` WHERE 
			`basic-information`.`StudyType` LIKE '" . $mode . "' 
			AND `student-study-link`.StudentID=`basic-information`.ID
			AND `basic-information`.`Status` LIKE '$status'
			AND `study`.ID=`student-study-link`.StudyID
			ORDER BY `study`.ParentID,`student-study-link`.StudyID,`basic-information`.StudyType,`basic-information`.Surname,`basic-information`.Firstname";
		}


		$this->showInfoList($sql);
	}

	private function bystudyInformation($study, $listType, $year, $mode,$period) {
		if ($study != "" && is_numeric($study)) {
			
			if ( $this->core->role == 1027){
				$userID = $this->core->userID; 
				
				if($year == "%"){
					$sql = "SELECT * ,count(DISTINCT `course-electives`.CourseID) AS Courses 
					FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link`,`external-students`
						WHERE `student-study-link`.StudentID = `basic-information`.ID 
						AND `course-electives`.StudentID = `basic-information`.ID 
						AND `external-students`.StudentID = `basic-information`.ID 
						AND `course-year-link`.StudyID=`student-study-link`.StudyID
						AND `course-year-link`.CourseID=`course-electives`.CourseID
						AND `student-study-link`.StudyID = '$study' 
						AND `course-electives`.PeriodID=$period
						AND `basic-information`.StudyType='$mode'
						AND `external-students`.userID = $userID 
						GROUP BY `basic-information`.ID
						ORDER BY `basic-information`.StudyType, `basic-information`.Surname,`basic-information`.Firstname";

				} else {
					$sql = "SELECT * ,count(DISTINCT `course-electives`.CourseID) AS Courses 
					FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link` ,`external-students`
						WHERE `student-study-link`.StudentID = `basic-information`.ID 
						AND `course-electives`.StudentID = `basic-information`.ID 
						AND `external-students`.StudentID = `basic-information`.ID 
						AND `course-year-link`.StudyID=`student-study-link`.StudyID
						AND `course-year-link`.CourseID=`course-electives`.CourseID
						AND `student-study-link`.StudyID = '$study' 
						AND `course-electives`.PeriodID=$period
						AND `basic-information`.StudyType='$mode'
						AND `external-students`.userID = $userID 
						GROUP BY `basic-information`.ID
						HAVING MAX(`course-year-link`.`Year`) = $year
						ORDER BY `basic-information`.StudyType, `basic-information`.Surname,`basic-information`.Firstname";
				}
			}else{

				if($year == "%"){
					$sql = "SELECT * ,count(DISTINCT `course-electives`.CourseID) AS Courses FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link`
						WHERE `student-study-link`.StudentID = `basic-information`.ID 
						AND `course-electives`.StudentID = `basic-information`.ID 
						AND `course-year-link`.StudyID=`student-study-link`.StudyID
						AND `course-year-link`.CourseID=`course-electives`.CourseID
						AND `student-study-link`.StudyID = '$study' 
						AND `course-electives`.PeriodID=$period
						AND `basic-information`.StudyType='$mode'
						GROUP BY `basic-information`.ID
						ORDER BY `basic-information`.StudyType, `basic-information`.Surname,`basic-information`.Firstname";

				} else {
					$sql = "SELECT * ,count(DISTINCT `course-electives`.CourseID) AS Courses FROM `basic-information`,`course-electives`, `student-study-link`, `course-year-link`
						WHERE `student-study-link`.StudentID = `basic-information`.ID 
						AND `course-electives`.StudentID = `basic-information`.ID 
						AND `course-year-link`.StudyID=`student-study-link`.StudyID
						AND `course-year-link`.CourseID=`course-electives`.CourseID
						AND `student-study-link`.StudyID = '$study' 
						AND `course-electives`.PeriodID=$period
						AND `basic-information`.StudyType='$mode'
						GROUP BY `basic-information`.ID
						HAVING MAX(`course-year-link`.`Year`) = $year
						ORDER BY `basic-information`.StudyType, `basic-information`.Surname,`basic-information`.Firstname";
				}
				//echo $sql;
			}
		}
		
		$this->showInfoListCourse($sql);
		
	}
	private function bystatusInformation($listType,$mode,$period, $status) {
		
			if($status == 'Exclude'){
				$exclude = "AND `comments`.`Comment`='EXCLUDE'";
			} else if($status == 'all'){
				$exclude = "";
			} else {
				$exclude = "AND `basic-information`.Status ='$status'";
			}


			if($mode == "%"){
				$sql = "SELECT comments.`Comment`,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`, `comments` ,`study`, `course-electives`
					WHERE `basic-information`.ID = `comments`.StudentID 
					AND `comments`.StudentID = `course-electives`.StudentID 
					AND `comments`.PeriodID='$period'
					AND `course-electives`.PeriodID='$period'
					$exclude
					AND `study`.ID=`comments`.StudyID
					GROUP BY `comments`.`StudentID` 
					ORDER BY `study`.ParentID,`study`.ID,`basic-information`.Surname,`basic-information`.Firstname";

			} else {
				$sql = "SELECT comments.`Comment`,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`, `comments` ,`study`,`course-electives`
					WHERE `basic-information`.ID = `comments`.StudentID 
					AND `comments`.StudentID  = `course-electives`.StudentID 
					AND `basic-information`.StudyType ='$mode'
					AND `comments`.PeriodID='$period'
					AND `course-electives`.PeriodID='$period'
					$exclude
					AND `study`.ID=`comments`.StudyID
					GROUP BY `comments`.`StudentID` 
					ORDER BY `study`.ParentID,`study`.ID,`basic-information`.Surname,`basic-information`.Firstname";
					
			}
		

		$this->showInfoList($sql);
		
	}

	private function byprogramInformation($program, $listType, $year, $mode,$period) {
		if ($program != "" && is_numeric($program)) {


			if($year == "%"){
				$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
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
					ORDER BY `basic-information`.Surname,`basic-information`.Firstname";

			} else {
				$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
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
					ORDER BY `basic-information`.Surname,`basic-information`.Firstname";
			}
			//echo $sql;
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function bycourseInformation($course, $listType, $studytype,$period){

		if ($course != "" && is_numeric($course)) {

			if(empty($studytype)){ $studytype = "%"; }

			$sql = "SELECT count(DISTINCT `course-electives`.CourseID) AS Courses,`basic-information`.ID,
					`basic-information`.ID, `basic-information`.FirstName,`basic-information`.MiddleName,`basic-information`.Surname,
					`basic-information`.Sex,`basic-information`.MobilePhone,`basic-information`.PrivateEmail,`basic-information`.`Status`,`basic-information`.StudyType
					FROM `basic-information`, `course-electives` ,`student-study-link` ,`study`
					WHERE `basic-information`.ID = `course-electives`.StudentID 
					AND `course-electives`.CourseID = '$course'
					AND `course-electives`.Approved IN (1)
					AND `course-electives`.PeriodID='$period'
					AND `basic-information`.`StudyType` LIKE '$studytype'
					AND `student-study-link`.StudentID=`basic-information`.ID
					AND `study`.ID=`student-study-link`.StudyID
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

		while ($row = $run->fetch_assoc()) {
			$results = TRUE;
			$firstname = ucfirst($row['FirstName']);
			$middlename = ucfirst($row['MiddleName']);
			$surname = ucfirst($row['Surname']);

			$sex = $row['Sex'];
			$uid = $row['ID'];
			$nrc = $row['GovernmentID'];
			$dob = $row['DateOfBirth'];
			$pob = $row['PlaceOfBirth'];
			$nationality = $row['Nationality'];

			$streetname = $row['StreetName'];
			$postalcode = $row['PostalCode'];
			$town = $row['Town'];
			$country = $row['Country'];
			$homephone = $row['HomePhone'];
			$mobilephone = $row['MobilePhone'];

			$disability = $row['Disabiliy'];
			$disabilitytype = $row['DissabilityType'];
			$email = $row['PrivateEmail'];
			$maritalstatus = $row['MaritalStatus'];
			$mode = $row['StudyType'];
			$sstatus = $row['Status'];

			if(isset($row['RoleID'])){
				$role = $row['RoleID'];
			} else {
				$role = "10";
			}


			if( $sstatus=="Deregistered"){ 
				$style = "background-color: #000;"; 
				$activate = "Deregistered account";
				$links = "#";
			} else if( $sstatus=="Graduated"){ 
				$style = "background-color: #62ab3b;";
				$activate = "GRADUATE ACCOUNT";
				$links ="#";
			} else if( $sstatus=="Requesting"){ 
				$style = "background-color: #62c37e;";
				$activate = "ACTIVATE ACCOUNT";
				$links =  $this->core->conf['conf']['path'] . '/admission/activate/'.$uid;
			} else if( $sstatus=="Approved"){ 
				$style = "background-color: #62c37e;";
				$activate = "ACTIVE ACCOUNT";
				$links = "";
			}

			if($this->core->role == 1000){
				$links =  $this->core->conf['conf']['path'] . '/admission/activate/'.$uid;
			}


			if($sstatus == "Deceased"){
				$style = "background-color: #000;";
				$firstname = "&#10014; " . $firstname;
			}

			if($firstname == $middlename){
				$middlename = '';
			}

			echo '<div class="student" style="">
			<div class="studentname" style="clear:both; '.$style.'"> Name: ' . $firstname . ' ' . $middlename . ' ' . $surname . ' </div>';

 

			echo '<div class="profilepic">';

			if(isset($this->core->cleanGet['payid'])){
				$payid = $this->core->cleanGet['payid'];
				$other = "?payid=".$this->core->cleanGet['payid'];
				$date = $this->core->cleanGet['date'];

				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #0098FF; text-align: center; padding: 10px;border-radius: 5px;">
				<a href="' . $this->core->conf['conf']['path'] . '/payments/modify/'.$payid.'?uid='.$uid.'&date='.$date.'">ASSIGN PAYMENT</a>
				</div>';
			}
	
	
			if($sstatus == "Employed"){	
				$num = "System number"; 	
				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; border-radius: 5px;">Employee</div>';
			}else{
				
				echo'<a href="'.$links.'">
				<div style="'.$style.' font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; color: #FFF; border-radius: 5px;">
				'.$activate.'</div></a>';
				$num = "Student number";
				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; border-radius: 5px;">'.$mode.' student</div>';

				if($mode == "Distance"){
					$sql = "SELECT `Group` FROM `groups` WHERE `StudentID` LIKE '$uid'";
					$run = $this->core->database->doSelectQuery($sql);

					while ($rd = $run->fetch_assoc()) {
						$group = $rd['Group'];
					}

					echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; border-radius: 5px;">Group '.$group.'</div>';
				}
			}


			echo'<a href="'.$this->core->conf['conf']['path'].'/picture/make/'.$uid.'">';
			if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
			} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				echo '<img width="100%" src="'.$this->core->fullTemplatePath.'/designerpack/person-circle.svg" alt="icon" >';
			}
			echo'</a>';
			
			//echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/information/picture/'.$uid.'">';

			if($sstatus=="Approved" || $sstatus=="Deregistered" || $sstatus == "Graduated" || $sstatus == "Employed"){

			if ($this->core->role == 108) {
				echo '<a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Edit user information</b></div></a>';
				echo '<a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Grades</b></div></a>';
				echo '<a href="' . $this->core->conf['conf']['path'] . '/payments/dosa/' . $uid . ''.$other.'"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Show payments and bills</b></div></a>';
				echo '<a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Send student SMS</b></div></a>';
				echo '<a href="' . $this->core->conf['conf']['path'] . '/accommodation/swap/' . $uid . '"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Correct boarding</b></div></a>';
		
			} elseif ($this->core->role == 102) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '">Grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $uid . ''.$other.'">Show payments and bills</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/cards/show/' . $uid . ''.$other.'">EduCard</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'">Send student SMS</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/password/reset/'. $uid .'">Reset password</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/accOverride/'. $uid .'">Add Accommodation override</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/deleteAccOverride/'. $uid .'">Remove Accommodation override</a></b></div>';
	
			} elseif ($this->core->role >= 100) {
				
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/uploadProfileImage/upload/' . $uid . '">Upload Image from files</a></b></div>';
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';

				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '">Grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $uid . ''.$other.'">Show payments and bills</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/cards/show/' . $uid . ''.$other.'">EduCard</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'">Send student SMS</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/cards/print/' . $uid . '">Print card</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/register/course/delete?userid='. $uid .'&cid=all">Cancel approved registration</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/register/course?userid='. $uid .'">Course registration</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/register/moodlereset/'. $uid .'">Reset Moodle Courses</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/password/reset/'. $uid .'">Reset password</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px; "><b><a href="' . $this->core->conf['conf']['path'] . '/meal/results/'. $uid .'">Print mealcard</a></b></div>';
	
			} elseif ($this->core->role <= 10 && $personal == TRUE) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/personal/">Show grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/personal/">Show payments</a></b></div>';
			}
			if ($this->core->role >= 1000) {
				echo '<a href="' . $this->core->conf['conf']['path'] . '/accommodation/assign?userid='. $uid .'"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Assign room</b></div></a>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;  border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/register/course/delete?userid='. $uid .'&cid=all">Delete course registration</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;  border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/programmes/change/'.$uid.'">Change Major/Minor</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/examination/results/'.$uid.'?uid='.$uid.'&period='.$this->core->getPeriod().'">Print Exam Slip</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/swap/' . $uid . '">Correct boarding</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/meal/results/'. $uid .'">Print mealcard</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/accOverride/'. $uid .'">Add Accommodation override</a></b></div>';
				echo '<a href="' . $this->core->conf['conf']['path'] . '/information/deleteAccOverride/'. $uid .'"><div style="border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b>Remove Accommodation override</b></div></a>';

			}
			} elseif($sstatus == "New"){
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; background-color: gold;  padding:10px; border-radius: 5px;"><b><a href="' . $this->core->conf['conf']['path'] . '/register/new/' . $uid . '">REGISTER STUDENT</a></b></div>';		
			} else {
				echo'<a href="' . $this->core->conf['conf']['path'] . '/admission/activate/'.$uid.'"><div style="background-color: red; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; color: #FFF;">ACCOUNT NOT ACTIVE</div></a>';
			}


			echo '</div>
			<div>
			<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
			  <tr>
			<td>'.$num.'</td>
			<td><b>' . $uid . '</b></td>
			  </tr>
			  <tr>
			<td width="200">Gender</td>
			<td><u>' . $sex . '</u></td>
	 		 </tr>
	
	 		 <tr>
			<td>NRC</td>
			<td>' . $nrc . '</td>
	 		 </tr>
	 		 <tr>
			<td>Date of birth</td>
			<td>' . $dob . '</td>
	 		 </tr>
			  <tr>
			<td>Registration status</td>
			<td><b>' . $sstatus . '</b></td>
	 		 </tr>';

			$sql = "SELECT * FROM `roles` WHERE `ID` ='$role'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_assoc()) {

				echo '<tr>
				<td>Access Level</td>
				<td>' . $row['RoleName'] . '</td>
				</tr>';

			}
			
			$sql = "SELECT COUNT(*) AS num FROM `ac_payroll` WHERE `student_id` ='$uid'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_assoc()) {

				$acord = $row['num'];
				
				echo $acord > 0 ? '<tr><td>Accommodation Exemption/ On Payroll</td><td><b> Yes </b></td></tr>' : '' ;

			}

	
				$sql = "
				SELECT MAX(Year) as CurrYear,
				(SELECT ExamCentre FROM `student-data-other` c WHERE a.StudentID=c.StudentID AND c.PeriodID=(SELECT MAX(PeriodID) FROM `course-electives` WHERE StudentID=$uid)) as ExamCentre  
				 FROM `course-electives` a
				LEFT JOIN `course-year-link` ON `course-year-link`.CourseID=a.CourseID 
				LEFT JOIN `student-study-link` ON `course-year-link`.StudyID=`student-study-link`.StudyID 
				WHERE a.StudentID=$uid AND `student-study-link`.StudentID=$uid
				";
				$run = $this->core->database->doSelectQuery($sql);
	

				while ($row = $run->fetch_assoc()) {
					//$studygroup = $row[9];
					//$studygrouptwo = $row[10];

				$year = $row['CurrYear'];
				//$syear = substr($uid,0,4)-$year;
				//if($syear == 0){ $syear = 1; }
				

				$center =$row['ExamCentre'];
				if($center == ''){
					//if ($mode == 'Fulltime'){
						$center = "NIPA";
					//}else{
					//	$center ='<b><a href="' . $this->core->conf['conf']['path'] . '/register/course?userid='. $uid .'">Add exam center</a></b>';
					//}
				}

				echo '<tr>
				<td>Year of Study</td>
				<td>Year <b>' . abs($year) . '</b></td>
				</tr>';
				echo '<tr><td>Exam center</td><td>' . $center . '</td></tr>';


				if(!empty($studygroup)){
					echo '<tr>
					<td>Study Group</td>
					<td>' . $studygroup . ' / ' . $studygrouptwo . '</td>
					</tr>';
				}
			}

			echo '</table></div>';

			$sql = 'SELECT `study`.Name,`study`.ShortName, `schools`.Description as schoolname
				FROM `student-study-link` LEFT JOIN `study` ON `student-study-link`.StudyID=`study`.ID 
				LEFT JOIN  `schools` ON `study`.`ParentID`=`schools`.ID
				WHERE `student-study-link`.StudentID = '.$uid;

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_assoc()) {
				$schoolname = $row['schoolname'];
				$studyname = $row['Name'].' ('.$row['ShortName'].')';
				$shortname = $row['ShortName'];
			}

			if($sstatus !='Employed' ) { 
				
				echo "<div class=\"alert alert-info\" role=\"alert\">Status: "; $this->progressInformation($uid); echo "</div>";
				
				////UPDATE Information On `student-current-year` for the accomodation system
				$studentNameForAcc = $firstname." ".$middlename." ".$surname." | ".$sex ." | ".$shortname." ".$year;	
				$update= "REPLACE INTO `student-current-year`(`StudentID`, `StudentName`) 
				VALUES ($uid, '$studentNameForAcc')";	
				$this->core->database->doInsertQuery($update);
			}
			
			echo '<div><br> <h2>Study information</h2><br>
			<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td width="200">Studying</td>
			<td width=""><b>' . $studyname . '</b></td>
			</tr>
			<tr>
			<td width="200">From</td>
			<td width=""><b>' . $schoolname . '</b></td>
			</tr>';

			$sql = "SELECT * FROM `student-program-link` as sp, `programmes` as p
				WHERE sp.`StudentID` = '$uid' 
				AND sp.`Major` = p.`ID` 
				ORDER BY sp.`ID` DESC LIMIT 1";

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($row = $run->fetch_row()) {

				$name = $row[7];

				echo '<tr>
				<td>Major </td>
				<td width=""><b>' . $name . '</b></td>
				</tr>';

					$student = TRUE;
				
				
			}

			$sql = "SELECT * FROM `student-program-link` as sp, `programmes` as p
				WHERE sp.`StudentID` = '$uid' 
				AND sp.`Minor` = p.`ID`
				ORDER BY sp.`ID` DESC LIMIT 1";

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($row = $run->fetch_row()) {

				$name = $row[7];

				echo '<tr>
				<td>Minor </td>
				<td width=""><b>' . $name . '</b></td>
				</tr>';

					$student = TRUE;
			
			}

			if($sstatus !='Employed' ) {
				
				echo'<tr><td colspan="2"><br><b>COURSE PROGRESSION: </b><br><br>';

				/*$sqls = "SELECT DISTINCT `courses`.`CourseDescription`, `courses`.Name, `periods`.`Year`, `periods`.`Semester`,`course-electives`.Approved AS Approved,(SELECT SUM(amount) FROM acct_invoice WHERE 
				StudentNo=`course-electives`.StudentID  AND AcademicYear=(SELECT `Year` FROM periods WHERE ID =`course-electives`.`PeriodID`) 
				AND semester=(SELECT `Semester` FROM periods WHERE ID =`course-electives`.`PeriodID`)) as invoice
				FROM `course-electives`
				LEFT JOIN `periods` ON `course-electives`.`PeriodID` = `periods`.ID
				LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.ID 
				WHERE `course-electives`.StudentID  = '$uid' 
				AND `course-electives`.Approved IN (1,0)"; 29003759
				*/
				
				$sqls = "SELECT DISTINCT `course-electives`.`ID` AS ID,`courses`.`CourseDescription`, `courses`.Name, `periods`.`Year`, `periods`.`Semester`,`course-electives`.Approved AS Approved,invoice.amount AS invoice
				FROM `course-electives` 
				LEFT JOIN `periods` ON `course-electives`.`PeriodID` = `periods`.ID 
				LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.ID 
				LEFT JOIN (SELECT SUM(amount) AS amount,StudentNo,academicYear,semester FROM acct_invoice WHERE StudentNo='$uid' GROUP BY academicYear,semester) as invoice ON invoice.StudentNo=`course-electives`.StudentID AND
				invoice.academicYear=`periods`.`Year` AND invoice.semester=`periods`.Semester
				WHERE `course-electives`.StudentID = '$uid' AND `course-electives`.Approved IN (1,0,3)";

				$runo = $this->core->database->doSelectQuery($sqls);

				while ($fetchw = $runo->fetch_assoc()) {
					if($year != $fetchw['Year'] . $fetchw['Semester']){
						echo '<br><b>' . $fetchw['Year'].' - Sem. '.$fetchw['Semester'] . '</b> Invoice <b>K'.number_format($fetchw['invoice'],2). '</b><br>';
					}
					
					$approved = $fetchw['Approved'];
					
					if($approved == 1){
						echo'<li>'.$fetchw['Name'].'  - '.$fetchw['CourseDescription'].'</i> ';
						
						if ($this->core->role == 1000 || $this->core->role == 104){
							echo '<a href="' . $this->core->conf['conf']['path'] . '/information/editcourse/'.$fetchw['ID'].'"id="ShowModalButton" onclick="showEditModal()">  <img src="' . $this->core->fullTemplatePath . '/designerpack/pencil-outline.svg" alt="icon" width="20" height="20"></a>';
						}
						
						echo '<br>';
						
					}elseif($approved == 3 ){
						
						echo '<li><s>'.$fetchw['Name'].'  - '.$fetchw['CourseDescription'].'</s></i> ';
						
						echo '<br>';
					}else{
						echo'<font color="grey"><li >'.$fetchw['Name'].'  - '.$fetchw['CourseDescription'].' <b>(Not Approved)</b></i></font>';
						
						if ($this->core->role == 1000 || $this->core->role == 104){
							echo '<a href="' . $this->core->conf['conf']['path'] . '/information/editcourse/'.$fetchw['ID'].'" id="ShowModalButton" onclick="showEditModal()">  <img src="' . $this->core->fullTemplatePath . '/designerpack/pencil-outline.svg" alt="icon" width="20" height="20"></a>';
						}
						
						echo '<br>';
					}
					
					$year = $fetchw['Year'] . $fetchw['Semester'];
					
				}


				if($runo->num_rows == 0){
					echo '<h2>NO COURSES SELECTED</h2>';
				}	

				echo'</p></td></tr>';
			}else{
				echo'<tr><td colspan="2"><br><b>COURSES ASSIGNED: </b><br><br>';

				echo "<table class='table table-dark'><thead><tr><th>#</th><th>Course</th><th>Date/Time</th><th>Mode</th><th>Action</th></tr></thead>";
		
				$sqld = "SELECT a.*,(SELECT CONCAT (CourseDescription,' (',Name,')') FROM courses WHERE ID=a.CourseID) as Course,
				(SELECT CONCAT (FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.LecturerID) as User,
				(SELECT CONCAT (FirstName,' ',Surname,' (',ID,')') FROM `basic-information` WHERE ID=a.UserID) as UserAdd
				 FROM `claim-lecturer-course` a WHERE a.`LecturerID` = '$uid' ";
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
						$session = $rowd['Session'].'-'.$rowd['Campus'];
						
						$output.= "<tr><td>$i</td><td><b>$course</b></td><td>$date</td><td>$session</td>";
						if($this->core->role == 1000 || $this->core->role == 104){
							$output.= "<td><a href='".$this->core->conf['conf']['path'] .'/information/lecturedelete/'.$cid."?uid=".$uid."'>Remove</a></td>";
						}
						$output.= "</tr>";
						$i++;
					}
				}else{
					
					$output.= "<tr><td colspan=5><b>No data found please add some information</b></td>
						</tr>";
				}
				echo $output;
				echo "</tbody></table>";
				if($this->core->role == 1000 || $this->core->role == 104){
					echo "</br></br><H3><b>Add</b> more courses using the form below</H3></br>";
					
					echo '<form id="savelecturercourse" name="savelecturercourse" method="post" action="' . $this->core->conf['conf']['path'] .'/information/savelecturercourse/'.$uid.'">';
					
					include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
			
						$select = new optionBuilder($this->core);
						$claims = $select->showCourses(null);
						$campus = $select->showCenters(null);
						echo 'Course :<select name="course" id="course" class="form-control" style="width: 300px">
											'.$claims.'
										</select></br>';
										
						echo 'Mode  :<select name="sessions" id="sessions" class="form-control" style="width: 300px">
						
										<option value="Fulltime">Fulltime</option>	
										<option value="Parttime">Parttime</option>	
										<option value="Distance">Distance</option>
										
										</select></br>';
						echo 'Campus :<select name="campus" id="campus" class="form-control" style="width: 300px">
											'.$campus.'
										</select></br>';
					
					echo '<button onclick="' . $this->core->conf['conf']['path'] .'/information/savelecturercourse/'.$uid.'?type='.$type.'" class="submit">Add</button>';
					echo '</form>';
				}
				echo'</p></td></tr>';
				
			}

			echo'</table></div>';
	

			$housing = FALSE;

			$sql = "SELECT * 
				FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `housing`.StudentID 
				AND `basic-information`.ID = '$uid'
				AND `housing`.PeriodID = `periods`.ID";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) {

				$AccommodationName = $fetch['HostelName'];
				$RoomNumber = $fetch['RoomNumber'];
				$RoomType = $fetch['RoomType'];	
				$RoomID = $fetch['RoomID'];	
				$weeks = $fetch['Weeks'];

				 
				echo '<div>
				<div class="segment">Housing information</div>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="200">Accommodation</td>
				<td width="">' . $AccommodationName . '</td>
				<tr>
				<td>Room</td>
				<td width=""><a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/'. $RoomID .'">' . $RoomNumber . ' (' . $RoomType . ')</a></td>
				</tr>
				<tr>
				<td>Weeks</td>
				<td width=""><b> ' . $weeks . ' WEEKS</b></td>
				</tr>
				</table></div>';


				$housing = TRUE;
			}

			if($housing == FALSE){
		
				$sql = "SELECT * FROM `housingapplications`, `rooms`, `hostel`,`basic-information`
				WHERE `housingapplications`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `housingapplications`.StudentID 
				AND `basic-information`.ID = '$uid'";
				$run = $this->core->database->doSelectQuery($sql);

				while ($fetch = $run->fetch_assoc()) {

					$AccommodationName = $fetch['HostelName'];
					$RoomNumber = $fetch['RoomNumber'];
					$RoomType = $fetch['RoomType'];	
				
					echo '<div>
					<div class="segment">HOUSING APPLICATION</div>
					<table width="500" height="" border="0" cellpadding="0" cellspacing="0" style="color: #ccc">
					<tr>
					<td width="200">Hostel name</td>
					<td width="">' . $AccommodationName . '</td>
					</tr>
					<tr>
					<td>Room</td>
					<td width="">' . $RoomNumber . ' (' . $RoomType . ')</td>
					</tr>
					</table></div>';


				}
			}

			

			echo '<div><br>
			<h2>Contact Information</h2><br>
			<table width="400" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Streetname</td>
				<td width="">' . $streetname . '</td>
			  </tr>';

			if ($postalcode != "") {
				echo '<tr>
				<td>Postal code</td>
				<td>' . $postalcode . '</td>
			 	</tr>';
			}

			if ($town != "") {
				echo '<tr>
				<td>Town</td>
				<td>' . $town . '</td>
			 	</tr>';
			}

			if ($country != "") {
				echo '<tr>
				<td>Country</td>
				<td>' . $country . '</td>
			  	</tr>';
			}

			echo '<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>';

			if ($homephone != "" && $homephone != "0") {
				echo '<tr>
				<td>Home Phone</td>
				<td>' . $homephone . '</td>
	 			</tr>';
			}

			if ($mobilephone != "" && $mobilephone != "0") {
				echo '<tr>
				<td>Mobile Phone</td>
				<td>' . $mobilephone . '</td>
				</tr>';
			}

			if ($email != "") {
				echo '<tr>
				<td>Private Email</td>
				<td><a href="mailto:' . $email . '">' . $email . '</td>
				</tr>';
			}
			echo'</table></div>';

			$sql = "SELECT * FROM `emergency-contact` WHERE `StudentID` = '" . $nrc . "' OR  `StudentID` = '" . $uid . "' ";
			$run = $this->core->database->doSelectQuery($sql);

	
			while ($fetch = $run->fetch_row()) {

				$fullname = $fetch[2];
				$relationship = $fetch[3];
				$phonenumber = $fetch[4];
				$street = $fetch[5];
				$town = $fetch[6];
				$postalcode = $fetch[7];

			echo '<div><br>
			<h2>Emergency information (Next of Kin)</h2><br>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="200">Full Name</td>
					<td width="">' . $fullname . '</td>
				  </tr>
				  <tr>
					<td>Relationship</td>
					<td>' . $relationship . '</td>
				  </tr>';

				if ($phonenumber != "" && $phonenumber != "0") {
					echo '<tr>
					<td>Phonenumber</td>
					<td>' . $phonenumber . '</td>
					</tr>';
				}

				echo '<tr>
				<td>Street</td>
				<td>' . $street . '</td>
			  </tr>
			  <tr>
				<td>Town</td>
				<td>' . $town . '</td>
			  </tr>
			  <tr>
				<td>Postalcode</td>
				<td>' . $postalcode . '</td>
			  </tr>
			</table></div>';

			}
			
			
/* Education History
			$sql = "SELECT * FROM `education-background` WHERE `StudentID` = '" . $nrc . "' OR  `StudentID` = '" . $uid . "' ";
			$run = $this->core->database->doSelectQuery($sql);
			$n = 0;

			while ($row = $run->fetch_row()) {

				$name = $row[2];
				$type = $row[3];
				$institution = $row[4];
				$filename = $row[5];

				if ($n == 0) {
					echo '<br/><h2>Education history</h2><br>';
					$n++;
				} else {
					echo '<hr>';
				}

				echo '<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Name of institution</td>
				<td width="">' . $institution . '</td>
			  </tr>
			  <tr>
				<td>Level of certificate</td>
				<td>' . $type . '</td>
			  </tr>
			  <tr>
				<td>Name of certificate</td>
				<td>' . $name . '</td>
			  </tr>';

				if ($filename != "") {
					echo '<tr>
					<td>Image of certificate</td>
					<td><a href="' . $this->core->conf['conf']['path'] . 'download/educationhistory/' . $filename . '"><b>View file</b></a></td>
			 		</tr>';
				}
				echo '</table>';
			} */
			echo "</div>";
		}

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}
	}

	private function showInfoList($sql) {

		$sqld = $sql;
		
		$run = $this->core->database->doSelectQuery($sqld);
		
			
		if(!isset($this->core->cleanGet['offset'])){
			
			$_SESSION["recipients"] = $sql;
			$url = $_SERVER['QUERY_STRING'];
			$url = explode('&', $url);
			$url = $url[2]. '&'. $url[3].'&'. $url[4].'&'. $url[5].'&'. $url[6];

			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sms/newbulk">Send SMS to all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/printer/search?'.$url.'">Print letter to all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/export/search?'.$url.'">Export all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sign/search?'.$url.'">Print sign list</a>'.
			'</div>';
			
			if(isset($this->core->cleanGet['period'])){
			
				$period= $this->core->cleanGet['period'];
				
				$sqlPeriod = "SELECT * FROM `periods`	WHERE `periods`.ID = '$period'";
				$runPeriod = $this->core->database->doSelectQuery($sqlPeriod);
				while ($fetch = $runPeriod->fetch_assoc()) {

				$startdate = $fetch['PeriodStartDate'];
				$enddate = $fetch['PeriodEndDate'];
				$name = $fetch['Name'];
				$year = $fetch['Year'];
				$semester = $fetch['Semester'];

					echo '<h2>SELECTED PERIOD '.$year.'  - '.$name.'</h2>';

				}
			}
			
			
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			
			<tbody>';
		}

		$count = $this->offset+1;
		
		while ($row = $run->fetch_assoc()) {
			$results == TRUE;
			
			$id = $row['ID'];
			$NID = $row['GovernmentID'];
			$firstname = $row['FirstName'];
			$middlename = $row['MiddleName'];
			$surname = $row['Surname'];
			$celphone = $row['MobilePhone'];
			$status = $row['Status'];
			$mode = $row['StudyType'];
			$courses = $row['Courses'];
			$examcenter= $row['ExamCentre'];
			$email= $row['PrivateEmail'];
			$currYear= $row['CurrYear'];
			$comment= $row['Comment'];
			
			/***** Extra- code for the study******/
			$sqlx = "SELECT a.Name,a.ShortName,c.Description as school FROM `study` a, `student-study-link` b, schools c
					WHERE a.ID = b.StudyID AND b.StudentID=$id AND b.Status=c.ID";
	
				$runx = $this->core->database->doSelectQuery($sqlx);
	
				while ($row = $runx->fetch_assoc()) {
					$studyName=$row['Name'];
					$studyShortName= $row['ShortName'];
					$school= $row['school'];
				}
			
			/*************************************/
			

			if($firstname == $middlename){
				$middlename ='';
			}
			
			if ($school!=$schoolCurrent){
				echo'<tr>
						<td colspan="9" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$school.'</td>
					</tr>';
			}
			if ($studyName!=$studyNameCurrent){
				echo'<tr>
						<td colspan="9" style="font-size: 9pt; font-weight: bold; border: 1px solid #333; align: center;">'.$studyName.'</td>
					</tr>
					<thead>
						<tr>
							<th bgcolor="#EEEEEE" width="40px">#</th>
							<th bgcolor="#EEEEEE" width="250px" data-sort"string"=""><b>Student Name</b></th>
							<th bgcolor="#EEEEEE"><b>Student Number</b></th>';
							if (!empty($courses)){
								echo '<th bgcolor="#EEEEEE"><b>Course(s)</b></th>';
							}
							echo '<th bgcolor="#EEEEEE"><b>Phone number</b></th>
							<th bgcolor="#EEEEEE"><b>Email</b></th>';
							if (!empty($currYear)){
								echo '<th bgcolor="#EEEEEE"><b>Year</b></th>';
							}
							echo '<th bgcolor="#EEEEEE"><b>Status</b></th>
							<th bgcolor="#EEEEEE"><b>Delivery</b></th>
						</tr>
					</thead>
					';
			}
			
			echo'<tr>
				<td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$surname.' '.$middlename.' '.$firstname.'</b></a></td>
				<td> '.$id.'</td>';
			if (!empty($courses)){
				echo'<td> '.$courses.'</td>';
			}
			 echo'	<td> '.$celphone.'</td>
				<td> '.$email.'</td>';
			if (!empty($currYear)){
				echo'<td> '.$currYear.'</td>';
			}
			if (!empty($examcenter)){
				echo'<td> '.$examcenter.'</td>';
			}else if (!empty($comment)){
				echo'<td> '.$comment.'</td>';
			}else{
			 echo'<td> '.$status.'</td>';	
			}
			echo'<td> '.$mode.'</td>
				</tr>';

			$count++;
			$results = TRUE;
			
			$schoolCurrent=$school;
			$studyNameCurrent=$studyName;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}

		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}


	}
	private function showInfoListCourse($sql) {
		
		$period= $this->core->cleanGet['period'];
		$sqld = $sql;
		
			$sqlPeriod = "SELECT * FROM `periods`	WHERE `periods`.ID = '$period'";
			$runPeriod = $this->core->database->doSelectQuery($sqlPeriod);
			while ($fetch = $runPeriod->fetch_assoc()) {

			$startdate = $fetch['PeriodStartDate'];
			$enddate = $fetch['PeriodEndDate'];
			$name = $fetch['Name'];
			$year = $fetch['Year'];
			$semester = $fetch['Semester'];

				echo '<h2>SELECTED PERIOD '.$year.'  - '.$name.'</h2>';

			}
		
		
		
		$run = $this->core->database->doSelectQuery($sqld);

		if(!isset($this->core->cleanGet['offset'])){
			
			$_SESSION["recipients"] = $sql;
			$url = $_SERVER['QUERY_STRING'];
			$url = explode('&', $url);
			$url = $url[2]. '&'. $url[3].'&'. $url[4];

						
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="40px">#</th>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"=""><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Student Number</b></th>
					<th bgcolor="#EEEEEE"><b>Phone number</b></th>
					<th bgcolor="#EEEEEE"><b>Status</b></th>
					<th bgcolor="#EEEEEE"><b>Delivery</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		$count = $this->offset+1;

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$celphone = $row[14];
			$status = $row[20];
			$mode = $row[19];
			$courses = $row[38];
			
			if(!is_numeric($courses)){
				$courses =1;
			}
			/***** Extra- code for the study******/
			$sqlx = "SELECT a.Name,a.ShortName,c.Description as school FROM `study` a, `student-study-link` b, schools c
					WHERE a.ID = b.StudyID AND b.StudentID=$id AND a.ParentID=c.ID";
	
				$runx = $this->core->database->doSelectQuery($sqlx);
	
				while ($row = $runx->fetch_assoc()) {
					$studyName=$row['Name'];
					$studyShortName= $row['ShortName'];
					$school= $row['school'];
				}
			
			/*************************************/
			

			if($firstname == $middlename){
				$middlename ='';
			}
			
			if ($school!=$schoolCurrent){
				echo'<tr>
						<td colspan="8" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$school.'</td>
					</tr>';
			}
			if ($studyName!=$studyNameCurrent){
				echo'<tr>
						<td colspan="8" style="font-size: 9pt; font-weight: bold; border: 1px solid #333; align: center;">'.$studyName.'</td>
					</tr>';
			}
			echo'<tr>
				<td><img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/user.png"></td><td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
				<td> '.$id.'</td>
				<td> '.$celphone.'</td>
				<td> '.$status.'</td>
				<td> '.$mode.'</td>
				</tr>';
			
			$sqlc = "SELECT DISTINCT `course-electives`.CourseID,courses.Name,courses.CourseDescription,`course-electives`.Approved FROM `course-electives`, courses WHERE `course-electives`.StudentID='$id' and `course-electives`.PeriodID='$period' AND `course-electives`.CourseID=courses.ID";
	
			$runc = $this->core->database->doSelectQuery($sqlc);
	
			while ($rowc = $runc->fetch_assoc()) {
			echo'<tr>
					<td colspan="3">'.$rowc['Name'].' '.$rowc['CourseDescription'].'</td>';
				if($rowc['Approved']==1){
					
					echo'<td colspan="4">Approved</td>';
				}else {
					echo'<td colspan="4">Not Approved</td>';
				}
				echo '</tr>';
			}
			$count++;
			$results = TRUE;
			
			$schoolCurrent=$school;
			$studyNameCurrent=$studyName;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}

		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}


	}

	public function editInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		LEFT JOIN `student-study-link` ON  `student-study-link`.StudentID = `bi`.ID
		WHERE bi.`ID` = '" . $item . "'
		LIMIT 1";

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
			$role = $row[23];
			$status = $row[20];
			$method = $row[19];

			$study = $row[35];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showRoles($role);


			$selectstudy = new optionBuilder($this->core);
			$selectstudy = $selectstudy->showStudies(NULL);


			$major = new optionBuilder($this->core);
			$major = $major->showPrograms(NULL);
			$minor = $major;
		

		}

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}


	public function groupInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }


		$sql = "SELECT `Group` FROM `groups` WHERE `StudentID` LIKE '$uid'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($rd = $run->fetch_assoc()) {
			$group = $rd['Group'];


			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showGroups($group);

		}

		include $this->core->conf['conf']['formPath'] . "editgroup.form.php";

	}
	
	public function accOverrideInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }


		$sql = "INSERT INTO `ac_payroll` (`student_id`, `name`, `authorizedby`, `dateTime`) VALUES ('$item', 'Temp','".$this->core->userID."', NOW()) 
		ON DUPLICATE KEY UPDATE `student_id` ='$item' ";
		$run = $this->core->database->doSelectQuery($sql);

		$this->showInformation($item);


	}
	public function deleteAccOverrideInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }


		$sql = "UPDATE `ac_payroll` SET `student_id`=student_id*7 WHERE `student_id` ='$item' ";
		$run = $this->core->database->doSelectQuery($sql);

		$this->showInformation($item);


	}
	public function editcourseInformation($item) {
		//if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT StudentID,CourseID FROM `course-electives` WHERE `ID` ='$item' ";
		$run = $this->core->database->doSelectQuery($sql);
		while ($rd = $run->fetch_assoc()) {
			$studentID = $rd['StudentID'];
			$courseID = $rd['CourseID'];
		}
		
		echo '<form id="saveeditcourse" name="saveeditcourse" method="post" action="' . $this->core->conf['conf']['path'] .'/information/saveeditcourse/'.$item.'?uid='.$studentID.'">';
					
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$claims = $select->showCoursesEdit($studentID,$courseID);
			echo 'Course :<select name="course" id="course" class="form-control" style="width: 300px">
								'.$claims.'
							</select></br>';
								
		echo '<button onclick="' . $this->core->conf['conf']['path'] .'/information/saveeditcourse/'.$item.'?uid='.$studentID.'" class="submit">Save</button>';
		echo '</form>';

		//$this->showInformation($item);
	}
	public function saveeditcourseInformation($item) {
		$uid = $_GET['uid'];
		$course = $_POST['course'];
		$period = $this->core->getPeriod();;
		$user = $this->core->userID;
		
		$sql = "UPDATE `course-electives` SET `Approved`=3 WHERE `ID` ='$item' ";
		$run = $this->core->database->doSelectQuery($sql);
		
		$sql = "INSERT INTO `course-electives` SET `Approved`=1,`StudentID`=$uid,`CourseID`=$course,`EnrolmentDate`=NOW(), PeriodID=$period, ApprovedBy=$user";
		$run = $this->core->database->doSelectQuery($sql);

		$this->showInformation($uid);
	}
	
	public function savelecturercourseInformation($item) {

		$type  = '';
		
		if (isset($_GET['type'])){
			$type  = $this->core->cleanGet["type"];
		}
		$userid   = $this->core->userID;
		$campus   = $this->core->cleanPost['campus'];
		$sessions   = $this->core->cleanPost['sessions'];
		
		$course   = $this->core->cleanPost["course"]; 
			
		$sql = "INSERT INTO `claim-lecturer-course`(`LecturerID`, `CourseID`, `DateTime`, `UserID`,`Session`,`Campus`) VALUES ('$item' , '$course', NOW(), '$userid','$sessions','$campus')";
		$this->core->database->doInsertQuery($sql);
		
		echo '<span class="successpopup">Your courses have been updated</span>';
		
		$this->showInformation($item);
	}
	public function lecturedeleteInformation($item) {

		if (isset($_GET['uid'])){
			$uid    = $this->core->cleanGet["uid"];
		}
		
			$sql1 = "DELETE FROM `claim-lecturer-course` WHERE ID = $item";
			
			$result1 = $this->core->database->doInsertQuery($sql1);
			
			echo '<span class="successpopup">Course has been removed</span>';
			$this->showInformation($uid);	
	}
	public function progressInformation($items)
	{
		$idnumber = "";
		$courseRegStartDate = "";
		$indiviualFee = "";
		$period = "";
		$FeePercentageToClearToAttendClass = "";
		$FeePercentageToClearExam = "";

		//used to get the current period
		$period  = $this->core->getPeriod();
		//.................................................................................................
		$sql = "SELECT bi.ID FROM `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $items. "' AND ac.`ID` = bi.`ID`";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {

			$idnumber = $row['ID'];
		}
		//.................................................................................................


		//gets the current balanace that the student has from the accounts url in payments 
		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$balance = $payments->getBalance($items);

		//.................................................................................................

		$sql = "SELECT CourseRegStartDate FROM periods p WHERE p.ID ='" . $period . "' ";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {

			$courseRegStartDate = $row['CourseRegStartDate'];
		}
		//.................................................................................................

		//sql to check for the fee percentage
		//check if the student is registered by checking the course elective code
		$sql = " select value from config c where description = 'FeePercentageToClearToAttendClass'; ";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			$FeePercentageToClearToAttendClass = $row['value'];
		}

		$sql = " select value from config c where description = 'FeePercentageToClearExam'; ";
		$run = $this->core->database->doSelectQuery($sql);
					
		while ($row = $run->fetch_assoc()) {
			$FeePercentageToClearExam = $row['value'];
		}

		// $feesToClear = $indiviualFee * ($FeePercentageToClearToAttendClass / 100);
		// $feesToExam = $indiviualFee * ($FeePercentageToClearExam / 100);

		//check if the student is registered by checking the course elective code
		$sql = "SELECT * FROM `course-electives` ce WHERE ce.StudentID = '" . $items . "' and ce.PeriodID = '" . $period . "' ";
		$run = $this->core->database->doSelectQuery($sql);
		//$this->infoSheet();
		if ($run->num_rows >= 1) {
			// echo "<script>
			// alertify.alert('You have already registered for this academic year!');
			// </script>";
			echo "Client is registered for this academic year";

			//gets the current tuition fees for the academic semester 
			$sql = "SELECT amount FROM acct_invoice ai where studentNo = '" . $items . "' and description = 'Tuition Fees' and academicYear = (SELECT year FROM periods p where ID = '" . $period . "') and Semester = (SELECT `Semester` FROM periods WHERE ID = '" . $period . "');";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_assoc()) {
				$indiviualFee = $row['amount'];
			}

			$feestoclear = $indiviualFee - $balance;
			$percentageToClear = 50;
			
			//calculate how much the student has to pay in order for him to write the exam

			//check if the balance is a negative then the school owes the student 
			//if the balance is a positive then the student owes the school
			if( $balance > 0 ){
				echo "Client owes the institution K" . $balance;
			}
			elseif($balance < 0){
				echo ( "Client has outstanding bills for this semester, account balance is K" . $balance);
			}

		} else {
			//echo "<script>
			//alert('You have not yet registered!');
			//</script>";

			if( $balance > 0 ){
				echo "Client owes the institution K" . $balance;
			}
			elseif($balance < 0){
				echo "Client has no outstanding bills for this semester,  account balance is K" . $balance;
			}
		}
		
	}

}

?>