<?php
class report {

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


	private function viewMenu($item){

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$periods = $select->showPeriods();

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		$level = $this->core->cleanGet['level'];

		$sql = "SELECT * FROM `periods`	WHERE `periods`.ID = '$period'";
		$run = $this->core->database->doSelectQuery($sql);

		echo'<form id="narrow" name="narrow" method="get" action="">
			<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/report/intake?period='.$period.'&mode='.$mode.'&level='.$level.'">Intake Report</a>
			<a href="' . $this->core->conf['conf']['path'] . '/report/program?period='.$period.'&mode='.$mode.'&level='.$level.'">Program Report</a>
			<a href="' . $this->core->conf['conf']['path'] . '/report/school?period='.$period.'&mode='.$mode.'&level='.$level.'">School Report</a>
			<a href="' . $this->core->conf['conf']['path'] . '/report/year?period='.$period.'&mode='.$mode.'&level='.$level.'">By Year Report</a>
			<a href="' . $this->core->conf['conf']['path'] . '/report/income">School Income Report</a>
		
				<div class="toolbaritem">Filter to show students from: 
					<select name="period" class="submit" style="width: 180px; margin-top: -17px; background-color: #000;">
					<option value=""> -- SELECT A PERIOD -- </option>
					'. $periods .'
					</select>


					<select name="mode" id="mode" class="submit" style="width: 130px; margin-top: -17px; background-color: #000;" >
						<option value=""> -- DELIVERY -- </option>
						<option value="Fulltime">Fulltime</option>
						<option value="Distance">Distance</option>
						<option value="Partime">Part-time</option>
						<option value="Both">Combined</option>
					</select>
					<select name="level" id="level" class="submit" style="width: 110px; margin-top: -17px; background-color: #000;" >
						<option value=""> -- Level -- </option>
						<option value="2,3">Under-graduate</option>
						<option value="4,5,6">Post-graduate</option>
						<option value="2,3,4,5,6">Combined</option>
					</select>
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px; background-color: #000;"/>
				</div>
			</div>
		</form> <br> <hr>';


		while ($fetch = $run->fetch_assoc()) {

			$startdate = $fetch['PeriodStartDate'];
			$enddate = $fetch['PeriodEndDate'];
			$name = $fetch['Name'];
			$year = $fetch['Year'];
			$semester = $fetch['Semester'];

			echo '<h2>SELECTED PERIOD '.$year.'  - '.$semester.' Mode:'.$mode.'</h2>';

		}
	}




	public function manageReport($item) {
 
		$this->viewMenu($item);

		if($item == "school"){
			$this->schoolReport($this->core->subItem);
		} else if($item == "program"){
			$this->programReport($this->core->subItem);
		} else if($item == "intake"){
			$this->intakesReport($this->core->subItem);
		}

	}


