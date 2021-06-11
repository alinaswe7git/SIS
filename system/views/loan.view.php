<?php
class loan {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function editLoan($item) {
		$ownerID=$_GET['userid'];
		$sql = "UPDATE `loan_pro`.`loan-information` SET `Status`='Closed'  WHERE `ID` = '" . $item ."'";
		$run = $this->core->database->doInsertQuery($sql);

		//$this->core->redirect("information", "show", $item);
		if($run){
			echo '<b>Close Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/show/'.$ownerID.'" >Back To Show Loans</a></div>';;
		}
	}


	public function addLoan($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$collateral = $select->showColluteral($item, null);
		
		include $this->core->conf['conf']['formPath'] . "addloan.form.php";
	}

	public function deleteLoan($item) {
		$ownerID=$_GET['userid'];
		$sql = 'DELETE FROM `loan_pro`.`loan-information`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		if($run){
			echo '<b>DELETE Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/show/'.$ownerID.'" >Back To Client Loan</a></div>';;
		}
		
	}
	public function uploadsignedLoan($item) {
		$ownerID=$_GET['userid'];
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$collateral = $select->showColluteral($item, null);
		
				
		include $this->core->conf['conf']['formPath'] . "uploadsignedloan.form.php";
	}
	public function saveuploadLoan() {
		$ownerID = $this->core->cleanPost['ownerID'];
		$id = $this->core->cleanPost['id'];
				
		if (isset($_FILES["file"])) {

			$file = $_FILES["file"];
		
			$home = getcwd();
			$path = $this->core->conf['conf']["dataStorePath"] . 'uploads/' . $course;

	
		
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $file["error"]["file"] . "<br>";
			} else {
		
				$fname = $_FILES["file"]["name"];
				$destination = $path."/".$fname;
		
				if (file_exists($destination)) {
					$fname = rand(1,999) . '-' .$fname;
					$destination = $path."/".$fname;
				}

				move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
				
				if(file_exists($destination)){
					echo'<div class="successpopup">File uploaded as '.$fname.'</div>';
				}
			}
		}
		$base = $this->core->conf['conf']['path'] . '/datastore/uploads/' . $item . '/'. $fname;
		
		$sql = "UPDATE `loan_pro`.`loan-information` SET `Document`='$base' WHERE ID = $id;";
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		//$this->core->redirect("information/show", $item);
		if($run){
			echo '<b>Update Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/show/'.$ownerID.'" >Back To Client loan</a></div>';
		}
	}

	public function saveLoan() {
		$ownerID = $this->core->cleanPost['ownerID'];
		$name = $this->core->cleanPost['name'];
		$collateralID = $this->core->cleanPost['collateralID'];
		$rate = $this->core->cleanPost['rate'];
		$amount = $this->core->cleanPost['amount'];
		$startDate = $this->core->cleanPost['startDate'];
		$endDate = $this->core->cleanPost['endDate'];
		
		$start = new DateTime($startDate);
		$end   = new DateTime($endDate);
		$diff  = $start->diff($end);
		$durationInMonths= $diff->format('%y') * 12 + $diff->format('%m');
		//$durationInMonths =get_month_diff($startDate, $endDate);
		
		
		$sql = "INSERT INTO `loan_pro`.`loan-information` (`Name`, `StartDate`, `CollateralID`, `Rate`, `OwnerID`, `Amount`, `DurationInMonths`,`EndDate`, `Status`)
		VALUES ('$name','$startDate', '$collateralID', '$rate', '$ownerID', '$amount', '$durationInMonths', '$endDate', 'Active');";
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $name, $uid, "Loan added Client $ownerID - $name");
		
		//$this->core->redirect("information", "show", $item);
		if($run){
			echo '<b>Save Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/loan/show/'.$ownerID.'" >Back To Client Loan</a>';
		}
	}
	public function showLoan($item) {

		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to Client Profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/loan/add/'.$item.'">Add Loan</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `loan-information` WHERE OwnerID= '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Loans</h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width=""><b>Trans ID</b></td>' .
		'<td width="200px"><b>Date/Time</b></td>' .
		'<td width=""><b>Collected</b></td>' .
		'<td width=""><b>To pay</b></td>' .
		'<td width=""><b>Rate %</b></td>' .
		'<td width=""><b>ClientID</b></td>' .
		'<td width=""><b>Description</b></td>' .
		'<td width=""><b>Status</b></td>' .
		'<td width="140px"><b>Management</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_assoc()) {
			$reverse = FALSE;


			if($fetch['Status'] == "Active"){
				$color = 'style="color: #00000;"';
			}

			if($fetch['Status'] == "Closed"){
				$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
			}
					
			$bid = $fetch['ID'];
			$uid =  $fetch['OwnerID'];
			$amount =  $fetch['Amount'];
			$topay =  $fetch['Amount'];
			$document = $fetch['Document'];
			$start = new DateTime($fetch['StartDate']);
			$end   = new DateTime($fetch['EndDate']);
			
			$date =  '<b>From:</b> '.$start->format('Y-m-d').' <b></br>To:</b>'.$end->format('Y-m-d').' <b>'.$fetch['DurationInMonths'].'</b> Month(s)';
			$description =  $fetch['Name'];
			$status =  $fetch['Status'];
			$rate=$fetch['Rate'];
			$durationInMonths=$fetch['DurationInMonths'];
			
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);

			echo '<tr ' . $color . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/loan/print/'.$bid.'">LOAN-' . $bid . '</a></b></td>
				<td>' . $date . '</td>
				<td><b>' . number_format($amount,2) . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . number_format($topay,2). ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . $rate. '</b></td>
				<td>' . $uid. '</td> ';
			if(!empty ($document)){
				echo '<td><a href="'.$document.'" >' . $description . '</a></td>';
			}else{
				echo '<td>' . $description . ' </td>';
			}
				echo '<td>' . $status . ' </td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/loan/detail/' . $fetch['ID'] . '?userid=' . $uid. '"> Details </a> | 
				<a href="' . $this->core->conf['conf']['path'] . '/loan/edit/' . $fetch['ID'] . '?userid=' . $uid. '"> Close </a> | 
				    <a href="' . $this->core->conf['conf']['path'] . '/loan/uploadsigned/' . $fetch['ID'] . '?userid=' . $uid. '"> Upload </a> '; 
	
				if(substr($description,0,7) != 'SETTLED'){
					echo' | <a href="' . $this->core->conf['conf']['path'] . '/loan/settle/' . $fetch['ID'] . '?userid=' . $uid. '"> settle</a>';
				}
			echo'</td></tr>';

			$total = $amount + $total;
			$totalToPay = $topay + $totalToPay;
		}


		echo '<tr class="heading"><td><b>Total Collected</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="7"><b>'. number_format($total,2) .'  '.$this->core->conf['conf']['currency'].'</b></td>'. 
		'<tr class="heading"><td><b>Total To Pay</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="7"><b>'. number_format($totalToPay,2) .'  '.$this->core->conf['conf']['currency'].'</b></td>' .

		'</tr>';

		echo '</table>';

	}
	public function detailLoan($item) {
		$ownerID =$_GET['userid'];
		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$ownerID.'">Return to Client Profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/loan/show/'.$ownerID.'" >Back To Client Loan</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `loan-information` WHERE ID= '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Loans</h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width=""><b>Trans ID</b></td>' .
		'<td width="200px"><b>Date/Time</b></td>' .
		'<td width=""><b>Collected</b></td>' .
		'<td width=""><b>To pay</b></td>' .
		'<td width=""><b>Rate %</b></td>' .
		'<td width=""><b>ClientID</b></td>' .
		'<td width=""><b>Description</b></td>' .
		'<td width=""><b>Status</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_assoc()) {
			$reverse = FALSE;


			if($fetch['Status'] == "Active"){
				$color = 'style="color: #00000;"';
			}

			if($fetch['Status'] == "Closed"){
				$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
			}
					
			$bid = $fetch['ID'];
			$uid =  $fetch['OwnerID'];
			$amount =  $fetch['Amount'];
			$topay =  $fetch['Amount'];
			$document = $fetch['Document'];
			$start = new DateTime($fetch['StartDate']);
			$end   = new DateTime($fetch['EndDate']);
			
			$date =  '<b>From:</b> '.$start->format('Y-m-d').' <b></br>To:</b>'.$end->format('Y-m-d').' <b>'.$fetch['DurationInMonths'].'</b> Month(s)';
			$description =  $fetch['Name'];
			$status =  $fetch['Status'];
			$rate=$fetch['Rate'];
			$durationInMonths=$fetch['DurationInMonths'];
			
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);
			
			
			$diff  = $start->diff($end);
			$startYM= $start->format('y').'-'.$start->format('n');
			$endYM= $end->format('y').'-'.$end->format('n');
			$curr=date("Y-m-d");
			$datecurr = new DateTime($curr);
			
            $datecurrYM =$datecurr->format('y').'-'.$datecurr->format('m').'-'.$datecurr->format('t').'-'.$datecurr->format('j');
			
			echo '<tr ' . $color . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/loan/print/'.$bid.'">LOAN-' . $bid . '</a></b></td>
				<td>' . $date . '</td>
				<td><b>' . number_format($amount,2) . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . number_format($topay,2). ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . $rate. '</b></td>
				<td>' . $uid. '</td> ';
			if(!empty ($document)){
				echo '<td><a href="'.$document.'" >' . $description . '</a></td>';
			}else{
				echo '<td>' . $description . ' </td>';
			}
				echo '<td>' . $status . ' </td>';
				
			echo'</td></tr>';
			
			$monthcount=0;
			
			while ($monthcount <= $durationInMonths){
				$interest_perM= round($amount*pow((1+$rate/100),$monthcount),2);
				$interest_prvMon= round($amount*pow((1+$rate/100),$monthcount-1),2);
				$interest_tilDate= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$interest_prvMon,2);
				$interest_initMon= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$amount,2);
				
				echo'<tr><td colspan="6">';
				
				if(($start->format('y')==$end->format('y'))&&($datecurr->format('n')-$start->format('n'))==$monthcount){
					
					echo'<b> Interest accrued to-date :<i>'.number_format($interest_tilDate+$interest_prvMon,2).' ZMW<i> interest accrued at end of month current month :<i>'.number_format($interest_perM,2).' ZMW </i> </b>' ;
					
				}else{
					//if ($monthcount==0){
						//echo'<b>Interest accrued so far in the initial month '.($monthcount).' : <i>'.number_format($interest_initMon,2).' ZMW </i></b>' ;
					
					//}else{
						echo'Interest accrued at end of month '.($monthcount).' : <i>'.number_format($interest_perM,2).' ZMW </i>' ;
					//}
					
				}
				
				
				echo'</td></tr>';
				$monthcount++;
			}
			while ($end->format('n') < $datecurr->format('n')){
				$interest_perM= round($amount*pow((1+$rate/100),$monthcount),2);
				$interest_prvMon= round($amount*pow((1+$rate/100),$monthcount-1),2);
				$interest_tilDate= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$interest_prvMon,2);
				
				echo'<tr><td colspan="6">';
				
				echo'<b> Over due interest accrued at end of month '.($monthcount).' :<i>'.number_format($interest_perM,2).' ZMW </i> interest accrued to-date :<i>'.number_format($interest_tilDate+$interest_prvMon,2).' ZMW<i></b>' ;
					
								
				
				echo'</td></tr>';
				if($durationInMonths==$monthcount){
					$monthcount++;
				}
			}

			$total = $interest_tilDate;
			$totalToPay = $interest_perM;
		}


		echo '<tr class="heading"><td><b>Total To Pay buy loan Close</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="6"><b>'. number_format($totalToPay,2) .'  '.$this->core->conf['conf']['currency'].'</b></td>' .

		'</tr>';

		echo '</table>';

	}
	public function detaillendLoan($item) {
		$ownerID =$_GET['userid'];
		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$ownerID.'">Return to Client Profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/loan/showlend/'.$ownerID.'" >Back To Client Lend</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `loan-lend-information` WHERE ID= '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Loans</h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width=""><b>Trans ID</b></td>' .
		'<td width="200px"><b>Date/Time</b></td>' .
		'<td width=""><b>Collected</b></td>' .
		'<td width=""><b>To pay</b></td>' .
		'<td width=""><b>Rate %</b></td>' .
		'<td width=""><b>ClientID</b></td>' .
		'<td width=""><b>Description</b></td>' .
		'<td width=""><b>Status</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_assoc()) {
			$reverse = FALSE;


			if($fetch['Status'] == "Active"){
				$color = 'style="color: #00000;"';
			}

			if($fetch['Status'] == "Closed"){
				$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
			}
					
			$bid = $fetch['ID'];
			$uid =  $fetch['OwnerID'];
			$amount =  $fetch['Amount'];
			$topay =  $fetch['Amount'];
			$document = $fetch['Document'];
			$start = new DateTime($fetch['StartDate']);
			$end   = new DateTime($fetch['EndDate']);
			
			$date =  '<b>From:</b> '.$start->format('Y-m-d').' <b></br>To:</b>'.$end->format('Y-m-d').' <b>'.$fetch['DurationInMonths'].'</b> Month(s)';
			$description =  $fetch['Name'];
			$status =  $fetch['Status'];
			$rate=$fetch['Rate'];
			$durationInMonths=$fetch['DurationInMonths'];
			
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);
			
			
			$diff  = $start->diff($end);
			$startYM= $start->format('y').'-'.$start->format('n');
			$endYM= $end->format('y').'-'.$end->format('n');
			$curr=date("Y-m-d");
			$datecurr = new DateTime($curr);
			
            $datecurrYM =$datecurr->format('y').'-'.$datecurr->format('m').'-'.$datecurr->format('t').'-'.$datecurr->format('j');
			
			echo '<tr ' . $color . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/loan/print/'.$bid.'">LOAN-' . $bid . '</a></b></td>
				<td>' . $date . '</td>
				<td><b>' . number_format($amount,2) . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . number_format($topay,2). ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . $rate. '</b></td>
				<td>' . $uid. '</td> ';
			if(!empty ($document)){
				echo '<td><a href="'.$document.'" >' . $description . '</a></td>';
			}else{
				echo '<td>' . $description . ' </td>';
			}
				echo '<td>' . $status . ' </td>';
				
			echo'</td></tr>';
			
			$monthcount=0;
			
			while ($monthcount <= $durationInMonths){
				$interest_perM= round($amount*pow((1+$rate/100),$monthcount),2);
				$interest_prvMon= round($amount*pow((1+$rate/100),$monthcount-1),2);
				$interest_tilDate= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$interest_prvMon,2);
				$interest_initMon= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$amount,2);
				
				echo'<tr><td colspan="6">';
				
				if(($start->format('y')==$end->format('y'))&&($datecurr->format('n')-$start->format('n'))==$monthcount){
					
					echo'<b> Interest accrued to-date :<i>'.number_format($interest_tilDate+$interest_prvMon,2).' ZMW<i> interest accrued at end of month current month :<i>'.number_format($interest_perM,2).' ZMW </i> </b>' ;
					
				}else{
					//if ($monthcount==0){
						//echo'<b>Interest accrued so far in the initial month '.($monthcount).' : <i>'.number_format($interest_initMon,2).' ZMW </i></b>' ;
					
					//}else{
						echo'Interest accrued at end of month '.($monthcount).' : <i>'.number_format($interest_perM,2).' ZMW </i>' ;
					//}
					
				}
				
				
				echo'</td></tr>';
				$monthcount++;
			}
			while ($end->format('n') < $datecurr->format('n')){
				$interest_perM= round($amount*pow((1+$rate/100),$monthcount),2);
				$interest_prvMon= round($amount*pow((1+$rate/100),$monthcount-1),2);
				$interest_tilDate= round(($datecurr->format('j')/$datecurr->format('t'))*$rate/100*$interest_prvMon,2);
				
				echo'<tr><td colspan="6">';
				
				echo'<b> Over due interest accrued at end of month '.($monthcount).' :<i>'.number_format($interest_perM,2).' ZMW </i> interest accrued to-date :<i>'.number_format($interest_tilDate+$interest_prvMon,2).' ZMW<i></b>' ;
					
								
				
				echo'</td></tr>';
				if($durationInMonths==$monthcount){
					$monthcount++;
				}
			}

			$total = $interest_tilDate;
			$totalToPay = $interest_perM;
		}


		echo '<tr class="heading"><td><b>Total To Pay buy loan Close</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="6"><b>'. number_format($totalToPay,2) .'  '.$this->core->conf['conf']['currency'].'</b></td>' .

		'</tr>';

		echo '</table>';

	}
	/*********Collateral start**********/
	public function showcolluteralLoan($item) {

		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to Client Profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/loan/addcolluteral/'.$item.'">Add Collateral</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `loan-collateral` WHERE OwnerID= '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Collateral</h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width=""><b>Name</b></td>' .
		'<td width="200px"><b>Value</b></td>' .
		'<td width="200px"><b>Inspector</b></td>' .
		'<td width="200px"><b>Inspection Date</b></td>' .
		'<td width="200px"><b>Comment</b></td>' .
		'<td width="140px"><b>Management</b></td>' .
		'</tr>';


		while ($fetch = $run->fetch_assoc()) {
								
			$bid = $fetch['ID'];
			$uid =  $fetch['OwnerID'];
			

			echo '<tr ' . $color . '>
				<td><b>'. $fetch['Name']. '</b></td>
				<td><b>'. number_format($fetch['Value'],2). '</b></td>
				<td><b>'. $fetch['InspectorName']. '</b></td>
				<td><b>'. $fetch['InspectionDate']. '</b></td>
				<td><b>'. $fetch['Comments']. '</b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/loan/editcolluteral/'.$fetch['ID'].'?userid='.$uid.'" >Edit</a>
				|<a href="' .$fetch['Document'].'" >View Document</a>
				|<a href="' . $this->core->conf['conf']['path'] . '/loan/deletecolluteral/'.$fetch['ID'].'?userid='.$uid.'" >Delete</a></li>';
			echo'</td></tr>';

		}
		echo '</table>';
	}
	public function addcolluteralLoan($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$users = $select->showUsers("100", null);
		
		include $this->core->conf['conf']['formPath'] . "addcolluteral.form.php";
	}
	
	public function savecolluteralLoan() {
		$ownerID = $this->core->cleanPost['ownerID'];
		$name = $this->core->cleanPost['name'];
		$value = $this->core->cleanPost['value'];
		$code = $this->core->cleanPost['code'];
		$inspectionDate = $this->core->cleanPost['inspectionDate'];
		$inspectorName = $this->core->cleanPost['inspectorName'];
		$comments = $this->core->cleanPost['comments'];
		$userID=$_SESSION['userid'];
		
		if (isset($_FILES["file"])) {

			$file = $_FILES["file"];
		
			$home = getcwd();
			$path = $this->core->conf['conf']["dataStorePath"] . 'uploads/' . $course;

	
		
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $file["error"]["file"] . "<br>";
			} else {
		
				$fname = $_FILES["file"]["name"];
				$destination = $path."/".$fname;
		
				if (file_exists($destination)) {
					$fname = rand(1,999) . '-' .$fname;
					$destination = $path."/".$fname;
				}

				move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
				
				if(file_exists($destination)){
					echo'<div class="successpopup">File uploaded as '.$fname.'</div>';
				}
			}
		}
		$base = $this->core->conf['conf']['path'] . '/datastore/uploads/' . $item . '/'. $fname;
		
		$sql = "INSERT INTO `loan_pro`.`loan-collateral` (`Name`, `Value`, `Code`, `OwnerID`, `Document`, `InspectorID`, `InspectionDate`, `Comments`, `InspectorName`)
		VALUES ('$name','$value', '$code', '$ownerID', '$base', '$userID', '$inspectionDate', '$comments', '$inspectorName');";
		//echo $sql;
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $name, $uid, "Loan Colluteral added Client $ownerID - $name");
		
		$run = $this->core->database->doInsertQuery($sql);
		
		//$this->core->redirect("information/show", $item);
		if($run){
			echo '<b>Add Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/showcolluteral/'.$ownerID.'" >Back To Client Collateral</a></div>';
		}
	}
	function editcolluteralLoan($item) {

		$sql = "SELECT * FROM `loan-collateral` WHERE `loan-collateral`.ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		while ($fetch = $run->fetch_assoc()) {
			
			$ownerID = $fetch['OwnerID'];
			$name = $fetch['Name'];
			$value = $fetch['Value'];
			$code = $fetch['Code'];
			$inspectorName = $fetch['InspectorName'];
			$comments = $fetch['Comments'];
			$item = $fetch['ID'];
			

			include $this->core->conf['conf']['formPath'] . "editcolluteral.form.php";
		}
	}
	public function updatecolluteralLoan($item) {
		$ownerID = $this->core->cleanPost['ownerID'];
		$name = $this->core->cleanPost['name'];
		$value = $this->core->cleanPost['value'];
		$code = $this->core->cleanPost['code'];
		$inspectionDate = $this->core->cleanPost['inspectionDate'];
		$inspectorName = $this->core->cleanPost['inspectorName'];
		$comments = $this->core->cleanPost['comments'];
		$userID=$_SESSION['userid'];
				
		$sql = "UPDATE `loan_pro`.`loan-collateral` SET `Name`='$name', `Value`='$value', `Code`='$code', `OwnerID`='$ownerID', `InspectorID`='$userID', `Comments`='$comments', `InspectorName`='$inspectorName' WHERE `ID`='$item';";
		//echo $sql;
		

		
		$run = $this->core->database->doInsertQuery($sql);
		
		//$this->core->redirect("information/show", $item);
		if($run){
			echo '<b>UPDATE Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/showcolluteral/'.$ownerID.'" >Back To Client Collateral</a></div>';
		}
	}
	public function deletecolluteralLoan($item) {
		$ownerID=$_GET['userid'];
		$sql = 'DELETE FROM `loan_pro`.`loan-collateral`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $item, $uid, "Loan Colluteral Deleted Client $item ");
		//$this->core->redirect("information", "show", $item);
		
		if($run){
			echo '<b>DELETE Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/loan/showcolluteral/'.$ownerID.'" >Back To Client Collateral</a></div>';
		}
	}
	public function get_month_diff($start, $end = FALSE)
	{
		$end OR $end = time();
		$start = new DateTime("@$start");
		$end   = new DateTime("@$end");
		$diff  = $start->diff($end);
		return $diff->format('%y') * 12 + $diff->format('%m');
	}
	/*********PAYMENT Loan********/
	
	public function settleLoan($item){
		$sub =$_GET['userid'];

		echo'<div class="col-lg-12 greeter" style="">Loan Settlement</div>';
		echo'<p><b>To settle this loan by cash press the following button</b></p>';
		echo '
		<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center"><b><a href="' . $this->core->conf['conf']['path'] . '/loan/regular/'. $item .'?userid='.$sub.'">Settle regular Loan</a></b></div>';
		echo'<p><br></p>';

	}


	public function regularLoan($item){
		$uid = $_GET['userid'];
		$sql = "SELECT * FROM `transactions` WHERE `transactions`.StudentID = '$uid' AND `Status` != 'REVERSED'";
		$run = $this->core->database->doSelectQuery($sql);

		echo'<form id="settlement" name="settlement" method="post" action="'.$this->core->conf['conf']['path']. '/loan/listsettle/'.$item.'?userid='.$uid.'">
		<div class="heading">SELECT PAYMENT(S) FOR THIS BILL TO BE SETTLED WITH</div><fieldset>';

		while ($fetch = $run->fetch_assoc()) {
			$amount = $fetch["Amount"];
			$ID = $fetch["ID"];
			$reference = $fetch["TransactionID"];
			$date = $fetch["TransactionDate"];

			echo'  <input type="checkbox" name="pay[]" value="'.$ID.'"> '.$date.' - '.$reference.' - <b>'.$amount.'</b><br>';
			$set = TRUE;
		}

		if($set != TRUE){
			echo'<div class="warningpopup">No payments for this clients yet. Please add payment first!</div>';


			echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center; width: 95%;">
			<b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/'. $uid .'">BACK TO PAYMENT OVERVIEW</a></b></div>';
			echo'<p><br></p>';
		} else {
			echo'<hr></fieldset> <input type="submit" value="Submit"></form>';
		}

	}



	public function listsettleLoan($item){

		$bill = $item;
		$uid =  $this->core->subitem;
	
		$payments = $this->core->cleanPost['pay'];

		
		$amount = 0;
		foreach($payments as $item){
			
			$sql = "SELECT * FROM `transactions` WHERE `transactions`.ID = '$item' AND `Phone` != '100'";
			$run = $this->core->database->doSelectQuery($sql);
		

			while ($fetch = $run->fetch_assoc()) {
				$pay = $fetch["Amount"];
				$amount = $amount+$pay;
			

				$sql = "UPDATE `transactions` SET `Phone` = '100' WHERE `transactions`.ID = '$item'";
				$this->core->database->doInsertQuery($sql);
			}
		}

		$sql = "SELECT * FROM `loan-information` WHERE `loan-information`.ID = '$bill' LIMIT 1";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$description = "Payment for " . $fetch['Name'];


			$sql = "UPDATE `loan-information` SET `Name` = CONCAT('SETTLED- ', `Name`),`Status`='Closed' WHERE `loan-information`.ID = '$bill';";
			$this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `balances` SET `Amount` = Amount-$amount, `LastUpdate` = NOW(), `LastTransaction` = '$description' WHERE `StudentID` = '$uid';";
			$this->core->database->doInsertQuery($sql);

			echo'<div class="successpopup">Loan SETTLED With payment of '.$amount.' ZMW , See payment overview</div>';

			echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center; width: 100%;">
			<b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/'. $uid .'">BACK TO PAYMENT OVERVIEW</a></b></div>';
			echo'<p><br></p>';
		}

	}
	
	
	public function printLoan($item) {
		
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $item, $uid, "Loan Contract printed $item ");

		$sql="SELECT a.ID, a.FirstName,a.MiddleName,a.Surname,a.MobilePhone,a.PrivateEmail,a.StreetName,a.Town,a.Status,
				b.ID as 'ID1',b.Name,b.Amount,b.Rate,b.StartDate,b.EndDate,b.DurationInMonths,c.Name as ColName,c.Value as ColValue 
				FROM `basic-information` a 
				INNER JOIN `loan-information` b ON a.ID=b.OwnerID 
				LEFT JOIN `loan-collateral` c ON c.ID=b.CollateralID
				WHERE b.ID=".$item;

		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		while ($row = $run->fetch_assoc()) {
			$results = TRUE;
			$firstname = $row['FirstName'];
			$middlename = $row['MiddleName'];
			$surname = $row['Surname'];
			$uid = $row['ID'];
			$streetname = $row['StreetName'];
			$town = $row['Town'];
			$phone = $row['MobilePhone'];
			$amount =  $row['Amount'];
			$client =  $row['FirstName'].' '.$row['MiddleName'].' '.$row['Surname'];
			$loanID = $row['ID1'];
			$description =  $row['b.Name'];
			$status =  $row['Status'];
			$rate=$row['Rate'];
			$durationInMonths=$row['DurationInMonths'];
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);
			$collateralName=$row['ColName'];
			$value=$row['ColValue'];
			$date=$row['StartDate'];
			$startDate=$row['StartDate'];
			$endDate=$row['EndDate'];
			$processFee=(3/100*$amount);
			$recievedAmount=$amount-$processFee;

			echo '<div id="print" style="word-wrap: normal|break-word|initial|inherit;"><div page-break-after:always;">';
			
			echo "
			<pre>
				<center>
				<h1>TRADING AGREEMENT</h1>

				<h1>BETWEEN</h1>

				<h1>I-Finance Zambia Limited</h1>

				<h2>AND<h2>

				<h1>$client Client ID: $uid</h1>
						   
				</center>
				
				
				
				
				
				
				
				
				
				
				
				
				
			</pre>

