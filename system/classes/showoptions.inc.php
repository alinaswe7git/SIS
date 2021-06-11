<?php
class optionBuilder {

	public $core;

	public function __construct($core) {

		$this->core = $core;
	}

	public function buildSelect($run, $selected = NULL) {
		$begin = "";
		$out = "";

		if (!empty($run)) {

			foreach ($run as $row) {

				$name = $row[1];
				$uid = $row[0];

				if ($uid == $selected) {
					$sel = 'selected="selected"'; 
				} else {
					$sel = "";
				}

				if ($uid == $selected) {
					$begin = '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';
				}else{
					$out = $out . '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';
				}
			}

		} else {

			$out = $out . '<option value="">No information available</option>';

		}

		$out = $begin . $out;
		return $out;
	}

	function showPeriods($study = null, $selected = null) {

		if ($study != null) {
			$sql = "SELECT `periods`.ID, CONCAT(`periods`.Year, ' - ', `periods`.Semester) FROM `periods` ORDER BY `ID` DESC";
		} else {
			$sql = "SELECT `periods`.ID, CONCAT(`periods`.Year, ' - ', `periods`.Semester) FROM `periods` ORDER BY `ID` DESC";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showCentres($centre, $selected = null) {

		if ($study != null) {
			$sql = "SELECT DISTINCT ID,Description FROM schools";
		} else {
			$sql = "SELECT DISTINCT ID,Description FROM schools";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	/*function showCentres($centre, $selected = null) {

		if ($study != null) {
			$sql = "SELECT  DISTINCT `student-data-other`.ExamCentre, `student-data-other`.ExamCentre FROM `student-data-other` WHERE PeriodID =".$this->core->getPeriod()." ORDER BY `student-data-other`.ExamCentre";
		} else {
			$sql = "SELECT  DISTINCT `student-data-other`.ExamCentre, `student-data-other`.ExamCentre FROM `student-data-other` WHERE PeriodID =".$this->core->getPeriod()." ORDER BY `student-data-other`.ExamCentre";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}*/

	function showCenters($center, $selected = null) {
		$sql = "SELECT `name`, `name` FROM `exam_centers` ORDER BY `exam_centers`.`name`";
		
		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	
	function showTrailYear($center, $selected = null) {
		$sql = "SELECT YEAR(`TDate`) as `YT`, YEAR(`TDate`) as `YT` FROM `paymets-trail` GROUP BY YEAR(`TDate`) ORDER BY YEAR(`TDate`)";
		
		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showPrograms($study, $selected = null) {

		if ($study != null) {
			$sql = "SELECT `programmes`.ID, `programmes`.ProgramName FROM `programmes`, `study-program-link` WHERE `study-program-link`.StudyID = '$study' AND `study-program-link`.ProgramID = `programmes`.ID ORDER BY `programmes`.`ProgramName`";
		} else {
			$sql = "SELECT `ID`, `ProgramName` FROM `programmes` ORDER BY `ProgramName`";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	

	function showFeepackages($study, $selected = null) {

		if ($study != null) {
			$sql = "SELECT * FROM `fee-package`,`fee-package-study-link` WHERE `fee-package-study-link`.StudyID = '$study' AND `fee-package-study-link`.FeePackageID = `fee-package`.ID";
		} else {
			$sql = "SELECT * FROM `fee-package` ORDER BY `fee-package`.Name";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}


	function showCourses($program, $selected = null) {

		if ($program != null) {
			$sql = "SELECT * FROM `courses`, `programmes`, `program-course-link` WHERE `program-course-link`.CourseID = `courses`.ID AND `program-course-link`.ProgramID = `programmes`.ID AND `program-course-link`.ProgramID = $program ORDER BY `courses`.`Name`";
		} else {
			$sql = "SELECT `ID`, CONCAT (`Name`,' (',`CourseDescription`,')') AS `Name` FROM `courses` WHERE `courses`.`CourseDescription` <> '' ORDER BY `courses`.`Name` ";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	function showClaimCourses($program, $selected = null) {

		if ($program != null) {
			$sql = "SELECT `ID`, CONCAT (`Name`,' (',`CourseDescription`,')') AS `Name` FROM `courses` WHERE ID IN (SELECT CourseID FROM `claim-lecturer-course` WHERE LecturerID=$program) ORDER BY `courses`.`Name`";
		} else {
			$sql = "SELECT `ID`, CONCAT (`Name`,' (',`CourseDescription`,')') AS `Name` FROM `courses` WHERE `courses`.`CourseDescription` <> '' ORDER BY `courses`.`Name`  ";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	
	function showCoursesEdit($StudentID, $selected = null) {

		if ($program != null) {
			$sql = "SELECT `ID`, CONCAT (`Name`,' (',`CourseDescription`,')') AS `Name` FROM `courses` WHERE ID IN (SELECT CourseID FROM `course-year-link` WHERE StudyID IN (SELECT StudyID FROM `student-study-link` WHERE StudentID =$StudentID)) ORDER BY `courses`.`Name`";
		} else {
			$sql = "SELECT `ID`, CONCAT (`Name`,' (',`CourseDescription`,')') AS `Name` FROM `courses` WHERE `courses`.`CourseDescription` <> '' ORDER BY `courses`.`Name`  ";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	
	function showAllcourses($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `courses`  WHERE `courses`.`CourseDescription` <> '' ORDER BY `courses`.`Name`";
		
		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showCourseList($program, $year, $semester) {

		$sql = "SELECT * FROM `courses`, `study`, `course-year-link` 
			WHERE `course-year-link`.CourseID = `courses`.ID 
			AND `course-year-link`.StudyID = `study`.ID
			AND `study`.ID = $program 
			AND `course-year-link`.Year = $year
			AND `course-year-link`.Semester = $semester
			ORDER BY `courses`.`Name`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}


	function showPCourses($program, $selected = null) {

		if ($program != null) {
			$sql = "SELECT * FROM `courses`, `course-prerequisites` WHERE `course-prerequisites`.Prerequisites= `courses`.ID AND `course-prerequisites`.CourseID = $program ORDER BY `courses`.`Name`";
		} else {
			$sql = "SELECT `ID`, `Name` FROM `courses` ORDER BY `courses`.`Name`";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showPermissions($selected = null) {

		$sql = "SELECT `ID`, `PermissionDescription` FROM `permissions`";

		$run = $this->core->database->doSelectQuery($sql);
		$out = $this->core->database->fetch_all($run);

		return ($out);
	}

	function showAccommodation($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `accommodation`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showCoursesV($program, $selected = null) {

		if ($program != null) {
			$sql = "SELECT `Name`, `Name` FROM `courses`, `programmes`, `program-course-link` WHERE `program-course-link`.CourseID = `courses`.ID AND `program-course-link`.ProgramID = `programmes`.ID AND `program-course-link`.ProgramID = $program ORDER BY `courses`.`Name`";
		} else {
			$sql = "SELECT `ID`, `Name` FROM `courses` ORDER BY `courses`.`Name`";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showUsers($role, $selected = null) {

		$sql = "SELECT `basic-information`.`ID`, CONCAT(`FirstName`, ' ', `Surname`) FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= '$role'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showPaymentTypes($selected = null) {

		$sql = "SELECT `ID`, `Value`, `Name` FROM `settings` WHERE `Name` LIKE 'PaymentType%' ORDER BY `Name`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showSchools($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `schools`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showSubjects($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `appl_subjects`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showCountry($selected = null) {

		$sql = "SELECT `country_id`, `short_name` FROM `appl_country`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}


	function showClaimcategory($selected = null) {

		$sql = "SELECT `ID`, CONCAT(`Name`,'-(K ',`Rate`,')') AS Name FROM `claim-category`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showStudies($selected = null) {

		$sql = "SELECT `ID`, CONCAT(`Name`,' (',`ShortName`,')') AS `Name` FROM `study` ORDER BY `Name`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	function showRoles($selected = null) {

		$sql = "SELECT `ID`, `RoleName` FROM `roles`";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
//............................................................summery...............................................................

	function showKin($id,$selected = null) {

		$sql = "SELECT `id`, `applicantno` FROM `appl_employment` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $this->core->database->fetch_all($run);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	
	
	function showAllKin($id,$selected = null) {

		$sql = "SELECT * FROM `appl_nextofkin` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}
	
		function showallprograminfo($id,$selected = null) {

		$sql = "SELECT * FROM `appl_program` WHERE `applicantno` = '$id' " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}
	
	
	
	function showPersonal($id,$selected = null) {

		$sql = "SELECT * FROM `appl_personal` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showProgram($id,$selected = null) {

		$sql = "SELECT ap.`level`, ap.modeofstudy, ap.campus, ap.knowhow,(SELECT s2.Name FROM study s2 WHERE s2.ID = ap.program) as program FROM appl_program ap WHERE ap.applicantno = $id";
		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showKins($id,$selected = null) {

		$sql = "SELECT * FROM `appl_nextofkin` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showSponsor($id,$selected = null) {

		$sql = "SELECT * FROM `appl_sponsor` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showEmployment($id,$selected = null) {

		$sql = "SELECT * FROM `appl_employment` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showGrade12($id,$selected = null) {

		$sql = "SELECT * FROM `appl_exam` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	function showPreviousschool($id,$selected = null) {

		$sql = "SELECT * FROM `appl_schools` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showOlevel($id,$selected = null) {

		//$sql = "SELECT * FROM `appl_grades` WHERE `applicantno` = $id " ;

		$sql = "SELECT (SELECT asub.name FROM appl_subjects as asub where asub.id = ag.subject_id ) as subject, ag.`level`,ag.grade FROM `appl_grades` as ag WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showTertiaryeducation($id,$selected = null) {

		//$sql = "SELECT * FROM `appl_grades` WHERE `applicantno` = $id " ;

		$sql = "SELECT * FROM `appl_professional` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showUploads($id,$selected = null) {

		$sql = "SELECT * FROM `appl_attachments` WHERE `applicantno` = $id " ;

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}

	//admission process code

	function showApplicant($selected = null) {

		$sql = "SELECT DISTINCT ap.applicantno, ap.firstname, ap.lastname, ap.gender, ap.NRCnumber,ap.email, (SELECT s2.name FROM study s2 WHERE s2.ID = ap2.program) as program FROM appl_personal ap, appl_program ap2, appl_status_submit ass where ap.applicantno = ap2.applicantno and ap.applicantno != ass.applicantno" ;

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showAdmitted($selected = null) {

		$sql = "SELECT ap.applicantno, ap.firstname, ap.lastname, ap.gender, ap.NRCnumber,ap.email, (SELECT s2.name FROM study s2 WHERE ID = ap2.program) as program FROM appl_personal ap, appl_program ap2, appl_status as1 WHERE as1.applicantno = ap.applicantno" ;

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showRejected($selected = null) {

		$sql = "SELECT DISTINCT ap.applicantno, ap.firstname, ap.lastname, ap.gender, ap.NRCnumber,ap.email, (SELECT s2.name FROM study s2 WHERE ID = ap2.program) as program FROM appl_personal ap, appl_program ap2, appl_status as1 WHERE ap.applicantno = as1.applicantno and as1.status = 'rejected' ";

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

//list of auto selected and auto rejected.......BACHELORS....................
//list of auto selected and auto rejected.......BACHELORS....................
	function showAutoselected($selected = null) {

		$sql = "SELECT asa.applicantno, asa.firstname, asa.middlename, asa.lastname, ap.`level`, ap.modeofstudy, (SELECT s2.Name from study s2 where s2.ID = ap.program ) as program FROM appl_subject_agregate_vw asa, appl_program ap where asa.applicantno = ap.applicantno AND ap.`level` = 'undergraduate' AND ENG >= 1 AND MAT >=1 AND OGT5 >= 3;";

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showAutoRejected($selected = null) {

		$sql = "SELECT asa.applicantno, asa.firstname, asa.middlename, asa.lastname, ap.`level`, ap.modeofstudy, (SELECT s2.Name from study s2 where s2.ID = ap.program ) as program FROM appl_subject_agregate_vw asa, appl_program ap where asa.applicantno = ap.applicantno AND ap.`level` = 'undergraduate' AND ENG != 1 AND MAT !=1 AND OGT5 <= 3;";

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	//List of auto selected and auto rejected ..............Diploma......
	function showAutoselectedDiploma($selected = null) {

		$sql = "SELECT asa.applicantno, asa.firstname, asa.middlename, asa.lastname, ap.`level`, ap.modeofstudy,(SELECT s2.Name from study s2 where s2.ID = ap.program ) as program FROM appl_subject_agregate_vw asa, appl_program ap where asa.applicantno = ap.applicantno AND ap.`level` = 'diploma' AND ENG >= 1 AND MAT >=1 AND (OGT5 >= 1 AND OGT8 >= 2 );";

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}

	function showAutoRejectedDiploma($selected = null) {

		$sql = "SELECT asa.applicantno, asa.firstname, asa.middlename, asa.lastname, ap.`level`, ap.modeofstudy,(SELECT s2.Name from study s2 where s2.ID = ap.program ) as program FROM appl_subject_agregate_vw asa, appl_program ap where asa.applicantno = ap.applicantno AND ap.level = 'diploma' AND ENG != 1 or MAT !=1 or (OGT5 != 1 or OGT8 != 2 );";

		$run = $this->core->database->doSelectQuery($sql);
		//$fetch = $run->mysql_fetch_array();

		return ($run);
	}


	function showLastID($selected = null) {

		$sql = "SELECT * FROM `student-study-link` ORDER BY StudentID DESC LIMIT 1;";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return ($fetch);
	}





}

?>