	public function schoolReport($period){

		$this->viewMenu($item);

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		$level = $this->core->cleanGet['level'];

		if($period == '' || $mode == ''|| $level == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY METHOD AND LEVEL FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		echo '<br /> <h2>REPORTED '.$mode.' - Students by SCHOOLS</h2><br/>';


		$sql = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, `schools`.Description
			FROM `basic-information`, `student-study-link`, `study`, `course-electives`, `schools`
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `course-electives`.PeriodID = $period
			AND `schools`.ID = `study`.ParentID 
			AND `basic-information`.`StudyType` IN ('$mode')
			AND `basic-information`.`Status` IN ('Requesting', 'Approved')
			AND `study`.`StudyType` IN ($level)
			GROUP BY  `StudyType`, `StudyID`, `Sex`
			ORDER BY `schools`.Description, `study`.Name ASC";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table class="table-striped table-hover" style="padding: 0px;  width: 100%;">
			<tr class="heading" >
				<td>SCHOOL</td>
				<td></td>
				<td></td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>REPORTED</td>
			</tr>';


		$count = array();
		$delivery = array();
		$school = array();
		$sid = array();
		$male = array();
		$female = array();
		$reported = array();
		$i=0;

		while ($fetch = $run->fetch_array()){
			$i++;
			$school = $fetch[4];
			$delivery[$school] = $fetch[1];
			$schools[$school] = $fetch[3];
			$sex = $fetch[2];
			$sid[$school]  = $fetch[4];
			$name[$school]  = $fetch[5];

			if($sex == 'Female'){
				$female[$school]  = $fetch[0];
			} else {
				$male[$school]  = $fetch[0];
			}


			$count[$school] = $male[$school]+$female[$school];

		}
		$i = 0;

		foreach($sid as $school){

			if($name[$school] != $previous){
				if($i != 0){
				echo'<tr style="font-weight: bold;" >
					<td colspan="2">'.$sname.'</td>
					<td>'.$sexpected.'</td>
					<td>'.$smale.'</td>
					<td>'.$sfemale.'</td>
					<td>'.$scount.'</td>
				</tr>';

				}
				$i++;

				$smale = 0; $sfemale = 0; $scount = 0;
			} else {
			 	$sname = $name[$school];
			}

			$previous = $name[$school];
			$sfemale = $female[$school] + $sfemale;
			$smale = $male[$school] + $smale;
			$scount = $count[$school] + $scount;
			$tfemale = $female[$school] + $tfemale;
			$tmale = $male[$school] + $tmale;
			$tcount = $count[$school] + $tcount;
			
		}

		echo'<tr style="font-weight: bold;" >
			<td colspan="2">'.$sname.'</td>
			<td>'.$sexpected.'</td>
			<td>'.$smale.'</td>
			<td>'.$sfemale.'</td>
			<td>'.$scount.'</td>
		</tr>';

		echo'<tr class="heading" style="font-size: 11pt;">
			<td colspan="2">TOTAL REPORTED ALL SCHOOLS</td>
			<td>'.$texpected .'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$tcount .'</td>
			
		</tr>';
		echo'</table>';

		$tmale = 0; $tfemale = 0; $tcount = 0;
		
		
		echo'<br>';


		echo '<br /> <h2>APPROVED '.$mode.' - Students by SCHOOLS</h2><br/>';


		$sql = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, `schools`.Description
			FROM `basic-information`, `student-study-link`, `study`, `course-electives`, `schools`
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `course-electives`.PeriodID = $period
			AND `schools`.ID = `study`.ParentID 
			AND `course-electives`.Approved = 1
			AND `basic-information`.`StudyType` IN ('$mode')
			AND `basic-information`.`Status` IN ('Requesting', 'Approved')
			AND `study`.`StudyType` IN ($level)
			GROUP BY  `StudyType`, `StudyID`, `Sex`
			ORDER BY `schools`.Description, `study`.Name ASC";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table class="table-striped table-hover" style="padding: 0px;  width: 100%;">
			<tr class="heading" >
				<td>SCHOOL</td>
				<td></td>
				<td></td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>APPROVED</td>
			</tr>';


		$count = array();
		$delivery = array();
		$school = array();
		$sid = array();
		$male = array();
		$female = array();
		$reported = array();
		$i=0;

		while ($fetch = $run->fetch_array()){
			$i++;
			$school = $fetch[4];
			$delivery[$school] = $fetch[1];
			$schools[$school] = $fetch[3];
			$sex = $fetch[2];
			$sid[$school]  = $fetch[4];
			$name[$school]  = $fetch[5];

			if($sex == 'Female'){
				$female[$school]  = $fetch[0];
			} else {
				$male[$school]  = $fetch[0];
			}


			$count[$school] = $male[$school]+$female[$school];

		}
		$i = 0;

		foreach($sid as $school){

			if($name[$school] != $previous){
				if($i != 0){
				echo'<tr style="font-weight: bold;" >
					<td colspan="2">'.$sname.'</td>
					<td>'.$sexpected.'</td>
					<td>'.$smale.'</td>
					<td>'.$sfemale.'</td>
					<td>'.$scount.'</td>
				</tr>';

				}
				$i++;

				$smale = 0; $sfemale = 0; $scount = 0;
			} else {
			 	$sname = $name[$school];
			}

			$previous = $name[$school];
			$sfemale = $female[$school] + $sfemale;
			$smale = $male[$school] + $smale;
			$scount = $count[$school] + $scount;
			$tfemale = $female[$school] + $tfemale;
			$tmale = $male[$school] + $tmale;
			$tcount = $count[$school] + $tcount;
			
		}

		echo'<tr style="font-weight: bold;" >
			<td colspan="2">'.$sname.'</td>
			<td>'.$sexpected.'</td>
			<td>'.$smale.'</td>
			<td>'.$sfemale.'</td>
			<td>'.$scount.'</td>
		</tr>';

		echo'<tr class="heading" style="font-size: 11pt;">
			<td colspan="2">TOTAL APPROVED ALL SCHOOLS</td>
			<td>'.$texpected .'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$tcount .'</td>
			
		</tr>';
		echo'</table>';

		$tmale = 0; $tfemale = 0; $tcount = 0;
		
		
		echo'<br>';


	}


	public function  programReport($item){ 
		$this->viewMenu($item);

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		$level = $this->core->cleanGet['level'];


		if($period == '' || $mode == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY AND LEVEL METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		if($mode == "Both"){
			$mode = "Fulltime', 'Distance";
		}


		echo '<br /> <h2>'.$mode.' - Students by SCHOOLS/PROGRAMS</h2><br/>';


		$sql = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, `schools`.Description
			FROM `basic-information`, `student-study-link`, `study`, `course-electives`, `schools`
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `course-electives`.PeriodID = $period
			AND `course-electives`.Approved IN (1,0)
			AND `schools`.ID = `study`.ParentID 
			AND `basic-information`.`StudyType` IN ('$mode')
			AND `basic-information`.`Status` IN ('Requesting', 'Approved')
			AND `study`.`StudyType` IN ($level)
			GROUP BY  `StudyType`, `StudyID`, `Sex`
			ORDER BY `schools`.Description, `study`.Name ASC";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table class="table-striped table-hover" style="padding: 0px; width: 100%;">';


		$count = array();
		$delivery = array();
		$school = array();
		$sid = array();
		$male = array();
		$female = array();
		$reported = array();
		$i=0;

		while ($fetch = $run->fetch_array()){
			$i++;
			$school = $fetch[4];
			$delivery[$school] = $fetch[1];
			$schools[$school] = $fetch[3];
			$sex = $fetch[2];
			$sid[$school]  = $fetch[4];
			$name[$school]  = $fetch[5];

			if($sex == 'Female'){
				$female[$school]  = $fetch[0];
			} else {
				$male[$school]  = $fetch[0];
			}


			$count[$school] = $male[$school]+$female[$school];

		}
		$i = 0;

		foreach($sid as $school){

			$sqm = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, `schools`.Description, MAX(`student-study-link`.ID)
			FROM `basic-information`, `student-study-link`, `study`, `schools`
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `schools`.ID = `study`.ParentID 
			AND  `student-study-link`.StudyID = '$school'
			AND `basic-information`.`StudyType` IN ('$mode')
			AND `basic-information`.`Status` IN ('Requesting', 'Approved')
			AND `study`.`StudyType` IN ($level)
			GROUP BY  `StudyType`, `StudyID`, `Sex`
			ORDER BY `schools`.Description, `study`.Name ASC";

			//$expected[$school] = 0;

			$runx = $this->core->database->doSelectQuery($sqm); 
			while ($fetchx = $runx->fetch_array()){
				//$expected[$school]  = $fetchx[0];
				//$texpected = $texpected + $fetchx[0];
			}

			if($name[$school] != $previous){
				if($i != 0){
				echo'<tr style="font-weight: bold; border-top: 2px solid #000;">
					<td colspan="2">SCHOOL TOTAL</td>
					<td>'.$sexpected.'</td>
					<td>'.$smale.'</td>
					<td>'.$sfemale.'</td>
					<td>'.$scount.'</td>
				</tr>';
				echo'<tr>
					<td colspan="6">&nbsp;</td>
				</tr>';
				}
				$i++;


				echo'<tr>
					<td colspan="6" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$name[$school].'</td>
				</tr>';

			echo'<tr class="heading" >
				<td>DELIVERY</td>
				<td>Study</td>
				<td></td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>APPROVED</td>
			</tr>';
				$smale = 0; $sfemale = 0; $scount = 0;
				// $sexpected = 0;
			}

			echo'<tr>
				<td>'.$delivery[$school].'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/search?search=reported&q='.$school.'&period='.$period.'&mode='.$mode.'">'.$schools[$school].'</a></td>
				<td>'.$expected[$school].'</td>
				<td>'.$male[$school].'</td>
				<td>'.$female[$school].'</td>
				<td>'.$count[$school].'</td>
			</tr>';

			$sfemale = $female[$school] + $sfemale;
			$smale = $male[$school] + $smale;
			$scount = $count[$school] + $scount;
			//$sexpected= $expected[$school] + $sexpected;

			$tfemale = $female[$school] + $tfemale;
			$tmale = $male[$school] + $tmale;
			$tcount = $count[$school] + $tcount;
			$previous = $name[$school];
		}

		echo'<tr style="font-weight: bold; border-top: 2px solid #000;">
			<td colspan="2">SCHOOL TOTAL</td>
			<td>'.$sexpected.'</td>
			<td>'.$smale.'</td>
			<td>'.$sfemale.'</td>
			<td>'.$scount.'</td>

			</tr>';

				echo'<tr>
					<td colspan="6">&nbsp;</td>
				</tr>';

		echo'<tr class="heading" style="font-size: 11pt;">
			<td colspan="2">TOTAL ALL SCHOOLS</td>
			<td>'.$texpected .'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$tcount .'</td>
			
		</tr>';
		echo'</table>';

		$tmale = 0; $tfemale = 0; $tcount = 0;
		
		
		echo'<br>';


	}



	public function graduatedReport($item){
		$this->viewMenu($item);

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		$level = $this->core->cleanGet['level'];


		if($period == '' || $mode == '' || $level == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY AND LEVEL METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		echo '<br /> <h2>'.$mode.' - Graduates by SCHOOLS/PROGRAMS</h2><br/>';


		$sql = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, `schools`.Description,   LEFT(`basic-information`.ID,4) as year
			FROM `basic-information`, `student-study-link`, `study`, `schools`
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `schools`.ID = `study`.ParentID 
			AND `basic-information`.`StudyType` IN ('$mode')
			AND `basic-information`.`Status` IN ('Graduated')
			AND `study`.`StudyType` IN ($level)
			GROUP BY  `StudyType`, `StudyID`, LEFT(`basic-information`.ID,4),  `Sex`
			ORDER BY `schools`.Description, `study`.Name ASC";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table class="table-striped table-hover" style="padding: 0px; width: 100%;">';


		$count = array();
		$delivery = array();
		$school = array();
		$sid = array();
		$male = array();
		$female = array();
		$reported = array();
		$i=0;

		while ($fetch = $run->fetch_array()){
			$i++;
			$school = $fetch[4];
			$delivery[$school] = $fetch[1];
			$schools[$school] = $fetch[3];
			$sex = $fetch[2];
			$sid[$school]  = $fetch[4];
			$name[$school]  = $fetch[5];
			$year[$school]  = $fetch[6];

			if($sex == 'Female'){
				$female[$year[$school]][$school]  = $fetch[0];
			} else {
				$male[$year[$school]][$school]  = $fetch[0];
			}


			$count[$year[$school]][$school] = $male[$year[$school]][$school]+$female[$year[$school]][$school];

		}
		$i = 0;

		foreach($sid as $school){

			if($name[$school] != $previous){
				if($i != 0){
				echo'<tr style="font-weight: bold; border-top: 2px solid #000;">
					<td colspan="2">SCHOOL TOTAL</td>
					<td>'.$sexpected.'</td>
					<td>'.$smale.'</td>
					<td>'.$sfemale.'</td>
					<td>'.$scount.'</td>
				</tr>';
				echo'<tr>
					<td colspan="6">&nbsp;</td>
				</tr>';
				}
				$i++;


				echo'<tr>
					<td colspan="6" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$name[$school].'</td>
				</tr>';

			echo'<tr class="heading" >
				<td>DELIVERY</td>
				<td>Study</td>
				<td></td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>GRADUATED</td>
			</tr>';
				$smale = 0; $sfemale = 0; $scount = 0;
				// $sexpected = 0;
			}

			echo'<tr>
				<td>'.$year[$school].'</td>
<td>'.$delivery[$school].'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/search?search=reported&q='.$school.'&period='.$period.'">'.$schools[$school].'</a></td>
				<td>'.$expected[$year[$school]][$school].'</td>
				<td>'.$male[$year[$school]][$school].'</td>
				<td>'.$female[$year[$school]][$school].'</td>
				<td>'.$count[$year[$school]][$school].'</td>
			</tr>';

			$sfemale = $female[$school] + $sfemale;
			$smale = $male[$school] + $smale;
			$scount = $count[$school] + $scount;
			//$sexpected= $expected[$school] + $sexpected;

			$tfemale = $female[$school] + $tfemale;
			$tmale = $male[$school] + $tmale;
			$tcount = $count[$school] + $tcount;
			$previous = $name[$school];
		}

		echo'<tr style="font-weight: bold; border-top: 2px solid #000;">
			<td colspan="2">SCHOOL TOTAL</td>
			<td>'.$sexpected.'</td>
			<td>'.$smale.'</td>
			<td>'.$sfemale.'</td>
			<td>'.$scount.'</td>

			</tr>';

				echo'<tr>
					<td colspan="6">&nbsp;</td>
				</tr>';

		echo'<tr class="heading" style="font-size: 11pt;">
			<td colspan="2">TOTAL ALL SCHOOLS</td>
			<td>'.$texpected .'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$tcount .'</td>
			
		</tr>';
		echo'</table>';

		$tmale = 0; $tfemale = 0; $tcount = 0;
		
		
		echo'<br>';


	}

	public function campusReport($item){


		// $period = $this->core->cleanGet['period'];
		// $mode = $this->core->cleanGet['mode'];

		// if($period == '' || $mode == ''){
		// 	echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
		// 	return;
		// }

		echo '<p style=" text-align:center;">
		<a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">View Student Campus Statistics</a>
		<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">View students per campus</button>
		</p>';

		echo '<div class="row">
		<div class="col">
			<div class="collapse multi-collapse" id="multiCollapseExample1">
				<div class="card card-body">

					<h1 style="background-color:DodgerBlue; text-align:center;">
						Campus Student Statistics
					</h1>

					<table id="dtMaterialDesignExample" class="table" cellspacing="0" width="100%">
						<thead>
						<tr>
								<th class="th-sm">Campus Name
								</th>
								<th class="th-sm">Number of students
								</th>


							</tr>
						</thead>
						<tbody>';



		$sql = "SELECT sdo .ExamCentre, COUNT(sdo .StudentID) FROM `student-data-other` sdo group by sdo .ExamCentre";

		$run = $this->core->database->doSelectQuery($sql);

		// echo '<br><h2>Campus Statistics</h2>';
		// echo'<table style="width: 100%;">
		// <tr class="heading">
		// 	<td>Campus</td>
		// 	<td>Count</td>

		// </tr>';

		while ($fetch = $run->fetch_array()){
			$campus = $fetch[0];
			$count = $fetch[1];

			echo'<tr>
				<td>'.$campus.'</td>
				<td>'.$count.'</td>
			</tr>';

		}
		echo'</table>';

		echo '</div>';
		echo '</div>';
		echo '</div>';

		//...........................................view students.................................

		echo '<div class="col">
        <div class="collapse multi-collapse" id="multiCollapseExample2">
            <div class="card card-body">

                <h1 style="background-color:red; text-align:center;">
                    Students per campus
                </h1>
                <table id="rejectedtable" class="table" cellspacing="0" width="100%">
                    <thead>
						<tr>
								<th class="th-sm">Student Number
								</th>
								<th class="th-sm">First Name
								</th>
								<th class="th-sm">Last Name
								</th>
								<th class="th-sm">Government ID
								</th>
								<th class="th-sm">Program
								</th>
								<th class="th-sm">Campus
								</th>
	
	
							</tr>
						</thead>
						<tbody>';
	
	
	
			$sql = "SELECT bi.ID, CONCAT(bi.FirstName, bi.MiddleName) as FirstName, bi.Surname, bi.GovernmentID, (SELECT s2 .name FROM study s2 where s2.ID = ssl2.StudyID ) as program, sdo.ExamCentre FROM `basic-information` bi, `student-data-other` sdo, `student-study-link` ssl2 where bi .ID = sdo.StudentID and sdo.StudentID = ssl2.StudentID ";
	
			$run = $this->core->database->doSelectQuery($sql);
	
			// echo '<br><h2>Campus Statistics</h2>';
			// echo'<table style="width: 100%;">
			// <tr class="heading">
			// 	<td>Campus</td>
			// 	<td>Count</td>
	
			// </tr>';
	
			while ($fetch = $run->fetch_array()){
				$SudentId = $fetch[0];
				$firtname = $fetch[1];
				$lastname = $fetch[2];
				$govID = $fetch[3];
				$program = $fetch[4];
				$campus = $fetch[5];
	
				echo'<tr>
					<td>'.$SudentId.'</td>
					<td>'.$firtname.'</td>
					<td>'.$lastname.'</td>
					<td>'.$govID.'</td>
					<td>'.$program.'</td>
					<td>'.$campus.'</td>
				</tr>';
	
			}
			echo'</table>';

			echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>';

		echo '<script>
		$(document).ready(function() {
			$("#rejectedtable").DataTable();
		});
		</script>';

		echo ' <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>';


	}


	public function financialReport($item){


		// $period = $this->core->cleanGet['period'];
		// $mode = $this->core->cleanGet['mode'];

		// if($period == '' || $mode == ''){
		// 	echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
		// 	return;
		// }

		echo '<p style=" text-align:center;">
		<a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">View by academic year</a>
		<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">By academic year and semester</button>
		<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#feecodediv" aria-expanded="false" aria-controls="feecodediv">By Fee code</button>
		</p>';

		echo '<div class="row">
		<div style =" padding: 20px;" class="col">
			<div class="collapse multi-collapse" id="multiCollapseExample1">
				<div class="card card-body">

					<h1 style="background-color:DodgerBlue; text-align:center;">
						Financial Statistics
					</h1>

					<table id="dtMaterialDesignExample" class="table" cellspacing="0" width="100%">
						<thead>
						<tr>
								<th class="th-sm">Academic Year
								</th>
								<th class="th-sm">Total Amount (K)
								</th>


							</tr>
						</thead>
						<tbody>';



		$sql = "SELECT ai.academicYear, SUM(ai.amount) as TotalAmount FROM acct_invoice ai group by ai.academicYear";

		$run = $this->core->database->doSelectQuery($sql);

		// echo '<br><h2>Campus Statistics</h2>';
		// echo'<table style="width: 100%;">
		// <tr class="heading">
		// 	<td>Campus</td>
		// 	<td>Count</td>

		// </tr>';

		while ($fetch = $run->fetch_array()){
			$academicYear = $fetch[0];
			$amount = $fetch[1];

			echo'<tr>
				<td>'.$academicYear.'</td>
				<td>'.round($amount,3).'</td>
			</tr>';

		}
		echo'</table>';

		echo '</div>';
		echo '</div>';
		echo '</div>';

		//...........................................view students.................................

		echo '<div class="col">
        <div style =" padding: 20px;" class="collapse multi-collapse" id="multiCollapseExample2">
            <div class="card card-body">

                <h1 style="background-color:red; text-align:center;">
				Financial Statistics
                </h1>
                <table id="rejectedtable" class="table" cellspacing="0" width="100%">
                    <thead>
						<tr>
								<th class="th-sm">Academic Year
								</th>
								<th class="th-sm">Semester
								</th>
								<th class="th-sm">Amount
								</th>
	
	
							</tr>
						</thead>
						<tbody>';
	
	
	
			$sql = "SELECT ai.academicYear, ai.semester ,SUM(ai.amount) as TotalAmount FROM acct_invoice ai group by ai.academicYear, ai.semester ";
	
			$run = $this->core->database->doSelectQuery($sql);
	
			// echo '<br><h2>Campus Statistics</h2>';
			// echo'<table style="width: 100%;">
			// <tr class="heading">
			// 	<td>Campus</td>
			// 	<td>Count</td>
	
			// </tr>';
	
			while ($fetch = $run->fetch_array()){
				$academicYear = $fetch[0];
				$semester = $fetch[1];
				$amount = $fetch[2];

	
				echo'<tr>
					<td>'.$academicYear.'</td>
					<td>'.$semester.'</td>
					<td>'.round($amount,3).'</td>

				</tr>';
	
			}
			echo'</table>';

			echo '</div>';
		echo '</div>';
		echo '</div>';


				//...........................................view by feecode.................................

		echo '<div class="col">
        <div style =" padding: 20px;" class="collapse multi-collapse" id="feecodediv">
            <div class="card card-body">

                <h1 style="background-color:red; text-align:center;">
				Financial Statistics by FeeCode
                </h1>
                <table id="feecodetable" class="table" cellspacing="0" width="100%">
                    <thead>
						<tr>
								<th class="th-sm">Fee Code 
								</th>
								<th class="th-sm">Semester
								</th>
								<th class="th-sm">Academic year
								</th>
								<th class="th-sm">Amount
								</th>
	
	
							</tr>
						</thead>
						<tbody>';
	
	
	
			$sql = "SELECT ai.feecode , ai.semester, ai.academicYear,SUM(ai.amount) as TotalAmount FROM acct_invoice ai group by ai.feecode , ai.semester ";
	
			$run = $this->core->database->doSelectQuery($sql);
	
			// echo '<br><h2>Campus Statistics</h2>';
			// echo'<table style="width: 100%;">
			// <tr class="heading">
			// 	<td>Campus</td>
			// 	<td>Count</td>
	
			// </tr>';
	
			while ($fetch = $run->fetch_array()){
				$feecode = $fetch[0];
				$semester = $fetch[1];
				$academicYear = $fetch[2];
				$amount = $fetch[3];

	
				echo'<tr>
					<td>'.$feecode.'</td>
					<td>'.$semester.'</td>
					<td>'.$academicYear.'</td>
					<td>K '.round($amount,2).'</td>

				</tr>';
	
			}
			echo'</table>';

			echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>';

		echo '<script>
		$(document).ready(function() {
			$("#feecodetable").DataTable();
		});
		</script>';

		echo ' <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>';


	}



	public function intakeReport($item){

		$this->viewMenu($period);

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];

		if($period == '' || $mode == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		setlocale(LC_MONETARY, 'en_US.UTF-8');
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT LEFT(ID,4), COUNT(LEFT(ID,4)), StudyType, Sex 
			FROM `basic-information` 
			WHERE LEFT(ID,4) > 2005 AND  LEFT(ID,4) < 2020 
			GROUP BY LEFT(ID,4), StudyType, Sex ";

		$run = $this->core->database->doSelectQuery($sql);


		while ($fetch = $run->fetch_array()){
			$year = $fetch[0];
			$count = $fetch[1];
			$delivery = $fetch[2];
			$sex = $fetch[3];

			 if($sex == 'Female'){
				$data[$year][$delivery]['Female'] = $count;
			} else {
				$data[$year][$delivery]['Male'] = $count;
			}
		}



		// GET TOTALS FOR FULLTIME
		echo '<br><h2>Full-Time Students per Intake - UNIVERSITY LEVEL</h2>';
		$mode = 'Fulltime';	

		echo'<table style="width: 100%;">
			<tr class="heading">
				<td>YEAR</td>
				<td>DELIVERY METHOD</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
				<td>GRADUATED</td>
				<td>ACTIVE </td>
				<td>REGISTERED </td>
				<td>TOTAL OWED </td>
			</tr>';

		foreach($data as $year => $delivery){
			$male = $delivery[$mode]['Male'];
			$female = $delivery[$mode]['Female'];
			$total = $male + $female;

			$subsql = "SELECT COUNT(LEFT(ID,4)) as Current, 'NIL'
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')
				AND LEFT(`basic-information`.ID,4) < 2020 
				UNION
				SELECT COUNT(LEFT(ID,4)) as Graduated, 'NIL'
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Graduated')
				UNION
				SELECT COUNT(DISTINCT `basic-information`.ID) as Registered, 'NIL'
				FROM `basic-information`, `course-electives` 
				WHERE `basic-information`.`ID` LIKE  '$year%' 
				AND `course-electives`.PeriodID = $period
				AND `basic-information`.ID = `course-electives`.StudentID
				AND `course-electives`.Approved = 1
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')";
			$subrun = $this->core->database->doSelectQuery($subsql); 


			$years = $year . "00000";
			$yearp = $year + 1 . "00000";
			$bsql = "SELECT SUM(Amount) as TOTAL
				FROM `basic-information`, `balances` 
				WHERE `Amount` > 0 
				AND `basic-information`.ID = `balances`.StudentID
				AND `Status` IN ('Graduated', 'Enrolled', 'New', 'Requesting', 'Approved', 'Deregistered', 'Locked')
				AND `StudyType` = '$mode' 
				AND `StudentID` BETWEEN $years AND $yearp";
		
		
			$brun = $this->core->database->doSelectQuery($bsql); 
			$balance = 0; 
			$sbalance = 0;

			while ($subfetch = $brun->fetch_array()){
				$balance = $subfetch[0];
				$sbalance = money_format('%!.0n', $balance);
			}
			
			$o=0;

			$registered = 0; 
			$current = 0;
			$graduated = 0;
			while ($subfetch = $subrun->fetch_array()){
				if($o == 0){
					$current = $subfetch[0];
				}else if($o == 1){
					$graduated = $subfetch[0];
				} else if($o == 2){
					$registered = $subfetch[0];
				}

				$o++;
			}
			if(empty($registered)){
				$registered = $current;
			}

			echo'<tr>
				<td><b>'.$year.'</b></td>
				<td><b>'.$mode.'</b></td>
				<td>'.$male.'</td>
				<td>'.$female.'</td>
				<td>'.$total.'</td>
				<td>'.$graduated.'</td>
				<td>'.$current.'</td>
				<td>'.$registered.'</td>
				<td>'.$sbalance.'</td>
			</tr>';

			
			$tfemale = $tfemale + $female;
			$tmale = $tmale + $male;
			$ttotal = $ttotal + $total;
			$tcur = $tcur + $current;
			$treg = $treg + $registered;
			$tgraduated = $tgraduated + $graduated;
			$tbalance = $tbalance + $balance;
			$balance = 0; $sbalance = 0;
			$total = 0;
		}


		$tbalance = money_format('%!.0n', $tbalance);

		echo'<tr class="heading">
			<td>TOTAL</td>
			<td>'.$mode.'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$ttotal.'</td>
			<td>'.$tgraduated .'</td>
			<td>'.$tcur.'</td>
			<td>'.$treg.'</td>
			<td>'.$tbalance.'</td>
		</tr>';

		echo'</table>';


			$tfemale = 0;
			$tmale = 0;
			$ttotal = 0;
			$treg = 0;
			$tcur = 0;
			$tgraduated = 0;
			$tbalance = 0;


		// GET TOTALS FOR DE
		echo '<hr><br /> <h2>Distance Students per Intake - UNIVERSITY LEVEL</h2>';
		$mode = 'Distance';

		echo'<table style="width: 100%;">
			<tr class="heading">
				<td>YEAR</td>
				<td>DELIVERY METHOD</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
				<td>GRADUATED</td>
				<td>ACTIVE </td>
				<td>REGISTERED </td>
				<td>TOTAL OWED </td>
			</tr>';

		foreach($data as $year => $delivery){
			$male = $delivery[$mode]['Male'];
			$female = $delivery[$mode]['Female'];
			$total = $male + $female;

			$subsql = "SELECT COUNT(LEFT(ID,4)) as Current 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Sex` IN ('Male', 'Female')
				AND `Status` IN ('Requesting', 'Approved')
				UNION
				SELECT COUNT(LEFT(ID,4)) as Graduated 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Graduated')
				UNION
				SELECT COUNT(DISTINCT `basic-information`.ID) as Registered 
				FROM `basic-information`, `course-electives` 
				WHERE `basic-information`.`ID` LIKE  '$year%' 
				AND `basic-information`.ID = `course-electives`.StudentID
				AND `course-electives`.Approved = 1
				AND `StudyType` = '$mode' 
				AND `course-electives`.PeriodID = $period
				AND `Sex` IN ('Male', 'Female')
				AND `Status` IN ('Requesting', 'Approved')";

			$subrun = $this->core->database->doSelectQuery($subsql); 
			$o=0;

			$years = $year . "00000";
			$yearp = $year + 1 . "00000";
			$bsql = "SELECT SUM(Amount) as TOTAL
				FROM `basic-information`, `balances` 
				WHERE `Amount` > 0 
				AND `basic-information`.ID = `balances`.StudentID
				AND `Status` IN ('Graduated', 'Enrolled', 'New', 'Requesting', 'Approved', 'Deregistered', 'Locked')
				AND `StudyType` = '$mode' 
				AND `StudentID` BETWEEN $years AND $yearp";
		
			$brun = $this->core->database->doSelectQuery($bsql); 
			$balance = 0; 
			$sbalance = 0;

			while ($subfetch = $brun->fetch_array()){
				$balance = $subfetch[0];
				$sbalance = money_format('%!.0n', $balance);
			}


			$registered = 0; 
			$current = 0;
			$graduated = 0;
			while ($subfetch = $subrun->fetch_array()){
				if($o == 0){
					$current = $subfetch[0];
				}else if($o == 1){
					$graduated = $subfetch[0];
				} else if($o == 2){
					$registered = $subfetch[0];
				}
				$o++;
			}

			echo'<tr>
				<td><b>'.$year.'</b></td>
				<td><b>'.$mode.'</b></td>
				<td>'.$male.'</td>
				<td>'.$female.'</td>
				<td>'.$total.'</td>
				<td>'.$graduated.'</td>
				<td>'.$current.'</td>
				<td>'.$registered.'</td>
				<td>'.$sbalance.'</td>
			</tr>';


			$tfemale = $tfemale + $female;
			$tmale = $tmale + $male;
			$ttotal = $ttotal + $total;
			$tcur = $tcur + $current;
			$treg = $treg + $registered;
			$tgraduated = $tgraduated + $graduated;
			$tbalance = $tbalance + $balance;
			$balance = 0; $sbalance = 0;

			$total = 0;
		}

		$tbalance = money_format('%!.0n', $tbalance);

		echo'<tr class="heading">
			<td>TOTAL</td>
			<td>'.$mode.'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$ttotal.'</td>
			<td>'.$tgraduated .'</td>
			<td>'.$tcur.'</td>
			<td>'.$treg.'</td>
			<td>'.$tbalance.'</td>
		</tr>';


		echo'</table>';
		echo'<hr>'; 


	}


	public function yearReport($period){
		$this->viewMenu($period);

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		$level = $this->core->cleanGet['level'];


		if($period == '' || $mode == ''|| $level == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND DELIVERY AND LEVEL METHOD FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		echo '<br /> <h2>APPROVED Students by PROGRAM by Year</h2>';
		
		
		///Students in study-per-year
		echo'<table class="table-striped table-hover" style="padding: 0px; width: 100%;">
			<tr class="heading" >
				<td>PROGRAM NAME</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
			</tr>';

		
		
			$sqlm="
			SELECT *,COUNT(ProgTB.ProgYear) as 'TOT' 
			FROM (SELECT `basic-information`.ID,`basic-information`.StudyType,`study`.Name, `study`.ID as 'STID',`basic-information`.Sex
			,(SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID AND `course-electives`.Approved = 1)) as 'Year'
			,CONCAT(`study`.Name,' - <b>Year: ',
			(SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID AND `course-electives`.Approved = 1) AND StudyID=`student-study-link`.StudyID)
			 , '</b>') AS 'ProgYear',
			`course-electives`.PeriodID
			FROM `basic-information`, `student-study-link`, `study`, `course-electives`
			 WHERE `study`.ID = `student-study-link`.StudyID 
			 AND `basic-information`.ID = `student-study-link`.StudentID 
			 AND `basic-information`.ID = `course-electives`.StudentID
			 AND `course-electives`.PeriodID =$period 
			 AND `course-electives`.Approved = 1
			 AND `basic-information`.StudyType = '$mode'
			 AND `study`.`StudyType` IN ($level)
			GROUP BY `basic-information`.ID,`study`.ID
			ORDER BY study.`Name`,Year) as ProgTB
			GROUP BY ProgTB.ProgYear
			";
			
			$sqlm="SELECT *,COUNT(ProgTB.ProgYear)as 'TOT'
			FROM (SELECT `basic-information`.ID,`basic-information`.StudyType,`study`.Name, `study`.ID as 'STID',`basic-information`.Sex
			,(SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID)) as 'Year'
			,CONCAT(`study`.Name,' - <b>Year: ',
			(SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID) AND StudyID=`student-study-link`.StudyID)
			 , '</b>') AS 'ProgYear',
			`course-electives`.PeriodID,`study`.ParentID,(SELECT Description FROM schools WHERE ID= `study`.ParentID)as school
			FROM `basic-information`, `student-study-link`, `study`, `course-electives`
			WHERE `study`.ID = `student-study-link`.StudyID 
						AND `basic-information`.ID = `student-study-link`.StudentID 
						AND `basic-information`.ID = `course-electives`.StudentID
						AND `course-electives`.PeriodID = $period  
						AND `course-electives`.Approved = 1
						AND `basic-information`.StudyType = '$mode'
						AND `study`.`StudyType` IN ($level)
			GROUP BY `basic-information`.ID,`study`.ID
			ORDER BY `study`.ParentID,study.`Name`,Year) as ProgTB";
			
			//WHERE ProgTB.ProgYear='Bachelor of Agricultural Business Management - <b>Year: 1</b>'
			//GROUP BY ProgTB.ProgYear,ProgTB.sex
			//ORDER BY ProgTB.ParentID,ProgTB.ProgYear";
			
			//echo $sqlm;
			
			$runm = $this->core->database->doSelectQuery($sqlm); 
			$tcount=0;
			
			$data = array();

			while ($fetchm = $runm->fetch_assoc()){
				
				$count = $fetchm['TOT'];		
				if($count == 0){ continue; }

				$delivery = $fetchm['StudyType'];
				$name = $fetchm['ProgYear'];
				$sex = $fetchm['Sex']; 
				$school = $fetchm['school'];
				$tcount+= $count;
				
				$data[$name][$sex] = $count;
				$schoollist[$name] = $school;
				
			}

			
			foreach($data as $ls => $value){
					
				$name = $ls;
				$male = $value['Male'];
				$female = $value['Female'];
				$total = $male + $female;
				$school = $schoollist[$name];
				
				if($school != $previous){
					
					if($start == TRUE){
						echo'<tr class="heading">
						<td>SCHOOL TOTAL</td>
						<td>'.$tmale.'</td>
						<td>'.$tfemale.'</td>
						<td><b>'.$ttotal .'</b></td>
						</tr>';
					}
					
					$start = TRUE;
					
					echo'<tr>
						<td colspan="4" style="font-size: 11pt; font-weight: bold; border: 1px solid #333;">'.$school.'</td>
					</tr>';
					
					$tmale = 0;
					$tfemale = 0;
					$ttotal = 0;
					
					
				}

				
				echo"<tr>
					<td>".$name."</td>
					<td>".$male."</td>
					<td>".$female."</td>
					<td><b>".$total."</b></td>
				</tr>"; 
				
				$tmale = $tmale + $male;
				$tfemale = $tfemale + $female;
				$ttotal = $ttotal + $total;
				
				$fmale = $fmale + $male;
				$ffemale = $ffemale + $female;
				$ftotal = $ftotal + $total;
				
				$previous = $school;
				$male = 0;
				$female = 0;
				$total = 0;
			
			}
						
			echo'<tr class="heading">
			<td>SCHOOL TOTAL</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td><b>'.$ttotal .'</b></td>
			</tr>
			
			<tr class="heading">
			<td>TOTALS</td>
			<td>'.$fmale.'</td>
			<td>'.$ffemale.'</td>
			<td><b>'.$ftotal .'</b></td>
			</tr>
			</table>';
	}
	
