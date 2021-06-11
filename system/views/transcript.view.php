<?php
class transcript {

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


	public function footerTranscript($item){


		echo'<h2 class="break">Key to Understanding Grades</h2>
		<h3>Pass Grades</h3> 
		<TABLE>
		<TR>
		  <TD width="100">A+</TD>
		  <TD width="800">Distinction</TD>
		 </TR>
		<TR>
		  <TD>A</TD>
		  <TD>Distinction</TD>
		 </TR>
		 <TR>
		  <TD>B+</TD>
		  <TD>Meritorious</TD>
		 </TR>
		 <TR>
		  <TD>B</TD>
		  <TD>Very Satisfactory</TD>
		 </TR>
		 <TR>
		  <TD>C+</TD>
		  <TD>Clear Pass</TD>
		 </TR>
		 <TR>
		  <TD>C</TD>
		  <TD>Bare Pass</TD>
	 </TR>
	 <TR>
	  <TD>S</TD>
	  <TD>Satisfactory, Pass in a Practical Course or Oral Examinations</TD>
	 </TR>
	 <TR>
	  <TD>P</TD>
	  <TD>Pass in a Supplementary Examination or Pass in a Practical Course</TD>
	 </TR>
	</TABLE>

	<h3>Fail Grades</h3> 
	<TABLE>
	<TR>
	  <TD width="100">D+</TD>
	  <TD width="800">Bare Fail</TD>
	 </TR>
	<TR>
	  <TD>D</TD>
	  <TD>Definite Fail</TD>
	 </TR>
	 <TR>
	  <TD>F</TD>
	  <TD>Fail in a Supplementary Examination</TD>
	 </TR>
	 <TR>
	  <TD>U</TD>
	  <TD>Unsatisfactory, Fail in a practical Course/  Thesis or Oral Examinations</TD>
	 </TR>
	 <TR>
	  <TD>NE</TD>
	  <TD>No Examination Taken</TD>
	 </TR>
	 <TR>
	  <TD>RS</TD>
	  <TD>Re-sit course examination only</TD>
	 </TR>
	</TABLE>

	<h3> Other Grades</h3> 
	<TABLE>
	<TR>
	  <TD width="100">WP</TD>
	  <TD width="800">Withdrawn from course with permission</TD>
	 </TR>
	<TR>
	  <TD>DC</TD>
	  <TD>Deceased during course</TD>
	 </TR>
	 <TR>
	  <TD>EX</TD>
	  <TD>Exempted</TD>
	 </TR>
	 <TR>
	  <TD>INC</TD>
	  <TD>Incomplete</TD>
	 </TR>
	 <TR>
	  <TD>DEF</TD>
	  <TD>Deferred Examination</TD>
	 </TR>
	 <TR>
	  <TD>SP</TD>
	  <TD>Supplementary Examination</TD>
	 </TR>
	  <TR>
	  <TD>DISQ</TD>
	  <TD>Disqualified</TD>
	 </TR>
	 </TABLE>

	<p>This transcript is not valid if it does not bear the '.$this->core->conf['conf']['organization'].' <b>date Stamp</b> or if it has <b>alterations.</b></p>
	<h2>&nbsp;</h2>
	<h2>&nbsp;</h2>
	</div>
	</div>';

		

	}


