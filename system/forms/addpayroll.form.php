<div class="heading"><?php echo ("Add payslip"); ?></div>
<form id="idsearch" name="payrolladd" method="post" action="<?php echo $this->core->conf['conf']['path']; ?>/payroll/save">

	<div class="label"><?php echo ("Leave Days"); ?>:</div>
	<select name="LeaveDays" style="width: 260px">
		<option value="0" selected>--None--</option>
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
	</select><br />
	<div class="label"><?php echo "Employee ID"; ?>:</div>
	<select name="OwnerID" id="OwnerID" onchange="showBank(this.value)" required style="width: 260px">
		<?php echo $employees; ?>
	</select><br />
	<div id="txtHintBank" ></div></br>
	
	<div class="label"><?php echo "Basic Pay"; ?>:</div>
	<input type="number" name="Basicpay" id="Basicpay" class="submit" required style="width: 260px" /><br />
	
	<div class="label"><?php echo "Month"; ?>:</div>
	<select name ='Month'id='Month'required >
    <option selected value='Janaury'>Janaury</option>
    <option value='February'>February</option>
    <option value='March'>March</option>
    <option value='April'>April</option>
    <option value='May'>May</option>
    <option value='June'>June</option>
    <option value='July'>July</option>
    <option value='August'>August</option>
    <option value='September'>September</option>
    <option value='October'>October</option>
    <option value='November'>November</option>
    <option value='December'>December</option>
    </select><br />
	<div class="label"><?php echo "Year"; ?>:</div>
	<input type="number" name="Year" id="Year" class="submit" value="<?php echo date("Y");?>" style="width: 260px" /><br />
	
	<div class="label"><?php echo "Position"; ?>:</div>
	<input list="positions" name="Position" id="Position">
	<datalist id="positions">
	  <?php echo $positions; ?>
	</datalist><br />
	
	<div class="label"><?php echo $this->core->translate("Number payroll entries"); ?>:</div>
	<select name="Entries" onchange="showEntries(this.value)" style="width: 260px">
		<option value="1" selected>1</option>
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
	</select><br />
	<div id="txtHint" ></div></br>

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Submit"); ?>" style="width: 260px"/>
	<br>


</form>

<script>

function showEntries(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","<?php echo $this->core->conf['conf']['path'];?>/api/payrollitems/"+str,true);
        xmlhttp.send();
    }
}
function showBank(str) {
    if (str == "") {
        document.getElementById("txtHintBank").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHintBank").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","<?php echo $this->core->conf['conf']['path'];?>/api/payrollbank/"+str,true);
        xmlhttp.send();
    }
}
</script>