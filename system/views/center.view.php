<?php
class center {

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

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/information/search">Send a bulk SMS message</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/approve/all">Approve all SMS</a>'.
		'</div>';
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


	public function setCenter($item){
		$center = $this->core->cleanGet['center'];

		$periodid = $this->getPeriod();

		if($center == ''){

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
			$select = new optionBuilder($this->core);
			$centers = $select->showCenters(NULL);
	

			echo '<div style="padding-left: 15px;"><form id="centersubmit" name="coursessubmit" method="GET" action="'. $this->core->conf['conf']['path'] . '/center/set/'.$item.'">
				<div class="heading">UPDATE YOUR EXAM CENTER</div>
				<div class="label">Please select your center: </div>
				<input type="hidden" name="userid" value="'.$userid.'">
				<select name="center">
					<option value="" selected>SELECT HERE</option>
					'.$centers.'
				</select>
				<br>
				<br><input type="submit"  value="Save center"></form></div>';
			return;

		} else {

			$item = $item/4;
			echo '<div class="successpopup">Your exam center has been set as '.$center .'</div>';

			$sql = "INSERT INTO `student-data-other` (`ID`, `StudentID`, `YearOfStudy`, `ExamCentre`,`DateTime`, `PeriodID`) 
			VALUES (NULL, '$item', '', '$center', NOW(), '$periodid') ON DUPLICATE KEY UPDATE `ExamCentre` = '$center',`DateTime`= NOW()";

			$this->core->database->doInsertQuery($sql);
		}
	}
}
?>
