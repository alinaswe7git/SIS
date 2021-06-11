<?php
class login {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

	public function runService($core) {
		$this->core = $core;
		$username = $_GET['username'];
   		$password = $_GET['password'];
		$domain = $_GET['domain'];

		$login = $this->authenticate($username,$password);

		echo json_encode($login);
	}

	public function generateKey($length, $charset = 'abcdefghijklmnopqrstuvwxyz23456789') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	private function authenticate($username, $password, $nologin = FALSE) {
	
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);

		$sql = "SELECT access.ID as UserID, RoleID, FirstName, Surname, `basic-information`.Status, ClientID
			 FROM `access` 
			LEFT JOIN `basic-information` ON `basic-information`.`ID` = `access`.`ID` 
			LEFT JOIN `tablet-list` ON `basic-information`.`ID` = `tablet-list`.`ClientID` 
			WHERE `access`.Username = '$username' 
			AND `access`.Password = '$passwordHashed'";

		$run = $this->core->database->doSelectQuery($sql);
		
		if ($run->num_rows > 0) { //successful login
			$this->core->logEvent("User '$username' authenticated successfully", "4");
			
			while ($row = $run->fetch_assoc()) {
				$userID = $row['UserID'];
				$role = $row['RoleID'];
				$status = $row['Status'];
				$name = $row['FirstName'] . ' ' .  $row['Surname'];

				$key = $this->generateKey(16);

				$tablet = $row['ClientID'];


				$output[0] = "AUTHORIZED";
				$output[1] = $userID;
				$output[2] = $role;
				$output[3] = $name;
				$output[4] = $status;
				$output[5] = $key; 
				$output[6] = $userID.'@mu.edu.zm';
				if($tablet != ''){
					$output[7] = 'TABLET';
				}


				$sqlx = "INSERT INTO `authentication` (`ID`, `StudentID`, `Login`, `Key`) VALUES (NULL, '$userID', NOW(), '$key')";
				$this->core->database->doInsertQuery($sqlx);
							
				if(empty($role) || $role==0) {
					$output = 'ACCESS DENIED - Your access to the system has been denied. Please contact ICT.';
				}

				if($status == "New" || $status == "Approved" || $status == "Requesting" || $status == "Employed" ||  $status == "Applying"){
					
				} else {
					$output = 'ACCESS DENIED - Your access to the system has been denied. Please contact ICT.';
				}
			}
			
		} else {
			$output = 'ACCESS DENIED - Your access to the system has been denied. Please contact ICT.';
			return $output;
		}

		return $output;
	}
}

?>