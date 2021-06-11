<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addloan" name="addloan" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/loan/save/" . $this->core->item; ?>">
	<p>Please enter the following information from (<?php echo $item; ?>)</p>
        <table cellspacing="0" >
          <tr>
            <td>
				<table width="768" border="0" cellpadding="5" cellspacing="0">
				  <tr>
					<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
					<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
				  </tr>
				  <tr>
					<td width="150"><b>Name (Loan Description)</b></td>
					<td>
					  <input type="text" name="name" value="" />
					  <input type="hidden" name="ownerID" value="<?php echo $item; ?>" />
					</td>
				  </tr>
				  <tr>
					<td width="150"><b>Collateral</b></td>
					<td>
					  <select name="collateralID" id="collateralID">
						<?php echo $collateral; ?>
						<!--<option value="0">-choose-</option>
						<option value="1">Car (K30000)</option>
						<option value="2">House (K250000)</option>-->
					  </select>
					</td>
				  </tr>
				  <tr>
					<td width="150"><b>Amount</b></td>
					<td>
					  <input type="number" name="amount" step="0.01"></td>
				  </tr>
				  <tr>
					<td width="150"><b>Rate (%)</b></td>
					<td>
					  <input type="number" name="rate" step="0.01"></td>
				  </tr>
				  <tr>
					<td width="150"><b>Start Date</b></td>
					<td>
					  <input type="date" name="startDate" ></td>
				  </tr>
				  <tr>
					<td width="150"><b>End Date</b></td>
					<td>
					  <input type="date" name="endDate" ></td>
				  </tr>

				</table>
		</td>
          </tr>
        </table>
	<br />
	  <input type="hidden" name="item" value="" />
	  <input type="submit" class="submit" name="submit" id="submit" value="Save" />
        <p>&nbsp;</p>

      </form>