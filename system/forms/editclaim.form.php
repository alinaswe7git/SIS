<form id="newclaim" name="newclaim" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/claim/itemupdate/" . $this->core->item; ?>">
<input type="hidden" name="claimid" id="claimid" value="<?php echo $id; ?>"/>
	<p>This form creates a new claim request
	</p>
	<p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
			<td bgcolor="#EEEEEE"><strong>Description</strong></td>
		</tr>
		<tr>
			<td>Category</td>
			<td><select name="category" id="category">
					<?php echo $claims; ?>
				</select></td>
			<td></td>
		</tr>
		<tr>
			<td>Course</td>
			<td><select name="course" id="course">
					<?php echo $courses; ?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Number Of students</td>
			<td><input name="students" type="number" value="" maxlength="6"></td>
			<td>Max. 6 characters</td>
		</tr>
		<tr>
			<td>School</td>
			<td><select name="school" id="school">
					<?php echo $schools; ?>
				</select></td>
			<td></td>
		</tr>
		
		<tr><td>Method of Delivery</td>
			<td>
				<select name="delivery" class="delivery">
					<option value="Fulltime">Fulltime</option>
					<option value="Distance" >Distance</option>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Period</td>
			<td><select name="period" id="period">
					<?php echo $periods; ?>
				</select></td>
			<td></td>
		</tr>
	</table>
	<input type="hidden" value="<?php  echo $name; ?>"

	
	</p><input type="submit" class="submit" name="submit" id="submit" value="<?php echo $button; ?>"/>
	<?php echo $submit; ?>
</form>
