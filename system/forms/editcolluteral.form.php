<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addcolluteral" name="addcolluteral" enctype="multipart/form-data" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/loan/updatecolluteral/" .$item ; ?>">
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
					<td width="150"><b>Name (Description)</b></td>
					<td>
					  <input type="text" name="name" value="<?php echo $name; ?>"/>
					  <input type="hidden" name="ownerID" value="<?php echo $ownerID; ?>" />
					</td>
				  </tr>
				  <tr>
					<td width="150"><b>Short Name</b></td>
					<td>
					  <input type="text" name="code" value="<?php echo $code; ?>" />
					  
					</td>
				  </tr>
				  <tr>
					<td width="150"><b>Value (ZMW)</b></td>
					<td>
					  <input type="number" name="value" value="<?php echo $value; ?>"></td>
				  </tr>
				  <tr>
					<td width="150"><b>Inspection Date</b></td>
					<td>
					  <input type="date" name="inspectionDate" value="<?php echo $inspectionDate; ?>"></td>
				  </tr>
				  <tr>
					<td width="150"><b>Inspector Name</b></td>
					<td>
					  <input type="text" name="inspectorName" value="<?php echo $inspectorName; ?>"></td>
				  </tr>
				  <tr>
					<td width="150"><b>Comments</b></td>
					<td>
					  <textarea name='comments'><?php echo $comments; ?></textarea></td>
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