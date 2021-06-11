<?php
class payrollBank {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

	/*
	 * Picking payroll entries
	 */
	public function runService($core) {
		$this->core = $core;
		//echo "Item  Service is running".$this->core->item;	
		
		if (isset($this->core->item)) {
			
			$item=$this->core->item;
						
			$sqlBnk = "SELECT ID,CONCAT(BankName,' ',Branch,'-',AccountNumber)as 'Name' FROM `payroll-bank-information` WHERE OwnerID='$item'";
			$runBnk = $this->core->database->doSelectQuery($sqlBnk);
			echo " <div class='label'>BankID :</div>
						<select name='BankID' id='BankID'style='width: 260px' required>";
			$i=0;
			while ($fetchBnk = $runBnk->fetch_assoc()){
				echo "<option selected value='".$fetchBnk['ID']."'>".$fetchBnk['Name']."</option>";
				$i++;
			}
			if($i==0){
				echo "<option selected value=''>Please add bank account (to user profile)</option>";
			}
			echo "</select><br />";
		}
	}
}

?>