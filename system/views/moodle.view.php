<?php
class moodle {

	public $core;
	public $view;
	public $moodle;

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


	public function updateMoodle(){
		$date = date("Y-m-d H:i:s");

		echo $date . " - Started Moodle course and program synchronization\n";

		$this->core->conf['mysql']['db'] = $this->core->conf['moodle']['database'];
		$connect = new database($this->core);

		// $this->createCourses($connect);
		$this->createPrograms($connect);

		echo $date . " - Completed Moodle course and program synchronization\n";
	}

	private function createCourses($connection){

		$sql = "SELECT `courses`.ID, `Name`, `CourseDescription`, `StudyID` FROM `courses`, `course-year-link` WHERE `courses`.ID = `course-year-link`.`CourseID`";
		$run = $this->core->database->doSelectQuery($sql);

		$table = $this->core->conf['moodle']['prefix'] . '_course';

		while ($fetch = $run->fetch_assoc()) {
			$code = $fetch['Name'];
			$description = $fetch['CourseDescription'];
			$name = $code . " - " . $description;
			$courseid= $fetch['ID'];
			$segments = 5;
			$programid = $fetch['StudyID'];

			$insert = "INSERT IGNORE INTO `$table` (`id`, `category`, `sortorder`, `fullname`, `shortname`, `idnumber`, `summary`, `summaryformat`, `format`, `showgrades`, `newsitems`, `startdate`, `enddate`, `marker`, `maxbytes`, `legacyfiles`, `showreports`, `visible`, `visibleold`, `groupmode`, `groupmodeforce`, `defaultgroupingid`, `lang`, `calendartype`, `theme`, `timecreated`, `timemodified`, `requested`, `enablecompletion`, `completionnotify`, `cacherev`) 
			VALUES ($courseid, '$programid', '0', '$name', '$code', '$courseid', '$description', '1', 'weeks', '0', '$segments', '1514782800', '1520838000', '0', '0', '0', '0', '1', '1', '0', '0', '0', '', '', '', '1520861451', '1520861451', '0', '1', '0', '1520861464');";
			$connection->doInsertQuery($insert);
			echo $insert;
		}
	}


