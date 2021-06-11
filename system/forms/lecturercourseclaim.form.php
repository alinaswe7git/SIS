

<table width="100%" border="0" cellpadding="5" cellspacing="0">
	<tr>
		<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
		<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
		<td bgcolor="#EEEEEE"><strong>Description</strong></td>
	</tr>
	<tr><td>Number of Courses</td>
		<td><input name="sessions" type="number" value="" maxlength="1" onkeyup="showItems(this.value)" ></td>	
		<td>Max number. 8  <b>Note: </b>do not change this number after you start entering the informaion.</td>
	</tr>
	<tr><td></td>
		<td><div id="txtItems"><b>Course entries will be listed here</b></div></td>	
		<td></td>
	</tr>
</table>
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
  xmlhttp.open("GET","/claim/addlecturecourse/"+str,true);
  xmlhttp.send();
}
</script>
	
