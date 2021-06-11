<div class="heading"><?php echo ("edit bank information"); ?></div>
<form id="idsearch" name="payrolladd" method="post" action="<?php echo $this->core->conf['conf']['path']; ?>/payroll/savebank">

	<div class="label"><?php echo "Bank name"; ?>:</div>
	<input type="text" name="BankName" id="BankName" class="submit" value = "<?php echo $BankName; ?>" style="width: 260px" /><br />
	
	<div class="label"><?php echo "Bank branch"; ?>:</div>
	<input type="text" name="Branch" id="Branch" class="submit" value = "<?php echo $Branch; ?>" style="width: 260px" /><br />
	
	<div class="label"><?php echo "Account number"; ?>:</div>
	<input type="text" name="AccountNumber" id="AccountNumber" class="submit" value = "<?php echo $AccountNumber; ?>" style="width: 260px" /><br />
	
	<div class="label"><?php echo "Social securuty number"; ?>:</div>
	<input type="text" name="SSS" id="SSS" class="submit" value = "<?php echo $SSS; ?>" style="width: 260px" /><br />
	
	<div class="label"><?php echo "Tpin number"; ?>:</div>
	<input type="text" name="Tpin" id="Tpin" class="submit" value = "<?php echo $Tpin; ?>" style="width: 260px" /><br />
	
	<input type="hidden" name="BankID" value = "<?php echo $item; ?>" />
	<input type="hidden" name="OwnerID" value = "<?php echo $OwnerID; ?>" />
	<input type="hidden" name="saveType" value = "edit" />

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Submit"); ?>" style="width: 260px"/>
	<br>


</form>