	private function createPrograms($connection){

		$sql = "SELECT * FROM `programmes`";
		$run = $this->core->database->doSelectQuery($sql);

		$table = $this->core->conf['moodle']['prefix'] . '_course_categories';





		while ($fetch = $run->fetch_assoc()) {
			$programid = $fetch['ID'];
			$name = $fetch['ProgramName'];
			$description = $fetch['ProgramName'];


			$token = '1f28bca78b640f1105b2f57bd06cc65b';
			$domainname = 'https://www.nkrumah.edu.zm/moodle';
			$functionname = 'core_course_create_categories';
			$restformat = 'json';

			$category = new stdClass();
			$category->name = $name;
			$category->parent = 0;
			$category->idnumber = $programid;
			$category->description = '<p>'.$name.'</p>';
			$category->descriptionformat = 1;
			$categories = array( $category);
			$params = array('categories' => $categories);

			header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once('/data/website/curl.php');
			$curl = new curl;
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			print_r($resp);

			$insert = "INSERT IGNORE INTO `$table` (`id`, `name`, `idnumber`, `description`, `descriptionformat`, `parent`, `sortorder`, `coursecount`, `visible`, `visibleold`, `timemodified`, `depth`, `path`, `theme`) 
			VALUES ('$programid', '$name', '$programid', '$description', '1', '0', '20000', '10', '1', '1', '1520861483', '0', '/$programid', NULL);";
			//$connection->doInsertQuery($insert);
		}
	}
	/*
	private function addcoursemoodle ($courseno, $coursename, $school) { 
		$dbConn2 = connectToDb2();
		// get category id from course category
		$query1 = "SELECT id FROM mdl_course_categories where name='$school'";
		$result1 = mysql_query($query1, $dbConn2);
		$row1 = mysql_fetch_assoc($result1);
		$query2 = "SELECT sortorder,category FROM `mdl_course` where category='$row1[id]' order by sortorder desc limit 1";
		$result2 = mysql_query($query2, $dbConn2);
		$row2 = mysql_fetch_assoc($result2);
		
		$sortorder=$row2[sortorder] + 1;
			  $query3 = "INSERT INTO `mdl_course` (summary,sortorder, category, fullname, shortname, idnumber, summaryformat, format, showgrades, modinfo, newsitems, startdate, numsections, marker, maxbytes, legacyfiles, showreports, visible, visibleold, hiddensections, groupmode, groupmodeforce,`defaultgroupingid`, timecreated, `timemodified`,requested,restrictmodules, enablecompletion,completionstartonenrol, completionnotify) VALUES ('','$sortorder','$row2[category]', '$courseno $coursename', '$courseno', '$courseno', 1, 'weeks', 1, 'a:0:{}', 5, UNIX_TIMESTAMP(NOW()), 10, 0, 104857600, 0,0,1,1,0, 0,0,0, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 0,0,0,0,0)";
		$result3 = mysql_query($query3, $dbConn2);
		$query4 = "SELECT id FROM `mdl_course` where shortname='$courseno'";
		 $result4 = mysql_query($query4, $dbConn2);
		$row4 = mysql_fetch_assoc($result4);		 
		  $query5="INSERT INTO mdl_enrol (id,	enrol, 	status, courseid, sortorder,name,enrolperiod,	enrolstartdate,	enrolenddate,	expirynotify,	expirythreshold, notifyall, password, cost, currency, roleid,	customint1, customint2, customint3, customint4,	customchar1,	customchar2,	customdec1, customdec2, customtext1, customtext2, timecreated, 	timemodified) VALUES (NULL, 'manual', 0, $row4[id], 0,NULL,	0, 	0, 	0, 	0, 	0, 	0, 	NULL, NULL,	NULL,	5, 	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()))";
		$result5 = mysql_query($query5, $dbConn2);
	}


	private function addmoodle ($studentno, $academicyear, $semester)
	{
		//	print "($studentno, $academicyear, $semester)";
		$dbConn = connectToDb();
		$sql = "SELECT * FROM courseregister WHERE studentno='$studentno' and academicyear='$academicyear' and semester='$semester'";
		$sqlresult = mysql_query($sql, $dbConn);
		$sqlrow = mysql_fetch_assoc($sqlresult);
		$studentname = $sqlrow[studentname];
		$studentno = $sqlrow[studentno];
		$i=1;
		while ($i<9)
		{
			$j='course'.$i;
			$courseno=$sqlrow[$j];
			$dbConn2 = connectToDb2();
			$query = "SELECT id FROM `mdl_course` where shortname='$courseno'";
			$result = mysql_query($query, $dbConn2) or die(mysql_error());
			$row = mysql_fetch_array($result);
			$instanceid=$row[0];

			$query = "SELECT id FROM `mdl_context` where instanceid='$instanceid' and depth=3";
			$result = mysql_query($query, $dbConn2) or die(mysql_error());
			$row = mysql_fetch_array($result);
			$contextid=$row[0];

			$query = "SELECT id FROM `mdl_enrol` where courseid='$instanceid' and enrol='manual'";
			$result = mysql_query($query, $dbConn2) or die(mysql_error());
			$row = mysql_fetch_array($result);
			if (mysql_num_rows($result) >= 1) {
			$enrolid=$row[0];
			//print "<br>$studentno,  $studentname, $instanceid, $contextid, $enrolid";
				getuserid ($studentno,  $studentname, $instanceid, $contextid, $enrolid);
			}

			$i=$i+1;
		}

	}

	function getuserid ($studentno, $studentname, $instanceid, $contextid, $enrolid){
	//make the database connection
	$dbConn2 = connectToDb2();

	$query4 = "SELECT id FROM `mdl_user` where username like '$studentno'";

	$result4 = mysql_query($query4, $dbConn2) or die(mysql_error());
	$row4 = mysql_fetch_array($result4);
	if ($row4[0]>0)
	{
	$userid=$row4[0];
	roleassignments ($contextid, $userid);
	userenrollments ($enrolid, $userid, $studentno, $studentname);
	}
	}

	function roleassignments ($contextid, $userid){
	//make the database connection
	$dbConn2 = connectToDb2();
	$query = "select * from `mdl_role_assignments` where `roleid`='5' and `contextid`='$contextid' and `userid`='$userid'";
	$result = mysql_query($query,$dbConn2);

	if (mysql_num_rows($result) <> 1) {
	$query4 = "INSERT INTO `mdl_role_assignments` (`id`, `roleid`, `contextid`, `userid`, `timemodified`, `modifierid`, `component`, `itemid`, `sortorder`) VALUES (NULL, '5', '$contextid', '$userid', UNIX_TIMESTAMP(NOW()), '0', '', '0', '0')";
	$result4 = mysql_query($query4, $dbConn2) or die(mysql_error());
	//print "<br>$query4<br>";
	}
	}

	function userenrollments ($enrolid, $userid, $studentno, $studentname){
	//make the database connection
	$dbConn2 = connectToDb2();
	$query = "select * from `mdl_user_enrolments` where `enrolid`=$enrolid and `userid`=$userid";
	$result = mysql_query($query,$dbConn2);
	if (mysql_num_rows($result) == 1) {
	$query6 = "UPDATE `mdl_user_enrolments` SET `timestart`=UNIX_TIMESTAMP(NOW()), `timeend`=UNIX_TIMESTAMP(ADDDATE(NOW(),120)), `modifierid`=UNIX_TIMESTAMP(NOW()) WHERE `enrolid`='$enrolid' and `userid`='$userid'";
	}
	else
	{
	$query6 = "INSERT INTO `mdl_user_enrolments` (`id`, `status`, `enrolid`, `userid`, `timestart`, `timeend`, `modifierid`, `timecreated`, `timemodified`) VALUES (NULL, '0', '$enrolid', '$userid', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(ADDDATE(NOW(),120)), '0', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()))";
	// select UNIX_TIMESTAMP('2014-03-30 00:00:00'),UNIX_TIMESTAMP(ADDDATE(CURDATE(),120)), FROM_UNIXTIME(2147483647) 
	}
	$result6 = mysql_query($query6, $dbConn2) or die(mysql_error());
	//print "$studentname - $studentno<br>";
	//print "<br>$query<br>$query6<br>";
	}
	*/
	
	
}
?>