<h3>Disclaimer</h3>
<div style='word-wrap: normal|break-word|initial|inherit;'>
The pledged invoice/Purchase order shall not be used to obtain financing from any financial institution.The purchasing partner shall be liable for obtaining money by false pretences where such a practice shall be suspected. 
</div>
</br>
</br>
</br>
<div style='word-wrap: normal|break-word|initial|inherit;'>
This agreement is entered into on $date between <b>$client</b> and <b>I-FINANCE ZAMBIA LIMITED</b>, having its office at Room 8 Permanent House, Chingola Zambia, herein referred to as the Financing partner and $client herein referred to as the supplying partner of Kitwe.
</div>
				


<p>
<h3>NATURE OF AGREEMENT</h3>
<b>I-FINANCE ZAMBIA LIMITED</b> agrees to partner with <b>$client</b> for the procurement of the goods by <b>$client</b> of which goods once procured will be subsequently supplied to <b>$client</b>. <b>$client</b> has agreed to share a profit sum with Speed Capital Solutions as specified in this Contract.
</p>
<table border='1'>
	<tr>
		<th>Contracting Name</th>
		<th>Purchase order number</th>
		<th>Invoice number</th>
		<th>Invoice Amount</th>
		<th>Expected payment date</th>
	</tr>
	<tr>
		<td>$client</td>
		<td>$loanID </br> $StartDate</td>
		<td>$loanID </br> $StartDate</td>
		<td>".number_format($topay,2)." ZMW</td>
		<td>$endDate</td>
	</tr>