	public function resultsTranscript($item){

		echo '<script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the pages HTML with divs HTML only
            document.body.innerHTML = 
              "<html><head><title></title></head><body>" + 
              divElements + "</body>";

            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

          
        }
		</script>';
		
		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}
		



		$studentID = $_GET['uid'];
		$studentNo = $studentID;

		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, IF(MiddleName = Firstname,'',MiddleName) AS MiddleName, Surname, `basic-information`.Status, Sex, Name,`study`.StudyType,`graduation`.Status,`graduation`.DateTime FROM `basic-information`, `study`, `student-study-link` LEFT JOIN `graduation` ON `student-study-link`.`StudentID` = `graduation`.`StudentID`
		WHERE `basic-information`.ID = '$studentID' AND	 `student-study-link`.`StudentID` = `basic-information`.ID AND `student-study-link`.`StudyID` = `study`.ID 
		AND `basic-information`.Status != 'Expelled'";

		$run = $this->core->database->doSelectQuery($sql);

			
			echo'<div id="printablediv">
			<table width="100%"><tr><td colspan=3><center><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /><br>
			<font size=5>'.$this->core->conf['conf']['organization'].'</font><br>
			<font size=4>OFFICE OF THE REGISTRAR</font></center>
			</td></tr>
			<tr><td>Fax: +260 215 228003<br>Tel: +260 215 228004<br> Email: registrar@mu.ac.zm</td><td align="right">
			Great North Road<br>
			P O Box 80415<br>
			<b> KABWE</td></tr>
			<tr><td colspan="3"><hr size=2></td></tr>
			</table>';
			
			



		while ($fetch = $run->fetch_row()){
			$i++;

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[3];
			$sex=$fetch[4]; 
			$studentname = $firstname . " " . $middlename . " " . $surname;
			
			
			$studyType=$fetch[6];
			$programme=$fetch[5];

			$graduation=$fetch[7]; 
			$grad=$fetch[8];
			
			//echo '<h3>Somthing</h3>'.$studentname;
			
			switch ($sex) {
				case "Male":
					$title="He";
					break;
				case "Female":
					$title="She";
					break;
				default:
					$title="He/She";
			}
			
			echo"<p><span style=\"font-size: 12px\"><br> This to certify that: <b>$studentname</b> - Student No.: <b>$studentNo</b><br>
			was a registered student of <b> ".$this->core->conf['conf']['organization']."</b><br>
			studying: <b>$programme</b> from the academic session: <b>$start</b>.<br><br>
			His/her academic performance was as follows:<br></span> </p>";

			$this->academicyear($studentNo);


			echo "<br>";

			switch ($remark) {
				case "Deceased":
					Print "$title was Deceased<br><br>";
					break;
				case "Exclude":
					Print "$title was Excluded<br><br>";
					break;
				case "WP":
					Print "$title withdrew with permission<br><br>";
					break;
				case "Approved":
					print "$title will be awarded the specified degree upon completion of studies<br><br>";
					break;
				case "Graduated":
					if ($studyType < 4){
					$graduation1=date('dS F Y',strtotime($grad));
						echo "$title was awarded a <b>$programme</b>
						degree with <b>$graduation</b> at the graduation ceremony held on $graduation1<br><br>";
					}else {
						$graduation1=date('d M Y',strtotime($grad));
						echo "$title was awarded a <b>$programme</b>
						degree at the graduation ceremony held on $graduation1<br><br>";
					}
					break;
				default:
					print "$title will be awarded the specified degree upon completion of studies<br><br>";
					break;
					
			}
			
			$date = date('d/m/y');
			
			echo "<br><br><br> <b>Gubula C. Siaciti <br> Registrar </b><br> <br> <b>A key to understanding of the grades is on the reverse side of this statement</b><br>";
			print "<br>Date:$date";
		}

		if($i==0){
			echo'<center><h1>NO TRANSCRIPT READY FOR THIS STUDENT</h1></center>';
		}
		
		//$this->footerTranscript();
		echo'</div>';
		

	}

	private function academicyear($studentNo) {
		global $remark, $count, $count1;
	

		$sql = "SELECT distinct academicyear, semester FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";
		$run = $this->core->database->doSelectQuery($sql);

		$count = 1; 
		
		while ($fetch = $run->fetch_array()){

			$acyr = $fetch[0];
			$semester = $fetch[1];

			

			$this->detail($studentNo, $acyr, $semester);
			
			
			if (($count == 2 or $count == 4 or $count==6 or $count==8 or $count==10) ) {
				$date = date('d/m/y');
			
			echo "<br><br><br> <b>Gubula C. Siaciti <br> Registrar </b><br> <br> <b>A key to understanding of the grades is on the reverse side of this statement</b><br>";
			print "<br>Date:$date";

				
				if (($count == 2 or $count==4 or $count==6 or $count==8 or $count==10 ) ){
					
					echo'<P style="page-break-before: always">
					<table width="100%"><tr><td colspan=3><center><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /><br>
					<font size=5>'.$this->core->conf['conf']['organization'].'</font><br>
					<font size=4>OFFICE OF THE REGISTRAR</font></center>
					</td></tr>
					<tr><td>Fax: +260 215 228003<br>Tel: +260 215 228004<br> Email: registrar@mu.ac.zm</td><td align="right">
					Great North Road<br>
					P O Box 80415<br>
					<b> KABWE</td></tr>
					<tr><td colspan="3"><hr size=2></td></tr>
					</table>';
					print "<p><span style=\"font-size: 12px\"><br>Transcript for Student No.:<b>$studentNo</b> - Continued</span></p>";
				}
			}
			
			$count = $count + 1;
		}

	}

	private function detail($studentNo, $acyr, $semester) {

		echo "<p> <span style=\"font-size: 14px; font-weight: bold;\"> $acyr &nbsp ($semester) </span>
		<table width=\"100%\"  style=\"font-size: 12px; text-align: left;\">\n <tr >\n <th >COURSE</th>\n <th >COURSE NAME</th>\n  <th >GRADE</th>\n </tr>\n\n";

		$sql = "SELECT 
				p1.CourseNo,
				p2.CourseDescription,
				p1.Grade
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			AND	p1.Semester = '$semester' 
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);
		
		while ($fetch = $run->fetch_array()){
			$course = strtoupper($fetch[0]);
			print "<tr>\n";
			print "<td width=10%>$course</td>\n";
			print "<td width=80%>$fetch[1]</td>\n";
			print "<td width=10%>$fetch[2]</td>\n";
			print "</tr>\n\n";
		}

		print "</table></p>\n ";
	}

}
?>
