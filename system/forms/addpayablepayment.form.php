<div class="heading"><?php echo ("Add payable information"); ?></div>
<form id="idsearch" name="payrolladd" method="post" action="<?php echo $this->core->conf['conf']['path']; ?>/payments/savepayable">

	<div class="label"><?php echo "Name"; ?>:</div>
	<input type="text" name="Name" id="Name" class="submit" style="width: 260px" required /><br />
	
	<div class="label"><?php echo "Type"; ?>:</div>
	
	<select name="Type" id="Type" required style="width: 260px">
		<option value="">- Select type -</option>
		<option value="Asset">Asset</option>
		<option value="Expandable">Expandable</option>
	</select><br />
	
	<div class="label"><?php echo "Description"; ?>:</div>
	<textarea name="Description" class="submit" style="width: 260px" ><?php echo $data; ?></textarea><br />
	
	<div class="label"><?php echo "Amount"; ?>:</div>
	<input type="number" name="Amount" id="Amount" class="submit" style="width: 260px" required /><br />
	
	<div class="label"><?php echo "Payment type"; ?>:</div>
	<select name="PaymentType" id="PaymentType" required style="width: 260px">
		<option value="">- Select payment type -</option>
		<option value="Bank Transfer">Bank Transfer</option>
		<option value="Cash">Cash</option>
		<option value="Cheque">Cheque</option>
	</select><br /><br />
	
	<div class="label"><?php echo "Transaction Date"; ?>:</div>
	<input type="date" name="Tdate" id="Tdate" class="submit" style="width: 260px" required /><br />
	
	<input type="hidden" name="OwnerID" id="OwnerID" value = "<?php echo $item; ?>" />
	<input type="hidden" name="userid" id="userid" value = "<?php echo $userid; ?>" />
	<input type="hidden" name="saveType" value = "add" />

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Submit"); ?>" style="width: 260px"/>
	<br>


</form>