</table>

<p>
$client undertakes to repay speed capital immediately the proceeds/revenue have been received from the funded purchase orders/invoices stated in this agreement.
</p> 
<p>
<h2>AGREEMENT TERMS</h2>
<table border='1'>
	<tr>
		<th colspan='3' bgcolor='lightblue'><center>SECTION I : KEY TERMS</center></th>
	</tr>
	<tr>
		<td><b>Trading Amount (Capital contribution)</b> </br> ".number_format($amount,2)." ZMW</td>
		<td><b>Profit margin</b> </br> $rate % </td>
		<td><b>First Date</b> $startDate <br><b>Payment Due</b> $endDate</td>
	</tr>
	<tr>
		<td><b>Duration of Agreement</b> </br> $durationInMonths month(s)</td>
		<td><b>Processing Fee (3%)</b> </br>$processFee ZMW </td>
		<td><b>Payment frequency</b> monthly </td>
	</tr>
	<tr>
		<td><b>Amount Received </b></br> ".number_format($recievedAmount,2)." ZMW</td>
		<td><b>Total repayment </b> </br>".number_format( $topay,2)." ZMW </td>
		<td><b>Amount per Payment</b> <br>".number_format($topay,2)." ZMW 
			</br>Includes capital,interest and recurring fees
		</td>
	</tr>
