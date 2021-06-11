<?php
class programmes {

	public $core;
	public $view;

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

	public function editProgrammes($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$sql = "SELECT  `study`.ID,  `study`.Name, `study`.StudyType, `study`.TimeBlocks,`study`.ShortName,`study`.IntakeMax
			FROM `study`
			WHERE `study`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$study = $fetch['ID'];
			$year = $fetch['TimeBlocks'];

			$select = new optionBuilder($this->core);
			$users = $select->showUsers("100", $fetch[4]);
			$schools = $select->showSchools();

			// FIRST YEAR
			$selectedcoursest11 = $select->showCourseList($study, 1, 1);
			$selectedcoursest12 = $select->showCourseList($study, 1, 2);

			// SECOND YEAR
			$selectedcoursest11 = $select->showCourseList($study, 2, 1);
			$selectedcoursest12 = $select->showCourseList($study, 2, 2);

			// THIRD YEAR
			$selectedcoursest11 = $select->showCourseList($study, 3, 1);
			$selectedcoursest12 = $select->showCourseList($study, 3, 2);

			// FOURTH YEAR
			$selectedcoursest11 = $select->showCourseList($study, 4, 1);
			$selectedcoursest12 = $select->showCourseList($study, 4, 2);

			$notselectedcourses = $select->showCourses(NULL);

			include $this->core->conf['conf']['formPath'] . "editprogramme.form.php";
		}

	}

	public function changeProgrammes($item) {

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$study = 		$select->showStudies(null);
		$major = 		$select->showPrograms(null);
		$minor = 		$select->showPrograms(null);
		
		
		include $this->core->conf['conf']['formPath'] . "changeprogramme.form.php";
		

		$study = $this->core->cleanPost['study'];
		$major = $this->core->cleanPost['major'];
		$minor = $this->core->cleanPost['minor'];


		if(isset($study) && isset($major) && isset($minor)){

			$sql = "UPDATE `student-program-link` SET `Major` = '$major', `Minor` = '$minor' WHERE `StudentID` = '$item'";
			$run = $this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `student-study-link` SET `StudyID` = '$study' WHERE `StudentID` = '$item'";
			$run = $this->core->database->doInsertQuery($sql);

			echo '<span class="successpopup">Information updated. Go <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">back to profile.</a></span>';

		}


	}

	public function addProgrammes() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$users = $select->showUsers("100", null);
		
		include $this->core->conf['conf']['formPath'] . "addprogramme.form.php";
	}

	public function deleteProgrammes($item) {
		$sql = 'DELETE FROM `programmes`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("programmes", "manage", NULL);
	}

	public function saveProgrammes() {
		$item = $this->core->cleanPost['item'];
		$name = $this->core->cleanPost['name'];
		$type = $this->core->cleanPost['programtype'];
		$coordinator = $this->core->cleanPost['coordinator'];
		$description = $this->core->cleanPost['description'];

		$year = $this->core->cleanPost['year'];
		$semester = $this->core->cleanPost['semester'];

		$selected = $this->core->cleanPost['selected'];
		$nselected = $this->core->cleanPost['nselected'];

		if (!empty($nselected)) {
			foreach ($nselected as $nsel) {
				$sql = "INSERT INTO `course-year-link` (`ID`, `CourseID`, `StudyID`, `Year`, `Semester`, `StudyCode`) 
					VALUES (NULL, '$nsel', '$item', '$year', '$semester', '');";

				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($selected)) { 
			foreach ($selected as $sel) {
				$sql = "DELETE FROM `course-year-link` WHERE `StudyID` = $item AND `CourseID` = $sel";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($item)) {
			$sql = "UPDATE `study` SET `ProgramType` = '$type', `Name` = '$name' WHERE `study`.`ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `study` (`ID`, `ProgramType`, `ProgramName`, `ProgramCoordinator`) VALUES (NULL, '$type', '$name', '$coordinator');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("programmes", "edit", $item);
	}

	public function manageProgrammes($item = NULL) {
		$sql = "SELECT *, COUNT(`course-year-link`.ID) as COUNT, `study`.ID as ID
			FROM `study` LEFT JOIN `course-year-link` ON `course-year-link`.StudyID = `study`.ID
			GROUP BY `study`.ID 
			ORDER BY `study`.Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/programmes/add">Add programme</a></div>' .
             	'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">' .
             	'<tr class="tableheader">' .
             	'<td><b>Programme name</b></td>' .
		'<td><b>Number of Courses</b></td>' .
		'<td><b>Programme Type</b></td>' .
		'<td><b>Management tools</b></td>' .
		'</tr>';

		$count = 0;
		$first = 1;
		$i = 0;
		$rest = NULL;
		$temp = NULL;

		while ($fetch = $run->fetch_assoc()) {
			$studytype  = $fetch['StudyType'];
			$count  = $fetch['COUNT'];

			if ($studytype == "1") {
				$type = "Master";
			} else if ($studytype == "2") {
				$type = "Bachelor";
			} else if ($studytype == "3") {
				$type = "Certificate";
			} else if ($studytype == "5") {
				$type = "Diploma";
			} else {
				$type = "Unknown";
			}

			echo '<tr ' . $bgc . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $fetch['ID'] . '"> ' . $fetch['Name'] . ' ('.$fetch['ShortName'].')</a></b></td>
				<td> ' . $count . ' </td>
				<td> ' . $type . ' </td>
				<td>
				<a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $fetch['ID'] . '"> <img src="'. $this->core->fullTemplatePath .'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/programmes/delete/' . $fetch['ID'] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'. $this->core->fullTemplatePath .'/images/del.png"> delete </a>
				</td>
				</tr>';

			$first = 3;
			$count = 0;
			$temp = $fetch[0];			
		}


		echo '</table>';
		$temp = $fetch[0];
	}


	public function showProgrammes($item) {
		$sql = "SELECT * FROM `programmes` 
			LEFT JOIN `basic-information` ON  ProgramCoordinator = `basic-information`.ID 
			WHERE `programmes`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {

			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
				  <tr>
					<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
					<td bgcolor="#EEEEEE"></td>
					<td  bgcolor="#EEEEEE"></td>
				  </tr>
					<tr>
					<td width="150"><b>Name of Programme</b></td>
					<td><b>' . $fetch[2] . '</b></td>
					<td></td>
					</tr>
					<tr>
					<td width="150"><b>Programme Coordinator</b></td>
					<td><a href="' . $this->core->conf['conf']['path'] . 'information/show/' . $fetch[8] . '">' . $fetch[4] . ' ' . $fetch[6] . '</b></td>
					<td></td>
					</tr>
					<tr><td>Programme Type</td>
					<td>';

			if ($fetch[1] == "0") {
				echo 'No type selected';
			}
			if ($fetch[1] == "1") {
				echo 'Minor';
			}
			if ($fetch[1] == "2") {
				echo 'Major';
			}
			if ($fetch[1] == "3") {
				echo 'Available as both';
			}

			echo '</select></td>
                <td></td>
                </tr><tr><td>Courses</td>
                <td>';

			$sql = "SELECT * FROM `courses`, `course-year-link` 
				WHERE `course-year-link`.CourseID = `courses`.ID 
				AND `course-year-link`.StudyID = '$fetch[0]'";

			$run = $this->core->database->doSelectQuery($sql);

			$i = 1;

			while ($fetchs = $run->fetch_assoc()) {

				echo '<li><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetchs['ID'] . '">' . $fetchs['Name'] . ' - ' . $fetchs['CourseDescription'] . '</a></li>';
				$i++;

			}

			if ($i == 1) {
				echo 'No courses have been added to the program yet. Please <a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $fetch[0] . '">add some.</a>';
			}

			echo '</td>
                <td></td>
                </tr></table>
                </p>';
		}
	}
}

?>
