<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery('.ddsel').ddslick({width:280, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});

});

</script>


<form id="editprogram" name="editprogram" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/programmes/save/" . $this->core->item; ?>">
	<p>You are editing:<b> <?php echo $fetch['Name']; ?></b>  </p>
	<p>

	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
        </tr>

	<tr><td width="150">Full name of study</td>
	<td><input name="fullname" type="text" value="<?php echo $fetch['Name']; ?>"></b></td>
	<td></td>
	</tr>

	<tr>
	<td>Short menu name for study</td>
	<td><input name="shortname" type="text" value="<?php echo $fetch['ShortName']; ?>" maxlength="15"></b></td>
	<td>Max. 15 characters</td>
	</tr>

	<tr><td>School</td>
	<td>
		<select name="school" id="school" class="select">
			<?php echo $schools; ?>
        </select>
	</td>
	<td></td>
	</tr>

	<tr>
		<td>Maximum size of intake</td>
		<td><b><input name="maxintake" type="text" value="<?php echo $fetch['IntakeMax']; ?>" style="width:100px"> students</b></td>
		<td></td>
	</tr>

	<tr><td>Method of Delivery</td>
	<td>
		<select name="delivery"  class="select">

		<?php
		if ($fetch[4] == "0") {	 echo '<option value="0">-choose-</option> '; } else { 	echo '<option value="'.$fetch['Delivery'].'">'.$fetch['Delivery'].'</option>'; }

		echo '<option value="Distance">Distance learning</option>';
		echo '<option value="Block">Block Release</option>';
		echo '<option value="Parallel">Parallel programme</option>';
		echo '<option value="Fulltime">Fulltime</option>';
		?>

		</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Study Type</td>
	<td>
	<select name="studytype" class="select">
	
	<?php
		if ($fetch[9] == "0") {	 echo '<option value="0">-choose-</option> '; } else { 	echo '<option value="'.$fetch['StudyType'].'">'.$fetch['StudyType'].'</option>'; }
	?>
		<option value="Certificate">Certificate</option>
		<option value="Diploma" >Diploma</option>
		<option value="Undergraduate">Udergraduate study</option>
		<option value="Postgraduate">Postgraduate study</option>
		<option value="Doctorate">Doctorate</option>
	</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Currenty on offer</td>
	<td><select name="active" class="select">

		<?php
			if ($fetch['ProgrammesAvailable'] == "yes") {	 
				echo '<option value="yes" selected="" >Yes</option> <option value="no">No</option> ';	
			}  else {
 				 echo '<option value="no" selected="" >No</option> <option value="yes">Yes</option> ';	
			} 
		?>

	</select></td>
	<td></td>
	</tr>


	<tr><td>Start of Intake</td>
	<td><?php echo $fetch[2]; ?></td>
	<td></td>
	</tr>
	<tr><td>End of Intake</td>
	<td><?php echo $fetch[3]; ?></td>
	<td></td>
	</tr>


	<tr><td>Total duration of study</td>
	<td>
	<select name="duration" class="select">

	<?php	
		echo '<option value="'.$fetch[10].'" elected="">'.$fetch[10].' Year</option>';
	?>
		<option value="1">1 Year</option>
		<option value="2">2 Year</option>
		<option value="3">3 Year</option>
		<option value="4">4 Year</option>
		<option value="5">5 Year</option>
	</select>
	</td>
	<td></td>
	</tr>
	<tr>
	<td></td>
	<td>
		<!--<input type="submit" class="submit" name="submit" id="submit" value="Save changes to study" />-->
	</td>
	<td></td>
	</tr>

	</table>

</form>




<br /><br /> <h2>Manage courses in programme</h2><p>Please enter the following information</p>




<?php 
$i=1;

while($i <= $year){
	$s1 = $select->showCourseList($study, $i, 1);
	$s2 = $select->showCourseList($study, $i, 2);
	

	echo'<table width="700" border="0" cellpadding="5" cellspacing="0">
 	<tr>
		<td width="100" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  width="130" bgcolor="#EEEEEE"><strong>Semester 1</strong></td>
		<td  width="130" bgcolor="#EEEEEE"><strong>Semester 2</strong></td>
	</tr>
 	<tr >
	<td><h1>YEAR '.$i.'</h1>Select which courses should be part of this programme for each of the respective terms.</td>

	<td width="100"> 
	<form id="selected1" name="selectedfr" method="post" action="'. $this->core->conf['conf']['path'] . '/programmes/save/' . $this->core->item. '">
		<input type="hidden" name="item" value="'. $item .'" />
		<input type="hidden" name="year" value="'. $i .'" />
		<input type="hidden" name="semester" value="1" />
		<select name="selected[]" multiple="multiple" size="10" style="width: 130px">
			'. $s1 .'
		</select>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	<form id="nselected" name="nselectedfr" method="post" action="'. $this->core->conf['conf']['path'] . '/programmes/save/'. $this->core->item .'">
		<input type="hidden" name="item" value="'. $item .'" />
		<input type="hidden" name="year" value="'. $i .'" />
		<input type="hidden" name="semester" value="1" />
		<select type="select" name="nselected[]" style="width: 130px">
			'. $notselectedcourses .'
		</select>  
		<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" />
	</form>
	</td>

	<td width="100"> 
	<form id="selected2" name="selectedfr" method="post" action="'. $this->core->conf['conf']['path'] .'/programmes/save/'. $this->core->item .'">
		<input type="hidden" name="item" value="'. $item .'" />
		<input type="hidden" name="year" value="'. $i .'" />
		<input type="hidden" name="semester" value="2" />
		<select name="selected[]" multiple="multiple" size="10" style="width: 130px">
			'. $s2 .'
		</select>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	<form id="nselected" name="nselectedfr" method="post" action="'. $this->core->conf['conf']['path'] .'/programmes/save/'. $this->core->item .'">
		<input type="hidden" name="item" value="'. $item .'" />
		<input type="hidden" name="year" value="'. $i .'" />
		<input type="hidden" name="semester" value="2" />
		<select type="select" name="nselected[]" style="width: 130px">
			'. $notselectedcourses .'
		</select>  
		<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" />
	</form>
	</td>
	</table>';
	$i++;
}

?>