</table>	
</p>	 			 	 


<h2>COLLATERAL</h2>
<ol>
<li><b>$client</b> shall produce collateral of the value higher than the total amount advanced under this agreement. Failure of settlement according to the agreement laid in this contract, and the collateral committed will be liquidated to recover the settlement amounts, penalties and any other costs incurred in relation to this agreement.
</li>
<li>The collateral pledged is: <b>$collateralName valued at ".number_format($value,2)." ZMW</b> </br>
<b>NOTE: $client</b> shall sign Contract of Sale Documents which will permit I-Finance Zambia Limited to dispose of the collateral upon breach of the contract in order to settle the amount due under this agreement.
</li>  
</ol>
<h2>NON COMPLIANCE</h2>
<p>
If $client defaults from the settlement plan as per the agreement they will be charged 3.33% penalty fee for non compliance after maturity of contract for every payment date foregone. 
If $client is unable to pay for the service rendered as per the agreement, either by mere default or due to bankruptcy, 30 days after maturity of the agreement, the supplying partner will thereby repossess the collateral submitted and/or take legal action against the same. 
</p>
<pre>
<b>
<b>This done and signed on behalf of the <b>I-Finance Zambia Limited</b> On $date

Signed………………………………………………………….. For and on behalf of I-Finance Zambia Limited.