	public function incomeReport(){
		//$this->viewMenu($period);
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$periods = $select->showTrailYear();

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];
		
		echo'<form id="narrow" name="narrow" method="get" action="">
			<div class="toolbar">
					
				<div class="toolbaritem">Filter to show statement from: 
					<select name="period" class="submit" style="width: 180px; margin-top: -17px;">
					<option value=""> -- SELECT A YEAR -- </option>
					'. $periods .'
					</select>


					<select name="mode" id="mode" class="submit" style="width: 130px; margin-top: -17px;" >
						<option value=""> -- TYPE -- </option>
						<option value="PAYMENT">PAYMENT</option>
						<option value="BILLING">BILLING</option>
					</select>
					
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/>
				</div>
			</div>
		</form><hr>';
		if($period == '' || $mode == ''){
			echo'<div class="warningpopup">SELECT THE PERIOD AND TYPE FOR WHICH YOU WISH TO GENERATE A REPORT</div>';
			return;
		}

		echo '<br /> <h2>Statement by School by Month For: <b>'.$period.' '.$mode.'</b></h2>';
		
		
			$sqlRow="SELECT  MONTH(`TDate`) AS MT,DATE_FORMAT((`TDate`) ,'%M') AS MF
				FROM `paymets-trail`
				WHERE `Type` LIKE '$mode' 
				AND YEAR(`TDate`) = '$period'
				GROUP BY YEAR(`TDate`), MONTH(`TDate`)";
			
		
			$runRow = $this->core->database->doSelectQuery($sqlRow); 
			
