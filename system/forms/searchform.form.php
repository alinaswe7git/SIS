<div class="heading"><?php echo $this->core->translate("Search by student number"); ?></div>
<form id="idsearch" name="idsearch" method="get" action="">
	<div class="label"><?php echo $this->core->translate("Enter student number"); ?>:</div>
	<input type="text" name="uid" id="student-id" class="submit" style="width: 125px"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Open Record"); ?>"/>
</form>

<div style="position: absolute; top: -100px; "> <div class="heading"><?php echo $this->core->translate("Search by EduCard"); ?></div>
<form id="idsearch" name="idsearch" method="get" action="">
	<div class="label"><?php echo $this->core->translate("Scan card"); ?>:</div>
	<input type="text" name="card" id="card" style="width: 250px" class="submit"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Open Record"); ?>"/>
</form></div>


 <script type="text/javascript">
 document.getElementById("card").focus();
 </script>

<!--
<div class="heading"><?php //echo $this->core->translate("Search by intake"); ?></div>
<form id="idsearch" name="yearsearch" method="get" action="">
	<div class="label"><?php //echo $this->core->translate("Select year of intake"); ?>:</div>
	<select name="year" class="submit">
		<option value="all">- ALL -</option>
		<option value="2021">2021</option>
		<option value="2020">2020</option>
		<option value="2019">2019</option>
		<option value="2018">2018</option>
		<option value="2017">2017</option>
		<option value="2016">2016</option>
		<option value="2015">2015</option>
		<option value="2014">2014</option>
		<option value="2013">2013</option>
		<option value="2012">2012</option>
		<option value="2011">2011</option>
		<option value="2010">2010</option>
		<option value="2009">2009</option>
		<option value="2008">2008</option>
	</select>
	<select name="mode" class="submit" style="width: 165px">
		<option value="%" selected>Select</option>
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
	</select>
	<select name="status" class="submit" style="width: 105px">
		<option value="%" selected>Status</option>
		<option value="Approved">Approved</option>
		<option value="Employed">Employed</option>
		<option value="Retired">Retired</option>
		<option value="Fired">Fired</option>
		<option value="Suspended">Suspended</option>
		<option value="Deceased">Deceased</option>
		<option value="Requesting">Requesting</option>
		<option value="Enrolled">Enrolled</option>
		<option value="Graduated">Graduated</option>
		<option value="Dismissed">Dismissed</option>
		<option value="Rejected">Rejected</option>
		<option value="Failed">Failed</option>
		<option value="New">New</option>
		<option value="Expelled">Expelled</option>
	</select>
	<input type="submit" class="submit" value="<?php //echo $this->core->translate("Open Records"); ?>"/>
</form>
-->

