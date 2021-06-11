 <?php
class payroll {

	public $core;
	public $view;
	
	public function buildView($core) {
		$this->core = $core;
	}

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	private function viewMenu(){
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payroll/add?date='.$today.'">Add payslip</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payroll/run?date='.$today.'">Run payroll</a>'.
		//'<a href="' . $this->core->conf['conf']['path'] . '/payroll/print?date='.$today.'">Print list</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payroll/month">Monthly totals</a></div>';
		
	}


	public function managePayroll($item=NULL, $linked = TRUE)  {
		$today = date("Y-m-d");
		$currentMonth=date("F");
		$currentYear=date("Y");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);

		$sql = "SELECT *,
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Deduction') as 'Deduction',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Income') as 'Income',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%NAPSA%') as 'NAPSA',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%PAYE%') as 'PAYE'
				FROM `payroll-information` a 
				WHERE `Month`='$currentMonth' AND `Year`='$currentYear'";

		$run = $this->core->database->doSelectQuery($sql);

		if($this->core->role > 10){
			$this->viewMenu();
		}

		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td ><b>Pay Slip #</b></td>' .
		'<td ><b>Name</b></td>' .
		'<td ><b>Basic Pay</b></td>' .
		'<td ><b>Incomes</b></td>' .
		'<td ><b>Deductions</b></td>' .
		'<td ><b>Net Pay</b></td>'.
		'<td><b>Management</b></td>';
		
		echo '</tr>';
		$totalincome=0;
		$totaldeduction=0;
		$totalnet=0;
		$totalnapsa=0;
		$totalpaye=0;
		$output = "";
		$color = 'style="color: #FF0000;"';
		while ($fetch = $run->fetch_assoc()) {
			
			$income=$fetch['Income'];
			$deduction=$fetch['Deduction'];
			$netpay=$income-$deduction;
			
			$output .= '<tr>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payroll/view/' . $fetch['ID'] . '"> ' . $fetch['Month'] .'-'.$fetch['Year'].'-'.$fetch['ID']. '</a></b></td>
			<td>' . $fetch['Name'] . '</td>
			<td>' . $fetch['Basicpay'] . ' '.$this->core->conf['conf']['currency'].'</td>
			<td><b>' . $income . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $deduction . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $netpay . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/' . $fetch['ID'] . '"> Print</a></td>';
		
		$totalincome+=$income;
		$totaldeduction+=$deduction;
		$totalnet+=$netpay;
		$totalnapsa+=$fetch['NAPSA'];
		$totalpaye+=$fetch['PAYE'];
		}

	
		echo '<div class="successpopup">A total of '.$run->num_rows . " payroll for the selected for: $currentMonth $currentYear </div> 
		<div class=\"warningpopup\"> Total Incomes: $totalincome ".$this->core->conf['conf']['currency']." 
		Deductions: $totaldeduction".$this->core->conf['conf']['currency']."  
		Net Pays:$totalnet ".$this->core->conf['conf']['currency']." 
		 NAPSA :$totalnapsa ".$this->core->conf['conf']['currency']." 
		 PAYE :$totalpaye ".$this->core->conf['conf']['currency']."</div>";
		echo $output;
		echo '</table>';
	}
	
	public function monthPayroll($item=NULL, $linked = TRUE)  {
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$months = $select->showPayrollMonths();
		$years = $select->showPayrollYears();

		$month = $this->core->cleanGet['Month'];
		$year = $this->core->cleanGet['Year'];
		
		if($this->core->role > 10){
			$this->viewMenu();
		}
		
		echo'<form id="narrow" name="narrow" method="get" action="">
				<div class="toolbaritem">Filter to show pay slips from: 
					<select name="Month" class="submit" style="width: 230px; margin-top: -17px;">
					<option value=""> -- SELECT A Month -- </option>
					<option value="all"> -- All Months -- </option>
					'. $months .'
					</select>


					<select name="Year" id="Year" class="submit" style="width: 150px; margin-top: -17px;" >
						<option value=""> -- YEAR -- </option>
						<option value="all"> -- All Years -- </option>
						'. $years .'
					</select>
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/>
			   </div>
			
		</form> <br> <hr>';
		
		if($month == '' || $year == ''){
			echo'<div class="warningpopup">SELECT THE MONTH AND YEAR FOR WHICH YOU WISH TO GENERATE PAYSLIPS</div>';
			return;
		}else{
			if($month == 'all'){
				$month = '%';
			}
			if($year == 'all'){
				$year = '%';
			}
		}
		
		$today = date("Y-m-d");
		$currentMonth=date("F");
		$currentYear=date("Y");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}
		

		$date = new DateTime($today);

		$sql = "SELECT *,
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Deduction') as 'Deduction',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Income') as 'Income',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%NAPSA%') as 'NAPSA',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%PAYE%') as 'PAYE'
				FROM `payroll-information` a 
				WHERE `Month` LIKE '$month' AND `Year` LIKE '$year'";
				
		$run = $this->core->database->doSelectQuery($sql);

		

		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td ><b>Pay Slip #</b></td>' .
		'<td ><b>Name</b></td>' .
		'<td ><b>Basic Pay</b></td>' .
		'<td ><b>Incomes</b></td>' .
		'<td ><b>Deductions</b></td>' .
		'<td ><b>Net Pay</b></td>'.
		'<td><b>Management</b></td>';
		
		echo '</tr>';
		$totalincome=0;
		$totaldeduction=0;
		$totalnet=0;
		$totalnapsa=0;
		$totalpaye=0;
		$output = "";
		$color = 'style="color: #FF0000;"';
		while ($fetch = $run->fetch_assoc()) {
			
			$income=$fetch['Income'];
			$deduction=$fetch['Deduction'];
			$netpay=$income-$deduction;
			
			$output .= '<tr>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payroll/view/' . $fetch['ID'] . '"> ' . $fetch['Month'] .'-'.$fetch['Year'].'-'.$fetch['ID']. '</a></b></td>
			<td>' . $fetch['Name'] . '</td>
			<td>' . $fetch['Basicpay'] . ' '.$this->core->conf['conf']['currency'].'</td>
			<td><b>' . $income . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $deduction . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $netpay . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/' . $fetch['ID'] . '"> Print</a></td>';
		
		$totalincome+=$income;
		$totaldeduction+=$deduction;
		$totalnet+=$netpay;
		$totalnapsa+=$fetch['NAPSA'];
		$totalpaye+=$fetch['PAYE'];
		}

	
		echo '<div class="successpopup">A total of '.$run->num_rows . " payroll for the selected for: $month $year </div> 
		<div class=\"warningpopup\"> Total Incomes: $totalincome ".$this->core->conf['conf']['currency']." 
		Deductions: $totaldeduction".$this->core->conf['conf']['currency']."  
		Net Pays:$totalnet ".$this->core->conf['conf']['currency']."
		NAPSA :$totalnapsa ".$this->core->conf['conf']['currency']." 
		 PAYE :$totalpaye ".$this->core->conf['conf']['currency']."</div>";
		echo $output;
		echo '</table>';
	}
	
	public function runPayroll($item=NULL, $linked = TRUE)  {
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$months = $select->showPayrollMonths();
		$years = $select->showPayrollYears();

		$month = $this->core->cleanGet['Month'];
		$year = $this->core->cleanGet['Year'];
		
		if($this->core->role > 10){
			$this->viewMenu();
		}
		
		echo'<form id="narrow" name="narrow" method="get" action="">
				<div class="toolbaritem">Filter to show pay slips from: 
					<select name="Month" class="submit" style="width: 230px; margin-top: -17px;">
					<option value=""> -- SELECT A Month -- </option>
					<option value="Janaury">Janaury</option>
					<option value="February">February</option>
					<option value="March">March</option>
					<option value="April">April</option>
					<option value="May">May</option>
					<option value="June">June</option>
					<option value="July">July</option>
					<option value="August">August</option>
					<option value="September">September</option>
					<option value="October">October</option>
					<option value="November">November</option>
					<option value="December">December</option>
					</select>

					<input type="number" name="Year" id="Year" class="submit" value="'. date("Y").'" style="width: 150px; margin-top: -17px;" />
					<input type="submit" value="Run"  style="width: 80px; margin-top: -15px;"/>
			   </div>
			
		</form> <br> <hr>';
		
		if($month == '' || $year == ''){
			echo'<div class="warningpopup">SELECT THE MONTH AND YEAR FOR WHICH YOU WISH TO GENERATE PAYSLIPS</div>';
			return;
		}else {
			$sqlemp = "SELECT * FROM `basic-information` WHERE `Status`='Employed' 
						AND ID IN (SELECT OwnerID FROM `payroll-information`)";
					
			$runemp = $this->core->database->doSelectQuery($sqlemp);
			while ($fetchemp = $runemp->fetch_assoc()) {
				$sqlsel = "SELECT * FROM `payroll-information` 
					       WHERE OwnerID ='".$fetchemp['ID']."' ORDER BY ID DESC LIMIT 1";
						
				$runsel= $this->core->database->doSelectQuery($sqlsel);
				while ($fetchsel = $runsel->fetch_assoc()) {
					
					$id = $fetchsel['ID'];
					$LeaveDays = $fetchsel['LeaveDays'];
					$Basicpay = $fetchsel['Basicpay'];
					$OwnerID = $fetchsel['OwnerID'];
					$BankID = $fetchsel['BankID'];
					$Position = $fetchsel['Position'];
					$uid = $this->core->userID;
					
					
					$sqladd = "INSERT INTO `loan_pro`.`payroll-information` (`LeaveDays`, `Basicpay`, `OwnerID`, `DateTime`, `Month`, `Year`, `BankID`, `Position`, `CreaterID`) 
					VALUES ('$LeaveDays', '$Basicpay', '$OwnerID', NOW(), '$month', '$year', '$BankID', '$Position', '$uid')";
						
					$runadd = $this->core->database->doSelectQuery($sqladd );
					
					if($runadd){
						$PayID = $this->core->database->id();
						
						$sqlitem = "INSERT INTO `loan_pro`.`payroll-items` (`PayID`, `ItemName`, `Amount`, `Type`)
						SELECT '$PayID', `ItemName`, `Amount`, `Type` FROM `payroll-items` WHERE PayID =".$id;
												
						$runsqlitem = $this->core->database->doInsertQuery($sqlitem );
						
					}
				}
				
			}
			
		}
		
		$today = date("Y-m-d");
		$currentMonth=date("F");
		$currentYear=date("Y");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}
		

		$date = new DateTime($today);

		$sql = "SELECT *,
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Deduction') as 'Deduction',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Income') as 'Income',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%NAPSA%') as 'NAPSA',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%PAYE%') as 'PAYE'
				FROM `payroll-information` a 
				WHERE `Month` LIKE '$month' AND `Year` LIKE '$year'";
				
		$run = $this->core->database->doSelectQuery($sql);

		

		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td ><b>Pay Slip #</b></td>' .
		'<td ><b>Name</b></td>' .
		'<td ><b>Basic Pay</b></td>' .
		'<td ><b>Incomes</b></td>' .
		'<td ><b>Deductions</b></td>' .
		'<td ><b>Net Pay</b></td>'.
		'<td><b>Management</b></td>';
		
		echo '</tr>';
		$totalincome=0;
		$totaldeduction=0;
		$totalnet=0;
		$totalnapsa=0;
		$totalpaye=0;
		$output = "";
		$color = 'style="color: #FF0000;"';
		while ($fetch = $run->fetch_assoc()) {
			
			$income=$fetch['Income'];
			$deduction=$fetch['Deduction'];
			$netpay=$income-$deduction;
			
			$output .= '<tr>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payroll/view/' . $fetch['ID'] . '"> ' . $fetch['Month'] .'-'.$fetch['Year'].'-'.$fetch['ID']. '</a></b></td>
			<td>' . $fetch['Name'] . '</td>
			<td>' . $fetch['Basicpay'] . ' '.$this->core->conf['conf']['currency'].'</td>
			<td><b>' . $income . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $deduction . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $netpay . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/' . $fetch['ID'] . '"> Print</a></td>';
		
		$totalincome+=$income;
		$totaldeduction+=$deduction;
		$totalnet+=$netpay;
		$totalnapsa+=$fetch['NAPSA'];
		$totalpaye+=$fetch['PAYE'];
		}

	
		echo '<div class="successpopup">A total of '.$run->num_rows . " payroll have just been run for: $month $year </div> 
		<div class=\"warningpopup\"> Total Incomes: $totalincome ".$this->core->conf['conf']['currency']." 
		Deductions: $totaldeduction".$this->core->conf['conf']['currency']."  
		Net Pays:$totalnet ".$this->core->conf['conf']['currency']."
		 NAPSA :$totalnapsa ".$this->core->conf['conf']['currency']." 
		 PAYE :$totalpaye ".$this->core->conf['conf']['currency']."</div>";
		echo $output;
		echo '</table>';
	}

	public function viewPayroll($item){
		
		$sql = "SELECT a.ID,a.LeaveDays,a.Basicpay,a.OwnerID,a.DateTime,a.Month,a.Year,a.BankID
				,b.BankName,b.Branch,b.AccountNumber,b.SSS,
				(SELECT GovernmentID FROM `basic-information` WHERE ID=a.OwnerID) as 'GID',
				a.Position as 'Pos',
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(LeaveDays) FROM `payroll-information` WHERE OwnerID=a.OwnerID) as 'LeaveDaysTotal',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE 
				PayID IN (SELECT ID FROM `payroll-information` WHERE OwnerID=a.OwnerID) AND ItemName LIKE '%PAYE%') as 'PAYETotal'
				FROM `payroll-information` a,`payroll-bank-information` b
				WHERE a.BankID=b.ID AND a.ID='$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();
			
		$today =  date("Y-m-d");
		$admin = $this->core->userID;
		$owner = $this->core->userID;
		$payslipID =  $fetch["ID"];
		$basicpay =  $fetch["Basicpay"];
		$name =  $fetch["Name"];
		$GID =  $fetch["GID"];
		$uid =  $fetch["OwnerID"];
		$leaveDays =  $fetch["LeaveDays"];
		$month =  $fetch["Month"];
		$year =  $fetch["Year"];
		$bankID =  $fetch["BankID"];
		$bankName =  $fetch["BankName"];
		$branch =  $fetch["Branch"];
		$accountNumber =  $fetch["AccountNumber"];
		$sss =  $fetch["SSS"];
		$pos =  $fetch["Pos"];
		$taxTD =  $fetch["PAYETotal"];
		$totalLeave =  $fetch["LeaveDaysTotal"];
		
		$sqlx = "SELECT * FROM `payroll-items` WHERE PayID = '$payslipID' ORDER BY Type";
		$runx = $this->core->database->doSelectQuery($sqlx);
			
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/'.$item.'">PRINT PAY SLIP</a>';
		
		if($this->core->role == 102 || $this->core->role == 1000){
			echo '<a href="' . $this->core->conf['conf']['path'] . '/payroll/edit/'.$item.'">Edit</a>';
		}
		
		echo '</div>';
		
		//echo '<div  style=" align: center;>';

		echo '<center><img width="300px" src="'.$this->core->conf['conf']['path'].'/datastore/logo/speedcap.png">';
			
		echo '<h2>Pay Slip </h2></center><br><table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr class="heading">
                    <td ><strong>EMPLOYEE: </strong>'.$name.'</td><td><strong>PAY PERIOD:</strong> '.$month.' '.$year.'</td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>ID NUMBER: </strong>'.$GID.'</td><td><strong>JOB TITLE: </strong>'.$pos.'</td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>BANK DETAILS: </strong>'.$bankName.' '.$branch.'</td><td><strong>Acc # </strong>'.$accountNumber.' </td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>SOCIAL SECURITY NUMBER: </strong>'.$sss.' </td><td><strong>CURRENCY:  </strong>ZMW</td>
                  </tr> 
				  <tr class="heading">
                    <td ><strong>TAX Year to-Date: </strong>'.number_format($taxTD,2).' '.$this->core->conf['conf']['currency'].'</td><td><strong>Leave Days:  </strong>'.$totalLeave.'</td>
                  </tr>
                  </table>';
		
		echo '<center><table width="468" border="1" cellpadding="5" cellspacing="0"><tr>';
		
		echo '<th>INCOMES</th><th>DEDUCTIONS</th></tr>';
		$tincome=0;
		$tdeduction=0;
		$incomePrint="";
		$deductionPrint="";
		echo '<tr>';
		while ($fetchx = $runx->fetch_assoc()) {
								
				if($fetchx['Type']=="Income"){
					$incomePrint.='<strong>'.$fetchx['ItemName'].'</strong> ' . number_format($fetchx["Amount"],2). '</br>';
					$tincome+=$fetchx["Amount"];
				}else{
					$deductionPrint.='<strong>'.$fetchx['ItemName'].'</strong> ' . number_format($fetchx["Amount"],2). '</br>';
					$tdeduction+=$fetchx["Amount"];
				}                    
                  
		}
		echo "<td>$incomePrint</td><td>$deductionPrint</td>";
		$tnetpay=$tincome-$tdeduction;
		echo '</tr><tr><th>Total :'.number_format($tincome,2).' '.$this->core->conf['conf']['currency'].'</th><th>Total :'.number_format($tdeduction,2).' '.$this->core->conf['conf']['currency'].'</th></tr>';		
		echo '<tr><th>Net Pay</th><th>'.number_format($tnetpay,2).' '.$this->core->conf['conf']['currency'].'</th></tr>';		
		echo ' </table></center>';


	}
	
	public function printPayroll($item){
		
		
		
		$sql = "SELECT a.ID,a.LeaveDays,a.Basicpay,a.OwnerID,a.DateTime,a.Month,a.Year,a.BankID
				,b.BankName,b.Branch,b.AccountNumber,b.SSS,
				(SELECT GovernmentID FROM `basic-information` WHERE ID=a.OwnerID) as 'GID',
				a.Position as 'Pos',
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(LeaveDays) FROM `payroll-information` WHERE OwnerID=a.OwnerID) as 'LeaveDaysTotal',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE 
				PayID IN (SELECT ID FROM `payroll-information` WHERE OwnerID=a.OwnerID) AND ItemName LIKE '%PAYE%') as 'PAYETotal'
				FROM `payroll-information` a,`payroll-bank-information` b
				WHERE a.BankID=b.ID AND a.ID='$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();
			
		$today =  date("Y-m-d");
		$admin = $this->core->userID;
		$owner = $this->core->userID;
		$payslipID =  $fetch["ID"];
		$basicpay =  $fetch["Basicpay"];
		$name =  $fetch["Name"];
		$GID =  $fetch["GID"];
		$uid =  $fetch["OwnerID"];
		$leaveDays =  $fetch["LeaveDays"];
		$month =  $fetch["Month"];
		$year =  $fetch["Year"];
		$bankID =  $fetch["BankID"];
		$bankName =  $fetch["BankName"];
		$branch =  $fetch["Branch"];
		$accountNumber =  $fetch["AccountNumber"];
		$sss =  $fetch["SSS"];
		$pos =  $fetch["Pos"];
		$taxTD =  $fetch["PAYETotal"];
		$totalLeave =  $fetch["LeaveDaysTotal"];
		
		$sqlx = "SELECT * FROM `payroll-items` WHERE PayID = '$payslipID' ORDER BY Type";
		$runx = $this->core->database->doSelectQuery($sqlx);
			
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/'.$item.'">PRINT PAY SLIP</a>';
		
		if($this->core->role == 102 || $this->core->role == 1000){
			echo '<a href="' . $this->core->conf['conf']['path'] . '/payroll/edit/'.$item.'">Edit</a>';
		}
		
		echo '</div>';
		
		echo '<div id="print" style="word-wrap: normal|break-word|initial|inherit;"><div page-break-after:always;">';

		echo '<center><img width="300px" src="'.$this->core->conf['conf']['path'].'/datastore/logo/speedcap.png">';
			
		echo '<h2>Pay Slip </h2></center><br><table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr class="heading">
                    <td ><strong>EMPLOYEE: </strong>'.$name.'</td><td><strong>PAY PERIOD:</strong> '.$month.' '.$year.'</td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>ID NUMBER: </strong>'.$GID.'</td><td><strong>JOB TITLE: </strong>'.$pos.'</td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>BANK DETAILS: </strong>'.$bankName.' '.$branch.'</td><td><strong>Acc # </strong>'.$accountNumber.' </td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>SOCIAL SECURITY NUMBER: </strong>'.$sss.' </td><td><strong>CURRENCY:  </strong>ZMW</td>
                  </tr>
				  <tr class="heading">
                    <td ><strong>TAX Year to-Date: </strong>'.number_format($taxTD,2).' '.$this->core->conf['conf']['currency'].' </td><td><strong>Leave Days:  </strong>'.$totalLeave.'</td>
                  </tr>
                  </table>';
		
		echo '<center><table width="468" border="1" cellpadding="5" cellspacing="0"><tr>';
		
		echo '<th>INCOMES</th><th>DEDUCTIONS</th></tr>';
		$tincome=0;
		$tdeduction=0;
		$incomePrint="";
		$deductionPrint="";
		echo '<tr>';
		while ($fetchx = $runx->fetch_assoc()) {
								
				if($fetchx['Type']=="Income"){
					$incomePrint.='<strong>'.$fetchx['ItemName'].'</strong> ' . number_format($fetchx["Amount"],2). '</br>';
					$tincome+=$fetchx["Amount"];
				}else{
					$deductionPrint.='<strong>'.$fetchx['ItemName'].'</strong> ' . number_format($fetchx["Amount"],2). '</br>';
					$tdeduction+=$fetchx["Amount"];
				}                    
                  
		}
		echo "<td>$incomePrint</td><td>$deductionPrint</td>";
		$tnetpay=$tincome-$tdeduction;
		echo '</tr><tr><th>Total :'.number_format($tincome,2).' '.$this->core->conf['conf']['currency'].'</th><th>Total :'.number_format($tdeduction,2).' '.$this->core->conf['conf']['currency'].'</th></tr>';		
		echo '<tr><th>Net Pay</th><th>'.number_format($tnetpay,2).' '.$this->core->conf['conf']['currency'].'</th></tr>';		
		echo ' </table></center>';
		
		echo '</div></div>';
		
		echo'<script type="text/javascript">
			printContents = document.getElementById("print").innerHTML;
			document.body.innerHTML = printContents;
			window.print();
		</script>';

	}
	
	public function addPayroll($item) {
		if(isset($_GET['amount'])){
			$amount = $_GET['amount'];
			$description = $_GET['description'];
			$type = $_GET['type'];
		}
		if(isset($_GET['uid'])){
			$uid = $_GET['uid'];
		}
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$employees = $select->showEmployees($item, null);
		
		$positions = $select->showPositionList($item, null);

		include $this->core->conf['conf']['formPath'] . "addpayroll.form.php";
	}
	
	public function editPayroll($item) {
		
		$sql = "SELECT a.ID,a.LeaveDays,a.Basicpay,a.OwnerID,a.DateTime,a.Month,a.Year,a.BankID
				,b.BankName,b.Branch,b.AccountNumber,b.SSS,
				(SELECT GovernmentID FROM `basic-information` WHERE ID=a.OwnerID) as 'GID',
				a.Position as 'Pos',
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name'
				FROM `payroll-information` a,`payroll-bank-information` b
				WHERE a.BankID=b.ID AND a.ID='$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();
			
		$today =  date("Y-m-d");
		$admin = $this->core->userID;
		$owner = $this->core->userID;
		$payslipID =  $fetch["ID"];
		$basicpay =  $fetch["Basicpay"];
		$name =  $fetch["Name"];
		$GID =  $fetch["GID"];
		$uid =  $fetch["OwnerID"];
		$leaveDays =  $fetch["LeaveDays"];
		$month =  $fetch["Month"];
		$year =  $fetch["Year"];
		$bankID =  $fetch["BankID"];
		$bankName =  $fetch["BankName"];
		$branch =  $fetch["Branch"];
		$accountNumber =  $fetch["AccountNumber"];
		$sss =  $fetch["SSS"];
		$pos =  $fetch["Pos"];
		
		
		$sqlx = "SELECT * FROM `payroll-items` WHERE PayID = '$payslipID' ORDER BY Type";
		$runx = $this->core->database->doSelectQuery($sqlx);
		$itemsprint="<table border='1'><tr><th>Name</th><th>Amount</th><th>Type</th><th>Action</th></tr> ";
		while ($fetchx = $runx->fetch_assoc()) {
								
			$itemsprint.="<tr><td>".$fetchx['ItemName']."</td><td>" . number_format($fetchx['Amount'],2). "</td><td>".$fetchx['Type']." </td>
			<td><a href=". $this->core->conf['conf']['path'] . "/payroll/deleteitem/".$fetchx['ID']."?payid=".$item.">Delete</a></td></tr>";
		         
		}
		$itemsprint.="</table>";
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$employees = $select->showEmployees($item, null);
		
		$positions = $select->showPositionList($item, null);
		echo "Edit payroll";
		include $this->core->conf['conf']['formPath'] . "editpayroll.form.php";
		
	}
	public function deleteitemPayroll($item) {
		if(isset($_GET['payid'])){
			$payid = $_GET['payid'];
		}
		$sqlx = "DELETE FROM `payroll-items` WHERE ID = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		
		echo '<b>Delete Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/edit/'.$payid.'" >Back To edit payslip</a>';
		
	}
	
	public function deletePayroll($item) {
		if(isset($_GET['payid'])){
			$payid = $_GET['payid'];
		}
		$sqlx = "DELETE FROM `payroll-information` WHERE ID = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		
		echo '<b>Delete Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/manage/" >Back To manage</a>';
		
	}

	public function savePayroll($item) {
		
		$LeaveDays = $this->core->cleanPost['LeaveDays'];
		$Basicpay = $this->core->cleanPost['Basicpay'];
		$OwnerID = $this->core->cleanPost['OwnerID'];
		$Month = $this->core->cleanPost['Month'];
		$Year = $this->core->cleanPost['Year'];
		$Position = $this->core->cleanPost['Position'];
		$BankID = $this->core->cleanPost['BankID'];
		$Entries = $this->core->cleanPost['Entries'];
				
		$uid = $this->core->userID;
		
				
		$sql = "INSERT INTO `loan_pro`.`payroll-information` (`LeaveDays`, `Basicpay`, `OwnerID`, `DateTime`, `Month`, `Year`, `BankID`, `Position`, `CreaterID`) 
		 VALUES ('$LeaveDays', '$Basicpay', '$OwnerID', NOW(), '$Month', '$Year', '$BankID', '$Position', '$uid')";
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		if($run){
			$PayID = $this->core->database->id();
			for($i=0;$i <= $Entries;$i++){
				$ItemName = $this->core->cleanPost['ItemName'.$i];
				$Amount = $this->core->cleanPost['Amount'.$i];
				$Type = $this->core->cleanPost['Type'.$i];
				
				$sqlItm = "INSERT INTO `loan_pro`.`payroll-items` (`PayID`, `ItemName`, `Amount`, `Type`) VALUES ('$PayID ', '$ItemName', '$Amount', '$Type ')";
				//echo $sqlItm;
				
				$runItm = $this->core->database->doInsertQuery($sqlItm);
				
			}
			
			echo '<b>Save Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/manage/" >Back To Payroll</a>';
		}
	}
	public function updatePayroll($item) {
		
		$LeaveDays = $this->core->cleanPost['LeaveDays'];
		$Basicpay = $this->core->cleanPost['Basicpay'];
		$OwnerID = $this->core->cleanPost['OwnerID'];
		$Month = $this->core->cleanPost['Month'];
		$Year = $this->core->cleanPost['Year'];
		$Position = $this->core->cleanPost['Position'];
		$BankID = $this->core->cleanPost['BankID'];
		$Entries = $this->core->cleanPost['Entries'];
				
		$uid = $this->core->userID;
		if(!empty($BankID)){
			$commit=" `BankID`='$BankID',";
		}else{
			$commit="";
		}
		
	
		$sql = "UPDATE `loan_pro`.`payroll-information` SET `LeaveDays`='$LeaveDays', $commit `OwnerID`='$OwnerID', `Month`='$Month', `Year`='$Year', `Basicpay`='$Basicpay',  `Position`='$Position', `CreaterID`='$uid' WHERE (`ID`='$item')";
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		if($run && $Entries > 0){
			
			for($i=0;$i <= $Entries;$i++){
				$ItemName = $this->core->cleanPost['ItemName'.$i];
				$Amount = $this->core->cleanPost['Amount'.$i];
				$Type = $this->core->cleanPost['Type'.$i];
				
				$sqlItm = "INSERT INTO `loan_pro`.`payroll-items` (`PayID`, `ItemName`, `Amount`, `Type`) VALUES ('$item ', '$ItemName', '$Amount', '$Type ')";
				//echo $sqlItm;
				
				$runItm = $this->core->database->doInsertQuery($sqlItm);
				
			}
			
			echo '<b>Save Success payroll items updated</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/manage/" >Back To Payroll</a>';
		}else{
			echo '<b>Save Success <b>no</b> payroll items updated </b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/manage/" >Back To Payroll</a>';
		}
	}	
	public function showPayroll($item)  {
		$today = date("Y-m-d");
		$currentMonth=date("F");
		$currentYear=date("Y");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);

		$sql = "SELECT *,
				(SELECT CONCAT(FirstName,' ',MiddleName,' ',Surname) FROM `basic-information` WHERE ID=a.OwnerID) as 'Name',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Deduction') as 'Deduction',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND Type='Income') as 'Income',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%NAPSA%') as 'NAPSA',
				(SELECT SUM(Amount) FROM `payroll-items` WHERE PayID=a.ID AND ItemName LIKE '%PAYE%') as 'PAYE'
				FROM `payroll-information` a 
				WHERE OwnerID =".$item;

		$run = $this->core->database->doSelectQuery($sql);
		
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Back to Profile</a>';
				
		echo '</div>';
		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td ><b>Pay Slip #</b></td>' .
		'<td ><b>Name</b></td>' .
		'<td ><b>Basic Pay</b></td>' .
		'<td ><b>Incomes</b></td>' .
		'<td ><b>Deductions</b></td>' .
		'<td ><b>Net Pay</b></td>'.
		'<td><b>Management</b></td>';
		
		echo '</tr>';
		$totalincome=0;
		$totaldeduction=0;
		$totalnet=0;
		$totalnapsa=0;
		$totalpaye=0;
		$output = "";
		$color = 'style="color: #FF0000;"';
		$name="";
		while ($fetch = $run->fetch_assoc()) {
			
			$name=$fetch['Name'];
			$income=$fetch['Income'];
			$deduction=$fetch['Deduction'];
			$netpay=$income-$deduction;
			
			$output .= '<tr>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payroll/view/' . $fetch['ID'] . '"> ' . $fetch['Month'] .'-'.$fetch['Year'].'-'.$fetch['ID']. '</a></b></td>
			<td>' . $fetch['Name'] . '</td>
			<td>' . $fetch['Basicpay'] . ' '.$this->core->conf['conf']['currency'].'</td>
			<td><b>' . $income . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $deduction . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><b>' . $netpay . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/payroll/print/' . $fetch['ID'] . '"> Print</a></td>';
		
		$totalincome+=$income;
		$totaldeduction+=$deduction;
		$totalnet+=$netpay;
		$totalnapsa+=$fetch['NAPSA'];
		$totalpaye+=$fetch['PAYE'];
		}

	
		echo '<div class="successpopup">A total of '.$run->num_rows . " payrslip(s) for the selected for: $name </div> 
		<div class=\"warningpopup\"> Total Incomes: $totalincome ".$this->core->conf['conf']['currency']." 
		 Deductions: $totaldeduction".$this->core->conf['conf']['currency']."  
		 Net Pays:$totalnet ".$this->core->conf['conf']['currency']."
		 NAPSA :$totalnapsa ".$this->core->conf['conf']['currency']." 
		 PAYE :$totalpaye ".$this->core->conf['conf']['currency']."</div>";
		echo $output;
		echo '</table>';
	}
	public function showbankPayroll($item)  {
		$today = date("Y-m-d");
		$currentMonth=date("F");
		$currentYear=date("Y");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);

		$sql = "SELECT *
				FROM `payroll-bank-information` 
				WHERE OwnerID =".$item;

		$run = $this->core->database->doSelectQuery($sql);
		
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Back to Profile</a> 
		<a href="' . $this->core->conf['conf']['path'] . '/payroll/addbank/'.$item.'">Add Bank</a>';
				
		echo '</div>';
		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td ><b>#</b></td>' .
		'<td ><b>Bank Name</b></td>' .
		'<td ><b>Branch</b></td>' .
		'<td ><b>Account Number</b></td>' .
		'<td ><b>Social Security</b></td>' .
		'<td ><b>Tpin</b></td>'.
		'<td><b>Management</b></td>';
		
		echo '</tr>';
		
		while ($fetch = $run->fetch_assoc()) {
		$output .= '<tr>
			<td>'.$fetch['ID']. '</td>
			<td>' . $fetch['BankName'] . '</td>
			<td>' . $fetch['Branch'] . ' </td>
			<td><b>' . $fetch['AccountNumber'] . ' </b></td>
			<td><b>' . $fetch['SSS']. ' </b></td>
			<td><b>' . $fetch['Tpin'] . ' </b></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/payroll/editbank/' . $fetch['ID'] . '"> Edit</a> |
			<a href="' . $this->core->conf['conf']['path'] . '/payroll/deletebank/' . $fetch['ID'] . '&uid='.$fetch['OwnerID'].'"> Delete</a></td>';
		}

	
		echo $output;
		echo '</table>';
	}
	
	function getentiresPayroll($item) {
		if(isset($_GET['items'])){
			$item = $_GET['items'];
		}
		
		for ($i=1; $i<=$item;$i++){
			
			echo "Item  ".$i."";
		}
	}
	//bank information
	public function addbankPayroll($item) {
		
		include $this->core->conf['conf']['formPath'] . "addbankpayroll.form.php";
	}
	
	public function editbankPayroll($item) {
		
		$sql = "SELECT * FROM `payroll-bank-information` WHERE ID='$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();
			
		$today =  date("Y-m-d");
		$admin = $this->core->userID;
		$owner = $this->core->userID;
		$BankID =  $fetch["BankID"];
		$BankName =  $fetch["BankName"];
		$OwnerID = $fetch["OwnerID"];
		$Branch =  $fetch["Branch"];
		$AccountNumber =  $fetch["AccountNumber"];
		$SSS =  $fetch["SSS"];
		$Tpin =  $fetch["Tpin"];
		
		include $this->core->conf['conf']['formPath'] . "editbankpayroll.form.php";
		
	}
	public function deletebankPayroll($item) {
		if(isset($_GET['uid'])){
			$uid = $_GET['uid'];
		}
		$sqlx = "DELETE FROM `payroll-bank-information` WHERE ID = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		
		echo '<b>Delete Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/showbank/'.$uid.'" >Back To bank information</a>';
		
	}
	public function savebankPayroll($item) {
		
		$saveType = $this->core->cleanPost['saveType'];
		$BankName = $this->core->cleanPost['BankName'];
		$Branch = $this->core->cleanPost['Branch'];
		$AccountNumber = $this->core->cleanPost['AccountNumber'];
		$OwnerID = $this->core->cleanPost['OwnerID'];
		$Tpin = $this->core->cleanPost['Tpin'];
		$SSS = $this->core->cleanPost['SSS'];
		
		if(isset ($this->core->cleanPost['BankID'])){
			$BankID = $this->core->cleanPost['BankID'];
		}
				
		$uid = $this->core->userID;
		
		$sql ="";
		if($saveType =='add'){
			$sql ="INSERT INTO `loan_pro`.`payroll-bank-information` (`BankName`, `Branch`, `AccountNumber`, `OwnerID`, `Tpin`, `SSS`) VALUES ('$BankName', '$Branch', ' $AccountNumber', '$OwnerID', '$Tpin' , '$SSS')";
		}else{
			$sql ="UPDATE `loan_pro`.`payroll-bank-information` SET `BankName`='$BankName', `Branch`='$Branch', `AccountNumber`='$AccountNumber', `Tpin`='$Tpin', `SSS`='$SSS' WHERE (`ID`='$BankID')";
		}
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		if($run){
						
			echo '<b>Save Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/payroll/showbank/'.$OwnerID.'" >Back To bank information</a>';
		}
	}
	
	
}

?>