			echo'<table class="table-striped table-hover" style="padding: 0px; width: 100%;">';
			
			$sqlCol="SELECT sch.`Name`
			FROM `paymets-trail` py 
			LEFT JOIN `student-study-link` sl ON py.StudentID=sl.StudentID
			LEFT JOIN `study` s ON s.ID=sl.StudyID
			LEFT JOIN `schools` sch ON sch.ID=s.ParentID
			WHERE `Type` LIKE '$mode' 
			AND YEAR(`TDate`) = '$period'
			AND MONTH(`TDate`) = '1'
			GROUP BY YEAR(`TDate`), MONTH(`TDate`),s.ParentID ";
			
			$runCol = $this->core->database->doSelectQuery($sqlCol); 
			echo'<tr class="heading" >';
			echo '<td>Month/School</td>';
			while ($fetchCol = $runCol->fetch_assoc()){
					
					$name = $fetchCol['Name'];
					echo '<td><b>'.$name.'</b></td>';	
					
			}
			echo '</tr>';
			
			$SANR=0;
			$SBS=0;
			$SSS=0;
			$SoMHS=0;
			$SSET=0;
			$SOE=0;
			
			while ($fetchRow = $runRow->fetch_assoc()){
				
				$rows = $fetchRow['MT'];		
				$monthname = $fetchRow['MF'];		
				
				$sqlData="SELECT sch.`Name`,YEAR(`TDate`) as `YT`, MONTH(`TDate`), ROUND(SUM(`Amount`)) AS AMOUNT
				FROM `paymets-trail` py 
				LEFT JOIN `student-study-link` sl ON py.StudentID=sl.StudentID
				LEFT JOIN `study` s ON s.ID=sl.StudyID
				LEFT JOIN `schools` sch ON sch.ID=s.ParentID
				WHERE `Type` LIKE '$mode' 
				AND YEAR(`TDate`) = '$period'
				AND MONTH(`TDate`) = '$rows'
				GROUP BY YEAR(`TDate`), MONTH(`TDate`),s.ParentID ";
				$runData = $this->core->database->doSelectQuery($sqlData); 
				
				echo'<tr>';
					echo '<td>'.$monthname.'</td>';	
					
					while ($fetchData = $runData->fetch_assoc()){
						//$monthtotal = 0;
						$school = $fetchData['Name'];
						$year = $fetchData['YT'];
						$amount = $fetchData['AMOUNT'];
						
						echo '<td>ZMW '.number_format($amount).'</td>';
						
						if($school == 'SANR'){$SANR +=$amount;}
						if($school == 'SBS'){$SBS +=$amount;}
						if($school == 'SSS'){$SSS +=$amount;}
						if($school == 'SoMHS'){$SoMHS +=$amount;}
						if($school == 'SOE'){$SOE +=$amount;}
						if($school == 'SSET'){$SSET +=$amount;}
						
					}
					 
					
				echo '</tr>';
			}
			
			
			echo'<tr class="heading">
				<td>SCHOOL TOTAL</td>
				<td>ZMW '.number_format($SANR).'</td>
				<td>ZMW '.number_format($SBS).'</td>
				<td>ZMW '.number_format($SSS).'</td>
				<td>ZMW '.number_format($SoMHS).'</td>
				<td>ZMW '.number_format($SSET).'</td>
				<td>ZMW '.number_format($SOE).'</td>
				</tr>';
			