<?php
if($this->core->role == 1027 ){

?>

<form id="namesearch" name="namesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("Search by Name"); ?></div>

	<div class="padding">
		<div class="label"><?php echo $this->core->translate("Enter students first name"); ?>:</div>
		<input type="text" name="studentfirstname" id="studentfirstname" style="width: 250px" class="submit"/><br>
	</div>
	<div class="padding">
		<div class="label"> <?php echo $this->core->translate("and/or surname"); ?>:</div>
		<input type="text" name="studentlastname" id="studentlastname" style="width: 250px" class="submit"/>
	</div>
	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit"  style="width: 250px">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select> <input type="submit" class="submit" value="<?php echo $this->core->translate("Search Records"); ?>"/>
	</select>
</form>

<form id="studysearch" name="studysearch" method="get" action="">
	<div class="heading"><?php echo "View students course register by study"; ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="study">
	<select name="q" id="program" class="submit" width="250" style="width: 250px">
		<?php echo $study; ?>
	</select><br/>
	<div class="label"><?php echo $this->core->translate("Filter by"); ?>:</div>
	<select name="mode" class="submit">
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
		<option value="Dismissed">Dismissed</option>
	</select>
	<select name="year" class="submit" style="width: 129px;">
		<option value="1">1st Year</option>
		<option value="2">2nd Year</option>
		<option value="3">3rd Year</option>
		<option value="4">4th Year</option>
		<option value="5">5th Year</option>
		<option value="6">6th Year</option>
		<option value="%">ALL</option>
	</select>
	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View Records"); ?>"/>
</form>

<?php }else { ?>

<form id="namesearch" name="namesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("Search by Name"); ?></div>

	<div class="padding">
		<div class="label"><?php echo $this->core->translate("Enter students first name"); ?>:</div>
		<input type="text" name="studentfirstname" id="studentfirstname" style="width: 250px" class="submit"/><br>
	</div>
	<div class="padding">
		<div class="label"> <?php echo $this->core->translate("and/or surname"); ?>:</div>
		<input type="text" name="studentlastname" id="studentlastname" style="width: 250px" class="submit"/>
	</div>
	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit"  style="width: 250px">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select> <input type="submit" class="submit" value="<?php echo $this->core->translate("Search Records"); ?>"/>
	</select>
</form>

<form id="studysearch" name="studysearch" method="get" action="">
	<div class="heading"><?php echo "View students course register by study"; ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="study">
	<select name="q" id="program" class="submit" width="250" style="width: 250px">
		<?php echo $study; ?>
	</select><br/>
	<div class="label"><?php echo $this->core->translate("Filter by"); ?>:</div>
	<select name="mode" class="submit">
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
		<option value="Dismissed">Dismissed</option>
	</select>
	<select name="year" class="submit" style="width: 129px;">
		<option value="1">1st Year</option>
		<option value="2">2nd Year</option>
		<option value="3">3rd Year</option>
		<option value="4">4th Year</option>
		<option value="5">5th Year</option>
		<option value="6">6th Year</option>
		<option value="%">ALL</option>
	</select>
	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View Records"); ?>"/>
</form>

<form id="excludesearch" name="excludesearch" method="get" action="">
	<div class="heading"><?php echo "View students by status by Period"; ?></div>
	
	<input type="hidden" name="search" value="exclude">
	<div class="label"><?php echo $this->core->translate("Filter by"); ?>:</div>
	<select name="mode" class="submit">
		<option value="%">All</option>
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
	</select>
	<select name="period" id="period" class="submit" width="230" style="width: 230px">
		<?php echo $periods; ?>
	</select>
	<select name="status" class="submit" style="width: 105px">
		<option value="%" selected>Status</option>
		<option value="all">- ALL -</option>
		<option value="Approved">Approved</option>
		<option value="Employed">Employed</option>
		<option value="Retired">Retired</option>
		<option value="Fired">Fired</option>
		<option value="Suspended">Suspended</option>
		<option value="Deceased">Deceased</option>
		<option value="Requesting">Requesting</option>
		<option value="Enrolled">Enrolled</option>
		<option value="Graduated">Graduated</option>
		<option value="Dismissed">Dismissed</option>
		<option value="Rejected">Rejected</option>
		<option value="Failed">Failed</option>
		<option value="New">New</option>
		<option value="Expelled">Expelled</option>
		<option value="Exclude">Exclude</option>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View Records"); ?>"/>
</form>

<form id="programmesearch" name="programmesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by study"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="programme">
	<select name="q" id="program" class="submit" width="250" style="width: 250px">
		<?php echo $study; ?>
	</select><br/>
	<div class="label"><?php echo $this->core->translate("Filter by"); ?>:</div>
	<select name="mode" class="submit">
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
		<option value="Dismissed">Dismissed</option>
	</select>
	<select name="year" class="submit" style="width: 129px;">
		<option value="1">1st Year</option>
		<option value="2">2nd Year</option>
		<option value="3">3rd Year</option>
		<option value="4">4th Year</option>
		<option value="5">5th Year</option>
		<option value="6">6th Year</option>
		<option value="%">ALL</option>
	</select>
	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View records"); ?>"/>
</form>

<form id="centersearch" name="centersearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by Exam Center"); ?></div>
	<div class="label"><?php echo $this->core->translate("Exam centre"); ?>:</div>
			<select name="examcenter" id="examcenter" class="submit" width="250" style="width: 250px">
				<?php echo $centres; ?>
			</select> <br/>
	<div class="label"><?php echo $this->core->translate("Filter by"); ?>:</div>
	<select name="mode" class="submit">
		<option value="Block">Block release</option>
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
	</select>
	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
	<input type="hidden" name="search" value="center">
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View records"); ?>"/>
</form>

<form id="coursesearch" name="coursesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by course"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="course">
	<select name="q" id="course" class="submit" width="250" style="width: 250px">
		<?php echo $courses; ?>
	</select>
	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
	<div class="label"><?php echo ("Mode of Study"); ?>:</div>
	<select name="mode" class="submit">
		<option value="">All</option>
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Partime">Part-time</option>
		<option value="Dismissed">Dismissed</option>
	</select>
	</br>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View records"); ?>"/>
</form>


<form id="rolesearch" name="rolesearch" method="get" action="">
<input type="hidden" name="search" value="true">
	<div class="heading"><?php echo $this->core->translate("View users by role"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all users who are"); ?>:</div>
	<input type="hidden" name="search" value="role">
	<select name="role" id="role" class="submit" width="250" style="width: 250px">
		<?php echo $roles; ?>
	</select> 
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View Records"); ?>"/>
</form>
<?php }?>

