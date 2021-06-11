<?php
class payrollItems {

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
		
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$ItemNameList = $select->showPayrollListItems($item, null);
		
		if (isset($this->core->item)) {
			
			$item=$this->core->item;
			
			for ($i=1; $i<=$item;$i++){
			
				echo "<h3> Item  No.".$i."</h3>";
				echo " <div class='label'>Name:</div>
					   <input list='ItemName$i' name='ItemName$i' style='width: 260px' required/>
					   <datalist id='ItemName$i'>
						   $ItemNameList
						</datalist><br />";
						
				echo " <div class='label'>Amount:</div>
					   <input type='number' name='Amount$i' style='width: 260px' required/><br />";
				
				echo " <div class='label'>Type:</div>
						<select name='Type$i' id='Type$i'style='width: 260px' required>
							<option selected value='Income'>Income</option>
							<option value='Deduction'>Deduction</option>
						</select><br />";
						
				echo "</br></br></br>";
			
			}
		}
	}
}

?>