			echo '</table>';
			
			echo '<br /> <h2>Statement by Month For: <b>'.$period.' '.$mode.'</b></h2>';
		
		
			$sqlRow="SELECT  MONTH(`TDate`) AS MT,DATE_FORMAT((`TDate`) ,'%M') AS MF, ROUND(SUM(`Amount`),2) AS AMOUNT
				FROM `paymets-trail`
				WHERE `Type` LIKE '$mode' 
				AND YEAR(`TDate`) = '$period'
				GROUP BY YEAR(`TDate`), MONTH(`TDate`)";
			
		
			$runRow = $this->core->database->doSelectQuery($sqlRow); 
			
			echo'<table class="table-striped table-hover" style="padding: 0px; width: 100%;">';
			
			echo'<tr class="heading" >';
			echo '<td>Month</td>';
			echo '<td>Amount</td>';
			echo '</tr>';
			
			$total=0;
			
			while ($fetchRow = $runRow->fetch_assoc()){
				$month = $fetchRow['MF'];
				$amount = $fetchRow['AMOUNT'];
				
				echo '</tr>';
			
					echo '<td>'.$month.'</td>';
					echo '<td>ZMW '.number_format($amount).'</td>';
					
					$total +=$amount;
			
				echo '</tr>';
			}
			
			
			echo'<tr class="heading">
				<td>TOTAL</td>
				<td>ZMW '.number_format($total).'</td>
				</tr>';
			
