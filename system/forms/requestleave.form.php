<html>
<title> LEAVE FORM</title>
<div class="leave_form" style="padding-left: 30px">
<form id="requestleave" name="requestleave" method = "post"action = "<?php echo $this->core->conf['conf']['path'] . "/staff/leaverequest/". $this->core->item; ?>" >



	<div class="label">StartDate:</div><input type="date" id="name" name="start"> <br/>
	
	<div class="label">EndDate:</div><input type="date" id="name" name="end"> <br/>
	
	<div class="label">Reason for leave:</div><input type="text" id="name" name="description"> <br/>
	<br/><input type = "submit" >
	
</form>
</div>
</html>