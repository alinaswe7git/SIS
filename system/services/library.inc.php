<?php
class library {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
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
			$period['year'] =$date_year."/".$p_year; 
			$period['semester'] ="Semester I";
		}else if($date_month <=6){
			$period['year'] = $m_year."/".$date_year; 
			$period['semester'] = "Semester II";
		}

		return $period;
	}



	public function runService($core) {
		$this->core = $core;
		
		$uid = strtolower($this->core->cleanGet['uid']);

		$period = $this->getPeriod();
		$year = $period['year'];
		$semester = $period['semester'];

		echo'<script>
			setTimeout(function(){
			   window.location.reload(1);
			}, 1000);
		</script>'; 

		
		$sql = "SELECT *, `basic-information`.Status as STAT, `basic-information`.StudyType as ST, `basic-information`.ID AS UID, `bd_catering`.`student_id` as BOARDER, `bd_catering`.dh
			FROM `accesscards`
			LEFT JOIN `bd_catering` ON `accesscards`.UserID = `bd_catering`.`student_id` AND `semester_id` = '$semester' AND `academic_year_id` = '$year'
			LEFT JOIN `basic-information` ON `basic-information`.ID = `accesscards`.UserID 
			LEFT JOIN `student-data-other` ON `accesscards`.UserID = `student-data-other`.StudentID
			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `accesscards`.UserID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			 WHERE `CardID` = '$uid' ";

		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$uid = $row['UID'];
			$name = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['Surname'];
			$output["delivery"] = $row['StudyType'];
			$status = $row['STAT'];
			$boarder = $row['BOARDER'];
			$hall = $row['dh'];

			if($status != "Approved" && $status != "Requesting"){
				$error = '<h1>NO STUDENT</h1>';
				echo $error;
				return;
			}

			if($boarder == ''){
				$error = '<h1>NOT IN BOARDING</h1>';
				echo $error;
				return;
			}

			$result = TRUE;
		}




		if($result!=TRUE){
			$error = '<h1>NO STUDENT</h1>';
			echo $error;
			return;
		}

		// MEAL TIMES
		$breakfastend = "1000";
		$dinnerstart = "1601";

		$currenttime = date("Hi");
		$time = date("H:i:s");
		$date = date("Y-m-d");

		// DISPAY DATA ABOUT STUDENT
		if($currenttime < $breakfastend){
			$type = "Breakfast";
		}else if($currenttime>$breakfastend && $currenttime < $dinnerstart){
			$type = "Lunch";
		}else if($currenttime>$dinnerstart){
			$type = "Dinner";
		}

		$currenttime = date("His");
		$dh="";
		if($_SERVER['REMOTE_ADDR']=='41.63.18.200'){
			$dh="Main DH";
		}elseif($_SERVER['REMOTE_ADDR']=='41.63.52.160'){
			$dh = "Town Campus DH";
		}else{
			$dh = "Rock DH";
		}

		$sql = "INSERT INTO `meals` (`ID`, `StudentID`,`Type`,`Date`,`Time`,`IP`,`DH`) VALUES (NULL, '$uid', '$type', NOW(), '$time','".$_SERVER['REMOTE_ADDR']."','$dh');";
		if($this->core->database->doInsertQuery($sql, TRUE) == TRUE){
			echo '<div style="background-color: green;  width: 100%; height: 100%; text-align: center;">';
			echo '<h1>SCANNED</h1>';
		} else {
			$sqlx = "SELECT * FROM `meals` WHERE `StudentID` = '$uid' AND `Date` = '$date' AND `Type` = '$type'";
			$runx = $this->core->database->doSelectQuery($sqlx);
			while ($rowx = $runx->fetch_assoc()) {
				$time = $rowx['Time'];
				$stime = strtotime($rowx['Time']);
				$stime = date("His", $stime);

				$stime = $currenttime-$stime;
				if($stime > 120){
					echo '<div style="background-color: red; width: 100%; height: 100%; text-align: center;">';
					echo '<h1> ALREADY SCANNED</h1>';
				} else {
					echo '<div style="background-color: green; width: 100%; height: 100%;  text-align: center;">';
					echo '<h1>SCANNED</h1>';
				}
			}

		} 

		if($hall == "1"){
			$hall = "Main DH";
		}elseif($hall == "2"){
			$hall = "Rock DH";
		}elseif($hall == "3"){
			$hall = "Town Campus DH";
		}

		
		echo'<h1><span style="font-size: 35pt;">'.$uid.' - '. $name . ' </span><br> COLLECTED '.$type.'<br> at '.$time.' ('.$date.')<br>FROM '.$hall.'</h1>';
 
		echo'<a href="'.$this->core->conf['conf']['path'].'/picture/make/'.$uid.'">';
		if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
			echo '<img width="400px" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
		} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
			echo '<img width="400px" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
		} else {
			echo '<img width="400px" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
		}
		echo'</a>';
		$currentdate =date("Y-m-d");
		
		$sql_count = "SELECT DH,Type,count(Type) as Num FROM meals WHERE `Date`='$currentdate' GROUP BY DH,Type";
		

		$run_count = $this->core->database->doSelectQuery($sql_count);
		echo " <br> ";
		
		echo ' <br> <div style="text-align: center;">';
		echo ' Meals for Date:'.date("d-m-Y");;
		
		echo " <table align='center'><tr><td><b>DH</b></td><td><b>Meal</b></td><td><b>Total</b></td></tr>";
		
		while ($row_count = $run_count->fetch_assoc()) {
			echo "<tr><td>".$row_count['DH']."</td><td>".$row_count['Type']."</td><td><b>".$row_count['Num']."</b></td></tr>";
			
		}
		echo " </table>";
		echo'</div>';
		
		echo'</div>';
		
		
		
		
		
	}
}
?>