			echo '</table>';
		
	}

	function overviewReport($item) {
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT COUNT( DISTINCT  `grades`.StudentNo) FROM `grades`, `basic-information`
			WHERE `grades`.AcademicYear LIKE '$year' AND `basic-information`.ID = `grades`.StudentNo AND `basic-information`.StudyType = '$time'";


		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$count = $fetch[0];

			$pages = $count / 100;
			$count = 1;

			while($count < $pages){
				$show = $count * 100;
				$old  = $show-100;
				echo 'Print results from '.$old.' to '.$show.' - <a href="' . $this->core->conf['conf']['path'] . '/report/batch/?uid='.$year.'&start='.$old.'&end='.$show.'&time='.$time.'">CLICK HERE</a><br>';
				$count++;
			}
		}
	}

	public function paymentsReport($item) {
		$year = $item;
		$month = $this->core->subitem;
		
		if($this->core->role != 105 && $this->core->role != 107 && $this->core->role != 1000){
			echo '<div>NO RIGHTS</div>';
			return;
		}

		$sql = "SELECT  `basic-information`.ID, `GovernmentID`, `balances`.`AccountCode`, `FirstName`,`MiddleName`,`Surname`, `student-data-other`.`ExamCentre`, `schools`.`Name`, `programmes`.`ProgramName`, SUM(`courses`.`CourseCredit`) as Credits, `basic-information`.`StudyType`,`basic-information`.`Status`, `basic-information`.`MobilePhone`, `ChargeType`
				FROM `basic-information`
				LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.`StudentID`
				LEFT JOIN `study` ON `study`.`ID` = `student-study-link`.`StudyID`
				LEFT JOIN `schools` ON `study`.ParentID = `schools`.`ID`
				LEFT JOIN `student-program-link` ON `student-program-link`.`StudentID` = `basic-information`.ID
				LEFT JOIN `course-electives` ON  `course-electives`.`StudentID` = `basic-information`.ID
				LEFT JOIN `programmes` ON `student-program-link`.`Major` = `programmes`.`ID`
				LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.`ID`
				LEFT JOIN `balances` ON  `balances`.`StudentID` = `basic-information`.ID
				LEFT JOIN `student-data-other` ON  `student-data-other`.`StudentID` = `basic-information`.ID
				LEFT JOIN `fee-package-charge-link` ON `basic-information`.ID = `fee-package-charge-link`.StudentID
				WHERE `basic-information`.`Status` IN ('New', 'Requesting')
				AND `course-electives`.Approved = '1'
				GROUP BY `course-electives`.`StudentID`, `basic-information`.ID  
				ORDER BY `Credits`  DESC"; 

		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=edurole-export.csv");
		header("Pragma: no-cache");
		header("Expires: 0");




		while ($row = $run->fetch_row()) {
			$results = TRUE;

			$uid = $row[0];
			$nrc = $row[1];
			$acccode = $row[2];

			$firstname = $row[3];
			$middlename = $row[4];
			$surname = $row[5];

			$campus = $row[6];
			$school = $row[7];
			$major = $row[8];
			$credits = $row[9];
			$mode = $row[10];
			$status = $row[11];
			$phone = $row[12];

			$charge = $row[13];
			
			echo	$count.','.
				$uid.','.
				$nrc.','.
				$acccode.','.
				$firstname.' '.$middlename.' ' . $surname . ','.
				$campus.','.
				$school.','.
				$major.','.
				$credits.','.
				$mode.','.
				$status.','.
				$phone.','.
				$charge.',
';

			

			$count++;
			$results = TRUE;
		}
	}



	function batchReport($item) {
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		if(empty($start)){
			$start = 0;
		}
		if(empty($end)){
			$end = 1000;
		}

		$major = $_GET['major'];
		$minor = $_GET['minor'];

		$sql = "SELECT DISTINCT `basic-information`.ID 
			FROM `basic-information`, `student-program-link`, `programmes`, `grades`
			WHERE 
			`student-program-link`.Major = '$major'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			AND
			`student-program-link`.Minor = '$minor'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			OR
			`student-program-link`.Major = '$minor'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			AND
			`student-program-link`.Minor = '$major'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			GROUP BY `basic-information`.ID 
			ORDER BY  `basic-information`.ID DESC
			LIMIT $start, $end";


		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$studentid = $fetch[0];
			$this->resultsReport($studentid, $year);
			$first = FALSE;
			$i++;
			$x++;
		}

		echo'<script type="text/javascript">
			window.print();
		</script>';

	}
	
	function resultsReport($item, $year) {

		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}

		if($item == ""){
			$studentID = $_GET['uid'];
		} else {
			$studentID = $item;
		}

		$studentNo = $studentID;
		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, `programmes`.ProgramName, YearOfStudy FROM `programmes`, `student-program-link`, `basic-information`, `student-data-other`
			WHERE `basic-information`.ID = `student-program-link`.`StudentID` AND `programmes`.ID = `student-program-link`.`Major` AND `basic-information`.`ID` = '$studentID' AND `student-data-other`.StudentID = `basic-information`.ID
			OR   `basic-information`.ID = `student-program-link`.`StudentID` AND `programmes`.ID = `student-program-link`.`Minor` AND `basic-information`.`ID` = '$studentID' AND `student-data-other`.StudentID = `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);
 
		$started = FALSE;
		$counter = 1;
		$program = 1;

		while ($fetch = $run->fetch_array()){
			$started = TRUE;

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[5];
			$gender=$fetch[4];
			$year=$fetch[6];

			$studentname = $firstname . " " . $middlename . " " . $surname;

			$school=$fetch[7];

			if($program == 1){
				$program = $fetch[5];
			}else{
				$programtwo = $fetch[5];
			}

			$session=$fetch[9];
			$remark=$fetch[8];
		}

		if($program == $programtwo || empty($programtwo)){
			$programs = "$program";
		} else {
			$programs = "$program /<br> $programtwo";
		}

		echo "<div style=\" float: left; clear: left; padding: 2px; page-break-inside: avoid; font-size: 14px;\">
			<div style=\"clear: left; float: left; width: 200px; padding-right: 15px; padding-top: 20px; \"><b>$studentID</b>
			<div style=\"float: left; width: 250px; padding-right: 15px;\"><b>$studentname</b></div>
			<div style=\"float: left; width: 250px; padding-right: 15px;\">Program: <b>$programs</b></div>
			<div style=\"float: left; width: 250px; padding-right: 15px;\">Gender: <b>$gender</b></div></div>
			";
	
		$overallremark= $this->academicyear($studentNo, $year);

		//$overallremark= $this->detail($studentNo, 2016, TRUE, $year);

		echo'</div></div>';
	}

	private function academicyear($studentNo, $year) {

		$sql = "SELECT distinct academicyear FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";

		$run = $this->core->database->doSelectQuery($sql);
		$countyear = 1;

		echo '<div style="float: left; padding: 20px; border: 5px solid #f1f1f1; font-size: 12px;">';
		while ($fetch = $run->fetch_array()){

			$acyr = $fetch[0];

			if($countyear == 1){
				$set = FALSE;
			} else {
 				$set = TRUE;
			}
	
			echo '<div style="float: left; width: 110px; padding-right: 15px;">';

			echo '<div style="width: 100px float:left; "><b>YEAR '.$acyr.'</b></div>';

			$overallremark = $this->detail($studentNo, $acyr, $set);

			echo '</div>';

			$remark = $overallremark[0];
			$repeat = $overallremark[1];
			$countyear++;

		}
		echo '</div>';echo '</div>';
	}

	private function detail($studentNo, $acyr, $set, $year) {

		$sql = "SELECT DISTINCT
				p1.CourseNo,
				p1.Grade
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			AND 	p1.Grade != ''
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
		$out = FALSE;
		$qualcount = 0;
		$suppposscount = 0;
		$count = 0;
		$passcount = 0;
		$hiderepeat = FALSE;
		
		while ($row = $run->fetch_array()){
			$count++;
			$course = $row[0];
			$grade = $row[1];

			if (substr($course, -1) == '1'){
				$coursetype = 0.5;
			}else{
				$coursetype = 1;
			}

	
			$output .= '<div style="width: 100px float:left; font-size: 10px; ">'.$course.': <b>'.$grade.'</b></div>';

			if ($grade == "IN" or $grade == "D" or $grade=="F" or $grade=="NE") {
				$repeatoutput .= "REPEAT $course <br>";
				$failcount++;
			}
			

			if ($grade== "A+" or $grade=="A" or $grade=="B+" or $grade=="B" or $grade=="C+") {
				$qualcount++;
			}

			if ($grade== "A+" or $grade=="A" or $grade=="B+" or $grade=="B" or $grade=="C+" or $grade=="C" or $grade=="P") {
				$passcount++;

				if($grade== "A+"){
					$points = 5 * $coursetype + $points;
				}else if($grade== "A"){
					$points = 4 * $coursetype + $points;
				}else if($grade== "B+"){
					$points = 3 * $coursetype + $points;
				}else if($grade== "B"){
					$points = 2 * $coursetype + $points;
				}else if($grade== "C+"){
					$points = 1 * $coursetype + $points;
				}else if($grade== "C"){
					//$points = 0 * $coursetype + $points;
				}
			}

			if ($grade == "D+") {
				$suppcount++;
				$failcount++;

				$suppoutput[$suppcount] = "SUPP IN $course <br>";
				$repeatoutput .= "REPEAT $course <br>";

				if ($grade == "WP") {
					$suppoutput3 .= "DEF IN $course;";
					$countwp=$countwp + 1;
				}
				if ($grade == "DEF") {
					$suppoutput3 = "DEFFERED";
				}
				if ($grade == "EX") {
					$suppoutput3 .= "EXEMPTED IN $course; ";
				}
				if ($grade == "DISQ") {
					$suppoutput3 = "DISQUALIFIED";
					$overallremark .="DISQUALIFIED";
				}
				if ($grade == "SP") {
					$suppoutput3 = "SUSPENDED";
					$overallremark.="SUSPENDED";
				}
				if ($grade == "LT") {
					$suppoutput3 = "EXCLUDE";
					$overallremark.="EXCLUDE";
				}
				if ($grade == "WH") {
					$suppoutput3 = "WITHHELD";
					$overallremark.="WITHHELD";
					$count = 0;
				}
			}
		}

		if ($suppcount >= 2 && $qualcount >1) {
			$failcount = $failcount-2;
			$overallremark .= $suppoutput[1] . $suppoutput[2];
			$hiderepeat = TRUE;
		} else if ($suppcount == 1 && $qualcount >=1) {
			$failcount = $failcount-1;
			$overallremark .= $suppoutput[1];
			$hiderepeat = TRUE;
		} else if ($suppcount > 1 && $qualcount ==1) {
			$failcount = $failcount-1;
			$overallremark .= $suppoutput[1]; 
			$hiderepeat = TRUE;
		}

		$percentage = ($failcount/$count*100)-100;

		if($hiderepeat == FALSE){
			$overallremark .= $repeatoutput;
		}

		if($passcount == $count){
			$percentage = 100;
		}

		if ($year=='1') {

			if ($percentage < 50) {
				$overallremark .="EXCLUDE";
			}else {
				if ($failcount == 0) {
					if ($overallremark=="") {
						$overallremark .=  "CLEAR PASS";
					} else { 
						$overallremark .=  "<br>";
					}
	
					if ($countwp>2){
						$overallremark .="$countwp<br> $suppoutput3<br>";
						$overallremark .= "WITHDRAWN WITH PERMISSION";
					} else {
						$overallremark .=  "$suppoutput3"; 
					}

				}else {
					if ($failcount <= 2) {
						$overallremark .= $suppoutput1;

					}else {
						$overallremark .=  $suppoutput2;
					}
				}
			}
		} else {
		
			if ($percentage < 75) {
				$overallremark="EXCLUDE";
			}else {
				if ($failcount == 0) {
					if ($overallremark=="") {
						$overallremark .=  "CLEAR PASS";
					} else { 
						$overallremark .=  "<br>";
					}
	
					if ($countwp>2){
						$overallremark .="$countwp<br> $suppoutput3<br>";
						$overallremark .= "WITHDRAWN WITH PERMISSION";
					} else {
						$overallremark .=  "$suppoutput3"; 
					}

				}else {
					if ($failcount <= 2) {
						$overallremark .= $suppoutput1;

					}else {
						$overallremark .=  $suppoutput2;
					}
				}
			}
		}
	

		$mincount = 4;
		if($count < $mincount){
			$overallremark = 'INCOMPLETE';

			if($output == ""){
				echo'<div style="float: left; width: 150px; padding-right: 15px;"><b>EMPTY</b></div>';
			}
			echo $output;
		} else {
			// print results
			echo $output;


		}

		$percentage = number_format((float)$percentage, 2, '.', '');

		$overallremark = rtrim($overallremark,'<br>');
		echo'<div style="float: left; width: 150px; padding-right: 15px; font-size: 12px;"><b>'.$overallremark.' <br>('.$percentage.'%)</b></div>';



}	

}
?>
