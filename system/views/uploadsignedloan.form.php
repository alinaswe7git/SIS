<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="uploadsigned" name="uploadsigned" enctype="multipart/form-data" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/loan/saveupload/" . $this->core->item; ?>">
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
					<td width="150"><b>Document</b></td>
					<td>
					  <input type="file" name="file" accept=".pdf,*.doc,*.docx"></td>
				  </tr>
				  
				</table>
		</td>
          </tr>
        </table>
	<br />
	  <input type="hidden" name="item" value="" />
	  <input type="hidden" name="ownerID" value="<?php echo $ownerID; ?>" />
	  <input type="hidden" name="id" value="<?php echo $item; ?>" />
	  <input type="submit" class="submit" name="submit" id="submit" value="Save" />
        <p>&nbsp;</p>

      </form>