Thus done and signed on behalf of the $client at $town on $date.
Signature:………………………………………

Name: …………………………………………

NRC: ………………………………………….

Residential Address………………………………………..

Cell:………………………………………………………..

Date: …………………………………………


<h2> Next of Kin</h>

Name: …………………………………………

NRC: ………………………………………….

Residential Address………………………………………..
</b>
</pre>";
		
			echo '</div></div>';


			$count++;
			$results = TRUE;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}


		echo'<script type="text/javascript">
			printContents = document.getElementById("print").innerHTML;
			document.body.innerHTML = printContents;
			window.print();
		</script>';


	}
	
	/******Lending Code Start ****/
	public function showlendLoan($item) {

		if($this->core->role > 10){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to Client Profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/loan/addlend/'.$item.'">Add Lending</a>
			</div>';
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `loan-lend-information` WHERE OwnerID= '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo 
		'<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
		<h2>Lending list</h2><br>
		<table width="" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="heading">' .
		'<td width=""><b>Trans ID</b></td>' .
		'<td width="200px"><b>Date/Time</b></td>' .
		'<td width=""><b>Collected</b></td>' .
		'<td width=""><b>To pay</b></td>' .
		'<td width=""><b>Rate %</b></td>' .
		'<td width=""><b>ClientID</b></td>' .
		'<td width=""><b>Description</b></td>' .
		'<td width=""><b>Status</b></td>' .
		'<td width="140px"><b>Management</b></td>' .
		'</tr>';

		$i = 0;

		while ($fetch = $run->fetch_assoc()) {
			$reverse = FALSE;


			if($fetch['Status'] == "Active"){
				$color = 'style="color: #00000;"';
			}

			if($fetch['Status'] == "Closed"){
				$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
			}
					
			$bid = $fetch['ID'];
			$uid =  $fetch['OwnerID'];
			$amount =  $fetch['Amount'];
			$topay =  $fetch['Amount'];
			$date =  '<b>From:</b> '.$fetch['StartDate'].' <b>To:</b>'.$fetch['EndDate'].' <b>'.$fetch['DurationInMonths'].'</b> Month(s)';
			$description =  $fetch['Name'];
			$status =  $fetch['Status'];
			$rate=$fetch['Rate'];
			$durationInMonths=$fetch['DurationInMonths'];
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);

			echo '<tr ' . $color . '>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/loan/printlend/'.$bid.'">LOAN-' . $bid . '</a></b></td>
				<td>' . $date . '</td>
				<td><b>' . number_format($amount,2) . ' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . number_format($topay,2) .' '.$this->core->conf['conf']['currency'].'</b></td>
				<td><b>' . $rate. '</b></td>
				<td>' . $uid. '</td>
				<td>' . $description . ' </td>
				<td>' . $status . ' </td>
				<td>'; 
				if ($status != 'Closed' ){
				
				echo'<a href="' . $this->core->conf['conf']['path'] . '/loan/detaillend/' . $fetch['ID'] . '?userid=' . $uid. '"> Details </a> 
				| <a href="' . $this->core->conf['conf']['path'] . '/loan/closelend/' . $fetch['ID'] . '/?userid=' . $uid. '"> Close</a> | 
				<a href="' . $this->core->conf['conf']['path'] . '/payments/addpayable/' . $fetch['ID'] . '/?type="lend"> Settle</a> |';
				
				echo'<a href="' . $this->core->conf['conf']['path'] . '/loan/deletelend/' . $fetch['ID'] . '/?userid=' . $uid. '">  Delete</a> ';
				}else if($this->core->role == 1000){
				 echo'<a href="' . $this->core->conf['conf']['path'] . '/loan/detaillend/' . $fetch['ID'] . '?userid=' . $uid. '"> Details </a> 
				 |<a href="' . $this->core->conf['conf']['path'] . '/loan/deletelend/' . $fetch['ID'] . '/?userid=' . $uid. '"> Delete</a> ';	
				}else{
					echo '<a href="' . $this->core->conf['conf']['path'] . '/loan/detail/' . $fetch['ID'] . '?userid=' . $uid. '"> Details </a>';
				}
			echo'</td></tr>';

			$total = $amount + $total;
			$totalToPay = $topay + $totalToPay;
		}


		echo '<tr class="heading"><td><b>Total Lent</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="7"><b>'. number_format($total,2) .'  '.$this->core->conf['conf']['currency'].'</b></td>'. 
		'<tr class="heading"><td><b>Total To be Paid</b></td>' .
		'<td width="60px"></td>' .
		'<td colspan="7"><b>'. number_format($totalToPay,2).'  '.$this->core->conf['conf']['currency'].'</b></td>' .

		'</tr>';

		echo '</table>';

	}
	public function deletelendLoan($item) {
		$ownerID=$_GET['userid'];
		$sql = 'DELETE FROM `loan_pro`.`loan-lend-information`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		//$this->core->redirect("information", "show", $item);
		if($run){
			echo '<b>DELETE Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/loan/showlend/'.$ownerID.'" >Back To Show Lending</a></li>';;
		}
	}
	public function closelendLoan($item) {
		$ownerID=$_GET['userid'];
		$sql = "UPDATE `loan_pro`.`loan-lend-information` SET `Status`='Closed'  WHERE `ID` = '" . $item ."'";
		$run = $this->core->database->doInsertQuery($sql);

		//$this->core->redirect("information", "show", $item);
		if($run){
			echo '<b>Close Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/loan/showlend/'.$ownerID.'" >Back To Show Lending</a></li>';
		}
	}
	
	public function addlendLoan($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$collateral = $select->showColluteral($item, null);
		
		include $this->core->conf['conf']['formPath'] . "addlendloan.form.php";
	}

	public function savelendLoan() {
		$ownerID = $this->core->cleanPost['ownerID'];
		$name = $this->core->cleanPost['name'];
		//$collateralID = $this->core->cleanPost['collateralID'];
		$rate = $this->core->cleanPost['rate'];
		$amount = $this->core->cleanPost['amount'];
		$startDate = $this->core->cleanPost['startDate'];
		$endDate = $this->core->cleanPost['endDate'];
		
		$start = new DateTime($startDate);
		$end   = new DateTime($endDate);
		$diff  = $start->diff($end);
		$durationInMonths= $diff->format('%y') * 12 + $diff->format('%m');
		//$durationInMonths =get_month_diff($startDate, $endDate);
		
		
		$sql = "INSERT INTO `loan_pro`.`loan-lend-information` (`Name`, `StartDate`,  `Rate`, `OwnerID`, `Amount`, `DurationInMonths`,`EndDate`, `Status`)
		VALUES ('$name','$startDate', '$rate', '$ownerID', '$amount', '$durationInMonths', '$endDate', 'Active');";
		//echo $sql;
		
		$run = $this->core->database->doInsertQuery($sql);
		
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $name, $uid, "Lend added Client $ownerID - $name");
		
		//$this->core->redirect("information", "show", $item);
		if($run){
			echo '<b>Save Success</b> </br><a href="' . $this->core->conf['conf']['path'] . '/loan/showlend/'.$ownerID.'" >Back to client lending list</a></li>';
		}
	}
	public function printlendLoan($item) {
		
		$uid = $this->core->userID;
		$this->core->audit(__CLASS__, $item, $uid, "Lend Contract printed $item ");

		$sql="SELECT a.ID, a.FirstName,a.MiddleName,a.Surname,a.MobilePhone,a.PrivateEmail,a.StreetName,a.Town,a.Status,a.GovernmentID,
				b.ID as 'ID1',b.Name,b.Amount,b.Rate,b.StartDate,b.EndDate,b.DurationInMonths
				FROM `basic-information` a 
				INNER JOIN `loan-lend-information` b ON a.ID=b.OwnerID 
				WHERE b.ID=".$item;

		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		while ($row = $run->fetch_assoc()) {
			$results = TRUE;
			$firstname = $row['FirstName'];
			$middlename = $row['MiddleName'];
			$surname = $row['Surname'];
			$governmentID = $row['GovernmentID'];
			$uid = $row['ID'];
			$streetname = $row['StreetName'];
			$town = $row['Town'];
			$phone = $row['MobilePhone'];
			$privateEmail = $row['PrivateEmail'];
			$amount =  $row['Amount'];
			$client =  $row['FirstName'].' '.$row['MiddleName'].' '.$row['Surname'];
			$loanID = $row['ID1'];
			$description =  $row['b.Name'];
			$status =  $row['Status'];
			$rate=$row['Rate'];
			$durationInMonths=$row['DurationInMonths'];
			$topay=round($amount*pow((1+$rate/100),$durationInMonths),2);
			$date=$row['StartDate'];
			$startDate=$row['StartDate'];
			$endDate=$row['EndDate'];
			$processFee=(3/100*$amount);
			$recievedAmount=$amount-$processFee;

			echo '<div id="print" style="word-wrap: normal|break-word|initial|inherit;"><div page-break-after:always;">';
			
			echo '
			<p style="text-align: center;"><u>TRADING AGREEMENT </u></p>
<p style="text-align: center;"><strong>I Finance Zambia Limited</strong></p>
<p style="text-align: center;"><strong>Made Between</strong></p>
<p style="text-align: center;"><strong><u>'.$client.'&nbsp;</u></strong>(Herein after called the lender)</p>
<p style="text-align: center;">Address<strong>&nbsp;:'. $streetname.'</strong></p>
<p style="text-align: center;">Contact number<strong>&nbsp;:</strong>&nbsp;<u>'.$phone.'</u>&nbsp; &nbsp; &nbsp;NRC/Incoperation #: <u>'.$governmentID.'</u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p>
<p style="text-align: center;">Email address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>: <u>'.$privateEmail.'</u></strong>&nbsp;Client ID #: <u>'.$uid.'</u></p>
<p style="text-align: center;"><strong>and</strong></p>
<p style="text-align: center;"><strong>I Finance Zambia Limited</strong> (Herein after called the Borrower) represented by <strong>Paul Steven Kasonde</strong></p>
<p style="text-align: center;">Address<strong>&nbsp;:</strong> Room 8, Permanent House, Kitwe Road, Chingola.</p>
<p style="text-align: center;">Contact number<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :0955 955 400 / 0966 875 653&nbsp;</strong>&nbsp;&nbsp;&nbsp; &nbsp;NRC: <strong>304399/51/1</strong></p>
<p style="text-align: center;">Email address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>: </strong>&nbsp;<a href="mailto:paul@speedcapital.org">paul@speedcapital.org</a> or <a href="mailto:jane@speedcapital.org">jane@speedcapital.org</a></p>
<p>&nbsp;</p>
<p><strong>THIS INDENTURE WITNESSTH AS FOLLOWS:</strong></p>
<p>The lender hereby demises unto the borrower a principal amount of&nbsp; '.number_format($amount,2).' ZMW&nbsp;<u> </u>for a definite period of&nbsp;'. $durationInMonths.' Months from '.$startDate.' to '.$endDate.'.</p>
<p>During this period, the following terms shall apply:&nbsp; &nbsp;</p>
<p><strong>The Borrower covenants with the Lender as follows:</strong></p>
<ol>
<li>To pay 8% interest of the principal per month payable on every 30<sup>th</sup> day of the calendar month.</li>
<li>Any monthly payment made later than 5 days after due date shall be liable to a charge of 0.267% for each day calculated on the principal and the same shall be payable to the lender.</li>
<li>To pay back the principal as and when the contract expires. The lender shall give notice of his/her intention to withdraw the principal.</li>
<li>At the expiration of the contract, the borrower shall consider the contract renewed if no written notice of renewal is received from the Lender.</li>
<li>The borrower shall give a month&rsquo;s notice if she/ he wishes to terminate the contract and shall hence pay back the principal and any outstanding monthly interest.</li>
<li>The Borrower reserves the right to revise the Interest Rate payable (as and when economic conditions so determine) by giving 30days notice to the Lender.</li>
</ol>
<p>&nbsp;</p>
<p><strong>The Lender here covenants with the borrower as followers:</strong></p>
<ol>
<li>That the Lender shall remit a principal amount at the stated date and shall not make any claims against the same as long as the borrower continues to pay the interest amount on a monthly basis.</li>
<li>The lender may increase the principal amount as and when he/she desires. However, the principal&nbsp; amount that he/ she can remit to the lender <strong>shall not</strong> be more than <strong>ZMW 500,000.00</strong>&nbsp;</li>
<li>The lender shall give a month&rsquo;s notice if she/ he wishes to terminate the contract.</li>
</ol>
<p><strong>IN WITNESS WHEREOF THE LENDER HAS SET HIS/HER HAND AND THE BORROWER HAS CAUSED IT TO BE FIXED ON THE DAY AND YEAR BEFORE WRITTEN.</strong></p>
<p><strong>Signed Sealed and Delivered this day of </strong>:&nbsp;'. $date.' .</p>
<p><strong><br /></strong>By the said lender: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </u>&nbsp;.<strong>Sign..............................................................</strong></p>
<p>&nbsp;</p>
<p><strong>Witness Name:&hellip;......................................&nbsp; Sign:.......................................... Address:..........................................</strong></p>
<p>&nbsp;</p>
<p><strong>NRC:......................................................................</strong></p>
<p>&nbsp;</p>
<p><strong>By the said Borrower </strong>: &nbsp;<strong><em>Paul S. Kasonde </em></strong><em>on behalf of<strong> Speed Capital Solutions Limited</strong></em> with National Registration Card #:&nbsp; <strong>304399/51/1&nbsp;&nbsp;</strong>&nbsp;</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
<p>Signature............................................................</p>';
		
			echo '</div></div>';


			$count++;
			$results = TRUE;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}


		echo'<script type="text/javascript">
			printContents = document.getElementById("print").innerHTML;
			document.body.innerHTML = printContents;
			window.print();
		</script>';


	}
}

?>
