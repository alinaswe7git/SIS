<?php
class senate {

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

	function showSenate($item) {


	if(isset($_GET['submit'])) {
	
	$year = $_GET['year'];
	$mode = $_GET['mode'];
	$period = $_GET['period'];
	$study = $_GET['program'];
	
	$report='';
	//Report header generation
	$sql_header="SELECT a.ShortName AS ShortName,a.Name AS Name,b.Description AS Description FROM study a LEFT JOIN schools b ON a.ParentID=b.ID WHERE a.ID='$study'";

	$run = $this->core->database->doSelectQuery($sql_header);

	while ($fetch = $run->fetch_assoc()){
		$school = $fetch['Description'];
		$programNo = $fetch['ShortName'];
		$programname = $fetch['Name'];
	}


	//Report Period generation
	$sql_Period="SELECT Year,Name FROM periods WHERE ID='$period'";
	$run = $this->core->database->doSelectQuery($sql_Period);

	while ($fetch = $run->fetch_assoc()){
		$academicyear= $fetch['Year'];
		$semester= $fetch['Name'];
	}

	
	if($year == "%"){
		$sql = "SELECT count(DISTINCT b.CourseID) AS Courses ,CONCAT(a.FirstName,' ',IF(a.FirstName=a.MiddleName,'',a.MiddleName),' ',a.Surname) AS StudentName,
			a.ID AS StudentNo,a.StudyType AS Mode,d.Year AS Year,c.StudyID AS StID
			FROM `basic-information` a,`course-electives` b, `student-study-link` c, `course-year-link` d 
			WHERE c.StudentID = a.ID AND 
			b.StudentID = a.ID AND 
			b.Approved=1 AND 
			d.StudyID=c.StudyID AND 
			d.CourseID=b.CourseID AND 
			c.StudyID = '$study' AND 
			b.PeriodID=$period AND 
			a.StudyType='$mode' 
			GROUP BY a.ID 
			ORDER BY Year,a.Surname	";
			
			//echo $sql;
	} else {
		$sql = "SELECT count(DISTINCT b.CourseID) AS Courses ,CONCAT(a.FirstName,' ',IF(a.FirstName=a.MiddleName,'',a.MiddleName),' ',a.Surname) AS   StudentName,a.ID AS StudentNo,a.StudyType AS Mode,d.Year AS Year,c.StudyID AS StID
			FROM `basic-information` a,`course-electives` b, `student-study-link` c, `course-year-link` d 
			WHERE c.StudentID = a.ID AND 
			b.StudentID = a.ID AND 
			d.StudyID=c.StudyID AND 
			d.CourseID=b.CourseID AND 
			c.StudyID = '$study' AND 
			b.PeriodID=$period AND 
			a.StudyType='$mode' 
			GROUP BY a.ID 
			HAVING MAX(d.`Year`) = $year
			ORDER BY a.Surname";
			
			//echo $sql;
			
	}
	
	$report.= "<fieldset>";
	$report.= "<h2>$school</h2>";
	$report.= "<h5>Summary Results for $programname - ($programNo $year) $mode for $academicyear $semester</h5>";	
	
	$run = $this->core->database->doSelectQuery($sql);


	$studentlist=array();
	$report.= "<table border='1'>\n";

	if($run->num_rows >= 1){
		$SID="";
		$i=0;

		while ($fetch = $run->fetch_assoc()){
			$SID = $fetch['StID'];
			$studentlist[$i] = $fetch['StudentNo'];
			
			$i++;
		}

		$report.= "</table>\n";
		$report.= "<br>";
		$report.= $this->summ1($studentlist,$academicyear, $semester,$SID);
		$report.= $this->performance($academicyear, $semester, $studentlist,$study,$year,$mode);
		$report.= $this->performance_overall($academicyear, $semester, $studentlist,$study,$year,$mode);

	} else {

		$report.= "<h3>No data found</h3>";

	}

}

include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

$select = new optionBuilder($this->core);

$periods = $select->showPeriods(null);
$study = $select->showStudies(null);



//$periods = $this->showPeriods(null);
//$study = $this->showStudies(null);


echo'<div class="container-fluid">
<div class="row-fluid">';
 
 

	if(isset($_GET['submit'])) {
		echo $report;
	}else{
		echo '<form id="senate" name="editsenate" method="GET">
		<div class="control-group">
	        <label class="control-label" for="Program">Program/Study</label>
	        <div class="controls">
			<select name="program" style="width: 490px">
					'. $study .'
			</select></div>
	      </div>
		<div class="control-group">
	        <label class="control-label" for="Mode">Mode</label>
	        <div class="controls">
			<select name="mode">
				<option value="Fulltime">Fulltime</option>
				<option value="Distance">Distance</option>
			</select>
			</div>
	      </div>
		<div class="control-group">
	        <label class="control-label" for="Year">Year</label>
	        <div class="controls">
			<select name="year" >
				<option value="1">1st Year</option>
				<option value="2">2nd Year</option>
				<option value="3">3rd Year</option>
				<option value="4">4th Year</option>
				<option value="5">5th Year</option>	
				<option value="6">6th Year</option>	
				<option value="%">ALL</option>	
			</select>
		   </div>
     	</div>
		 <div class="control-group">
        	<label class="control-label" for="Period">Period</label>
        	<div class="controls"> 
			<select name="period" >
				'. $periods .'
			</select>
			</div>
	      </div><div class="control-group">
	        <div class="controls">
	          <button class="btn btn-success" name="submit" value="submit">Submit</button>
	        </div>
	      </div>
		</fieldset></form></div>';
	}
}


