<?php
class history { 

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

	function historyHistory($item) {
		setlocale(LC_MONETARY, 'en_US.UTF-8');
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start']; 
		$end = $_GET['end'];

		$period = $this->core->cleanGet['period'];
		$mode = $this->core->cleanGet['mode'];

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$periods = $optionBuilder->showPeriods();

		echo '<form id="narrow" name="narrow" method="get" action="">
			<div class="toolbar">
				<div class="toolbaritem">SELECT FILTER: 
					<select name="mode" class="submit" style="width: 105px;  margin-top: -17px;">
						<option value="Fulltime">Fulltime</option>
						<option value="Distance">Distance</option>
						<option value="Parttime">Part-time</option>
						<option value="Parttime">Block Release</option>
						<option value="all" selected>ALL</option>
					</select>
					<select name="period" class="submit" style="width: 105px;  margin-top: -17px;">
						'.$periods.'
					</select>
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/>
				</div>
			</div></form>';


		echo '<h2>REGISTERED STUDENTS BY PROGRAM</h2>';

		if($period == ''){
			$period = $this->core->getPeriod();
		} 

		if($method == 'all'){
			$period = '%';
		}
	
		if($mode == ''){
			$mode = '%';
		}

		$sql = "SELECT COUNT(DISTINCT `basic-information`.ID), `basic-information`.StudyType, `Sex`, `study`.Name, `study`.ID, (SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID) AND StudyID=`student-study-link`.StudyID) as YEART
			FROM `basic-information`, `student-study-link`, `study`, `course-electives` 
			WHERE `study`.ID = `student-study-link`.StudyID 
			AND `basic-information`.ID = `student-study-link`.StudentID 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `basic-information`.`StudyType` LIKE '$mode'
			AND `Sex` IN ('Male', 'Female', 'Unknown')
			AND `course-electives`.PeriodID = '$period'
			GROUP BY  `YEART`,`StudyID`
			ORDER BY `basic-information`.StudyType, `study`.Name, `YEART` ASC";
		echo $sql;

		$run = $this->core->database->doSelectQuery($sql); 

		echo'<table class="table-striped table-hover" style="padding: 0px;">
			<tr class="heading" >
				<td>Study</td>
				<td>YEAR</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
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

			$sid[] = $i;
			$school = $i;
			$sex = $fetch[2];

			$schoolid[$school] = $fetch[4];
			$delivery[$school] = $fetch[1];
			$schools[$school] = $fetch[3];
			$year[$school]  = $fetch[5];

			if($sex == 'Female'){
				$female[$school]  = $fetch[0];
			} else {
				$male[$school]  = $fetch[0];
			}

			$count[$school] = $male[$school]+$female[$school];
			$tfemale = $female[$school] + $tfemale;
			$tmale = $male[$school] + $tmale;
			$tcount = $count[$school] + $tcount;
			$tmale = $male[$school] + $tmale;

		}


		foreach($sid as $school){

			echo'<tr>
				<td>'.$delivery[$school].'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/search?search=reported&q='.$schoolid[$school].'">'.$schools[$school].'</a></td>
				<td>Year '.$year[$school].'</td>
				<td>'.$male[$school].'</td>
				<td>'.$female[$school].'</td>
			</tr>';

		}

		echo'<tr class="heading">
			<td>TOTAL</td>
			<td></td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$tcount .'</td>

		</tr>';
		echo'</table>';

		$tmale = 0; $tfemale = 0; $tcount = 0;
		
		
		echo'<br>';
		echo'<hr>'; 
		echo '<br /> <h2>Students by PROGRAM by Year</h2>';
		
		
		///Students in study-per-year
		echo'<table class="table-striped table-hover" style="padding: 0px;">
			<tr class="heading" >
				<td>DELIVERY</td>
				<td>Study Per-YEAR</td>
				<td>REGISTERED</td>
			</tr>';

		$period = $this->core->getPeriod();
		
		
		$sqlm="SELECT *,COUNT(ProgTB.ProgYear) as 'TOT' 
		FROM	(SELECT MAX(Year) FROM `course-year-link` WHERE CourseID IN (SELECT CourseID FROM `course-electives` WHERE StudentID=`basic-information`.ID)) as 'Year',
			`study`.Name,
			AS 'ProgYear',
			`course-electives`.PeriodID
		FROM `basic-information`, `student-study-link`, `study`, `course-electives`
		WHERE `study`.ID = `student-study-link`.StudyID 
		AND `basic-information`.ID = `student-study-link`.StudentID 
		AND `basic-information`.ID = `course-electives`.StudentID
		AND `course-electives`.PeriodID = $period 
		AND `basic-information`.StudyType = 'Distance'
		GROUP BY `basic-information`.ID,`study`.ID
		ORDER BY study.`Name`,Year) as ProgTB
		GROUP BY ProgTB.ProgYear";
	
	}
}
?>
