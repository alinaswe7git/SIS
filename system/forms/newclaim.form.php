<script>
function showItems(str) {
  if (str=="") {
    document.getElementById("txtHint").innerHTML="";
    return;
  }
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("txtItems").innerHTML=this.responseText;
    }
  }
  xmlhttp.open("GET","/claim/addclaimitem/"+str,true);
  xmlhttp.send();
}
</script>
<form id="newclaim" name="newclaim" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/claim/save/" . $this->core->item; ?>">
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
			<td>Course</td>
			<td><select name="course" id="course" style="width: 250px">
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
		<tr><td>Claim Items</td>
			<td><input name="sessions" type="number" value="" maxlength="1" onkeyup="showItems(this.value)" ></td>	
			<td>Max number. 3  <b>Note: </b>do not change this number after you start entering item informaion.</td>
		</tr>
		<tr><td></td>
			<td><div id="txtItems"><b>Item entries will be listed here</b></div></td>	
			<td></td>
		</tr>
	</table>
	<input type="hidden" value="<?php  echo $name; ?>"/>
	<input type="hidden" name="claimtype" value="Assesment"/>
	
	</p><input type="submit" class="submit" name="submit" id="submit" value="<?php echo $button; ?>"/>
	<?php echo $submit; ?>
</form>