	function summ1($studentlist,$academicyear, $semester,$SID){
		
		$list = implode(",",$studentlist);
		if($SID == 216 ){
			$query="SELECT 'Students with Clear Pass' as Description, count(a.remark) as 'Students' 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as a where a.remark like '%clear%'
			union SELECT 'Students with SUPP Remarks',count(b.remark) FROM 
			(SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as b where b.remark like '%supp%'
			union SELECT 'Students with Proceed Remarks',count(b.remark) FROM 
			(SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as b where b.remark like '%REPEAT%'
			union SELECT 'Students excluded',count(c.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as c  where c.remark like '%excl%' 
			union SELECT 'Students on Part Time',count(d.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as d where d.remark like '%part%'
			union SELECT 'TOTAL',count(e.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as e";
		}else{
			$query="SELECT 'Students with Clear Pass' as Description, count(a.remark) as 'Students' 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as a where a.remark like '%clear%'
			union SELECT 'Students with Proceed Remarks',count(b.remark) FROM 
			(SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as b where b.remark like '%proceed%'
			union SELECT 'Students excluded',count(c.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as c  where c.remark like '%excl%' 
			union SELECT 'Students on Part Time',count(d.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as d where d.remark like '%part%'
			union SELECT 'TOTAL',count(e.remark) 
			FROM (SELECT * 
			FROM studentremark 
			where studentno in ($list) and academicyear='$academicyear' and semester='$semester'
			GROUP BY studentno ) as e";
		}
		//echo $query." <br/>";
		
		return $this->qToTable($query);
		
		
	} // end of function detail



	function qToTable($query){
	 	//given a query, automatically creates an HTML table output
	  	$output = "";

		$run = $this->core->database->doSelectQuery($query);


	  	$output .= "<table border = '1'>";
	  	//get column headings
	
		//get field names
		$output .= "<tr>";
		$output .= "  <th>No.</th>";
		$output .= "  <th>Description</th>";
		$output .= "  <th>No. of Students</th>";
		$output .= "</tr>";

		//get row data as an associative array
		$num = 1;
	
		while ($fetch = $run->fetch_assoc()){
			$description = $fetch['Description'];
			$students = $fetch['Students'];

			$output .= "<tr>\n";
			//look at each field
			$output .= "  <td>$num</td>\n";
			$output .= "  <td>".$description."</td>\n";
			$output .= "  <td>".$students."</td>\n";
			$output .= "</tr>\n\n";
		
			$num =$num + 1; 
	 	}// end while

		$output .= "</table>\n";
		return $output;
	} 
	

	function GeneratePoints() {
		
		$query = "UPDATE
					grades AS Table_A
					INNER JOIN ( SELECT ID,CourseNo, 
						IF(Grade = 'INC', '0',IF(Grade = 'P', '0',
						IF(Grade = 'A+' ,'2.5',
						 IF(Grade  = 'A' ,'2',
						  IF(Grade  = 'B+','1.5',
						   IF(Grade = 'B' ,'1',
							IF(Grade = 'C+' ,'0.5',
							 IF(Grade = 'C' ,'0',
							   IF(Grade = 'D+' ,'0','0'))))))))) AS 'Points'	   
						FROM grades )  AS Table_B
						ON Table_A.ID = Table_B.ID
				SET
					Table_A.Points = Table_B.Points
				WHERE
					Table_A.Points = 0";
		$result =  $this->db->query($query);
		
		return TRUE;
		
		
	}


	function performance ($academicyear, $semester, $studentlist,$study,$year,$mode){
		$output="";
		//Report header generation
		$sql_header="SELECT a.ShortName AS ShortName,a.Name AS Name,b.Description AS Description FROM study a LEFT JOIN schools b ON a.ParentID=b.ID WHERE a.ID='$study'";

		$run = $this->core->database->doSelectQuery($sql_header);

		while ($fetch = $run->fetch_assoc()){
			$school = $fetch['Description'];
			$programno = $fetch['ShortName'];
			$programname = $fetch['Name'];
		}

	
		$output .=  "<h5>Student performance for $programname ($programno $year)-$mode for each course for academic year $academicyear - $semester</h5>";
		$list = implode(",",$studentlist);
		//echo $list;
		
		$sql = "SELECT courseno from grades where academicyear='$academicyear' and semester='$semester' and studentno in ($list) group by courseno" ;
		$run = $this->core->database->doSelectQuery($sql);
		
		$output .= "<table border='1'>";
		$output .=  "<tr><td><b>CourseNo</b></td>";
		$output .=  "<td><b>Course Name</b></td>";
		$output .=  "<td><b>A+ (#)</b></td>";
		$output .=  "<td><b>A (#)</b></td>";
		$output .=  "<td><b>B+ (#)</b></td>";
		$output .=  "<td><b>B (#)</b></td>";
		$output .=  "<td><b>C+ (#)</b></td>";
		$output .=  "<td><b>C (#)</b></td>";
		$output .=  "<td><b>D+ (#)</b></td>";
		$output .=  "<td><b>D (#)</b></td>";
		$output .=  "<td><b>EXP (#)</b></td>";
		$output .=  "<td><b>NE/INC (#)</b></td>";
		$output .=  "<td><b>P (#)</b></td>";
		$output .=  "<td><b>F (#)</b></td>";
		$output .=  "<td><b>Totals</b></td></tr>";
		

		

		while ($fetch = $run->fetch_assoc()){
			$courseno = $fetch['courseno'];

			$output .= $this->perform1($academicyear, $semester,$courseno,$list);
			//$output .= "<tr><td colspan='12'>$courseno</td></tr>" ;
		}
		
		$output .= "</table>"; 
		
		return $output;
	}


	function performance_overall ($academicyear, $semester, $studentlist,$study,$year,$mode){
	
		$output="";
		//Report header generation
		$sql_header="SELECT a.ShortName AS ShortName,a.Name AS Name,b.Description AS Description FROM study a LEFT JOIN schools b ON a.ParentID=b.ID WHERE a.ID='$study'";

		$run = $this->core->database->doSelectQuery($sql_header);

		while ($fetch = $run->fetch_assoc()){
			$school = $fetch['Description'];
			$programNo = $fetch['ShortName'];
			$programname = $fetch['Name'];
		}
	
		$output .=  "<h5>Student performance for $programname ($programno $year)-$mode for each <b>course across the board</b> for academic year $academicyear - $semester</h5>";
		$list = implode(",",$studentlist);

		
		$query1 = "SELECT courseno from grades where academicyear='$academicyear' and semester='$semester' and studentno in ($list) group by courseno" ;
		$run = $this->core->database->doSelectQuery($query1);

		
		$output .= "<table border='1'>";
		$output .=  "<tr><td><b>CourseNo</b></td>";
		$output .=  "<td><b>Course Name</b></td>";
		$output .=  "<td><b>A+ (#)</b></td>";
		$output .=  "<td><b>A (#)</b></td>";
		$output .=  "<td><b>B+ (#)</b></td>";
		$output .=  "<td><b>B (#)</b></td>";
		$output .=  "<td><b>C+ (#)</b></td>";
		$output .=  "<td><b>C (#)</b></td>";
		$output .=  "<td><b>D+ (#)</b></td>";
		$output .=  "<td><b>D (#)</b></td>";
		$output .=  "<td><b>NE/INC (#)</b></td>";
		$output .=  "<td><b>P (#)</b></td>";
		$output .=  "<td><b>F (#)</b></td>";
		$output .=  "<td><b>Totals</b></td></tr>";
		
		while ($fetch = $run->fetch_assoc()){
			$courseno = $fetch['courseno'];
			$output .= $this->perform1_overall($academicyear, $semester,$courseno,$list);
		}
		
		$output .= "</table>"; 
		
		return $output;
	}



	function perform1 ($academicyear, $semester, $courseno,$list) {
		$output = "";
		
		$aplus=0;$a=0;$bplus=0;$b=0;$cplus=0;$c=0;$dplus=0;$d=0;$ne=0;$in=0;$p=0;$f=0;$exp=0;
		
		$sql = "SELECT p1.grade AS Grade,count(p1.grade) AS Number, p2.CourseDescription AS CourseName FROM grades p1, courses p2 WHERE p1.studentno in ($list) and p1.academicyear='$academicyear' and semester='$semester' and p1.courseno='$courseno' and p1.courseno=p2.Name group by p1.grade" ;
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()){

			$grade = $fetch['Grade'];
			$number = $fetch['Number'];
			$coursename = $fetch['CourseName'];
			
			if ($grade=="A+")
			{ $aplus=$number; }
			if ($grade=="A")
			{ $a=$number; }
			if ($grade=="B+")
			{ $bplus=$number; }
			if ($grade=="B")
			{ $b=$number; }
			if ($grade=="C+")
			{ $cplus=$number; }
			if ($grade=="C")
			{ $c=$number; }
			if ($grade=="D+")
			{ $dplus=$number; }
			if ($grade=="D")
			{ $d=$number; }
			if ($grade=="NE" || $grade=="INC")
			{ $ne=$number; }
			if ($grade=="P")
			{ $p=$number; }
			if ($grade=="EXP")
			{ $exp=$number; }
			if ($grade=="F")
			{ $f=$number; }
		} // end while
		$tot=$aplus+$a+$bplus+$b+$cplus+$c+$dplus+$d+$ne+$in+$p+$f+$exp;

		// enable percentages
		if ($tot<>0){
			//$aplus=$aplus/$tot*100;
			//$a=$a/$tot*100;
			//$bplus=$bplus/$tot*100;
			//$b=$b/$tot*100;
			//$cplus=$cplus/$tot*100;
			//$c=$c/$tot*100;
			//$dplus=$dplus/$tot*100;
			//$d=$d/$tot*100;
			//$wh=$wh/$tot*100;
			//$p=$p/$tot*100;
			//$f=$f/$tot*100;
			$nothing=5;
		}
		$output .= "<tr><td>$courseno</td>";
		$output .= "<td>$coursename</td><td>";
		$output .= number_format($aplus,0);
		$output .= "</td><td>";
		$output .= number_format($a,0);
		$output .= "</td><td>";
		$output .= number_format($bplus,0);
		$output .= "</td><td>";
		$output .= number_format($b,0);
		$output .= "</td><td>";
		$output .= number_format($cplus,0);
		$output .= "</td><td>";
		$output .= number_format($c,0);
		$output .= "</td><td>";
		$output .= number_format($dplus,0);
		$output .= "</td><td>";
		$output .= number_format($d,0);
		$output .= "</td><td>";
		$output .= number_format($ne,0);
		$output .= "</td><td>";
		$output .= number_format($p,0);
		$output .= "</td><td>";
		$output .= number_format($exp,0);
		$output .= "</td><td>";
		$output .= number_format($f,0);
		$output .= "<td><b>$tot</b></td><tr>";
		
		return $output;
	} // end of function detail



	function perform1_overall ($academicyear, $semester, $courseno){
		
		$output = "";
		
		$aplus=0;$a=0;$bplus=0;$b=0;$cplus=0;$c=0;$dplus=0;$d=0;$ne=0;$in=0;$p=0;$f=0;$exp=0;
		
		$sql = "SELECT p1.grade AS Grade,count(p1.grade) AS Number, p2.CourseDescription AS CourseName FROM grades p1, courses p2 WHERE p1.academicyear='$academicyear' and semester='$semester' and p1.courseno='$courseno' and p1.courseno=p2.Name group by p1.grade" ;
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()){

			$grade = $fetch['Grade'];
			$number = $fetch['Number'];
			$coursename = $fetch['CourseName'];
			
			if ($grade=="A+")
			{ $aplus=$number; }
			if ($grade=="A")
			{ $a=$number; }
			if ($grade=="B+")
			{ $bplus=$number; }
			if ($grade=="B")
			{ $b=$number; }
			if ($grade=="C+")
			{ $cplus=$number; }
			if ($grade=="C")
			{ $c=$number; }
			if ($grade=="D+")
			{ $dplus=$number; }
			if ($grade=="D")
			{ $d=$number; }
			if ($grade=="NE" || $grade=="INC")
			{ $ne=$number; }
			if ($grade=="P")
			{ $p=$number; }
			if ($grade=="EXP")
			{ $exp=$number; }
			if ($grade=="F")
			{ $f=$number; }
		} // end while
		$tot=$aplus+$a+$bplus+$b+$cplus+$c+$dplus+$d+$ne+$in+$p+$f+$exp;

		// enable percentages
		if ($tot<>0){
			//$aplus=$aplus/$tot*100;
			//$a=$a/$tot*100;
			//$bplus=$bplus/$tot*100;
			//$b=$b/$tot*100;
			//$cplus=$cplus/$tot*100;
			//$c=$c/$tot*100;
			//$dplus=$dplus/$tot*100;
			//$d=$d/$tot*100;
			//$wh=$wh/$tot*100;
			//$p=$p/$tot*100;
			//$f=$f/$tot*100;
			$nothing=5;
		}
		$output .= "<tr><td>$courseno</td>";
		$output .= "<td>$coursename</td><td>";
		$output .= number_format($aplus,0);
		$output .= "</td><td>";
		$output .= number_format($a,0);
		$output .= "</td><td>";
		$output .= number_format($bplus,0);
		$output .= "</td><td>";
		$output .= number_format($b,0);
		$output .= "</td><td>";
		$output .= number_format($cplus,0);
		$output .= "</td><td>";
		$output .= number_format($c,0);
		$output .= "</td><td>";
		$output .= number_format($dplus,0);
		$output .= "</td><td>";
		$output .= number_format($d,0);
		$output .= "</td><td>";
		$output .= number_format($ne,0);
		$output .= "</td><td>";
		$output .= number_format($p,0);
		$output .= "</td><td>";
		$output .= number_format($f,0);
		$output .= "<td><b>$tot</b></td><tr>";
		
		return $output;
	} // end of function detail


	function showPeriods($study, $selected = null) {

		if ($study != null) {
			$sql = "SELECT `periods`.ID AS Value, CONCAT(`periods`.Year, ' - ', `periods`.Name) AS Name FROM `periods` ORDER BY `ID` DESC";
		} else {
			$sql = "SELECT `periods`.ID AS Value, CONCAT(`periods`.Year, ' - ', `periods`.Name) AS Name FROM `periods` ORDER BY `ID` DESC";
		}

		$fetch = $this->core->database->doSelectQuery($sql);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}
	
	function showStudies($selected = null) {

		$sql = "SELECT `ID` AS Value, `Name` FROM `study` ORDER BY `Name`";

		$fetch = $this->core->database->doSelectQuery($sql);
		$out = $this->buildSelect($fetch, $selected);

		return ($out);
	}

	public function buildSelect($run, $selected = NULL) {
		$begin = "";
		$out = "";

		if (!empty($run)) {

			foreach ($run as $row) {

				$name = $row->Name;
				$uid = $row->Value;

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

}
?>

