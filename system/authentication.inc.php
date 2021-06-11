<?php
class auth {

	public $core;

	public function __construct($core) {
		$this->core = $core;
	}

	public function login() {
		$username = $this->core->cleanPost['username'];
		$password = $this->core->cleanPost['password'];

		if (isset($username) && isset($password)) {
			
			if (!$this->authenticateLDAP($username, $password)) {
				
				if (!$this->authenticateSQL($username, $password)) {
					return FALSE;
				} else {
					///UPDATE MD5 for moodle login temporary fix
					$sql = "UPDATE `access` SET `PasswordMD5` =MD5('$password') WHERE Username='$username';";
					$run = $this->core->database->doInsertQuery($sql);
					
					return TRUE;
				}
			}else {
				return TRUE;
			}

		} else {
			$this->core->setViewError('Please enter all fields', 'Please <a href=".">return to the login page</a> and try again.');
			$this->core->builder->initView("error");
			return FALSE;
		}

		return FALSE;
	}

	private function authenticateLDAP($username, $password) {
		if ($this->core->conf['ldap']['ldapEnabled'] == TRUE && function_exists('ldap_connect')) {
			if (is_numeric($username)) {
				$ou = $this->core->conf['ldap']['studentou'];
			} else {
				$ou = $this->core->conf['ldap']['staffou'];
			}

			$ldapconn = ldap_connect($this->core->conf['ldap']['server'], $this->core->conf['ldap']['port']);
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

			$ldapbind = ldap_bind($ldapconn, "uid=" . $username . "," . $ou, $password);

			if ($ldapbind) {
				$this->core->logEvent("User '$username' authenticated successfully", "4");
				return $this->authenticateAccess($username, $password);
			} else {	
				$this->core->logEvent("User '$username' authentication failed", "4");
				return FALSE;
			}

		} else {
			$this->core->logEvent("PHP-LDAP module missing or not enabled", "1");
		}
		
		return FALSE;
	}

	private function authenticateAccess($username, $password, $nologin = FALSE) {
	
		$passwordHashed = $this->hashPassword($username, $password);

		$sql = "SELECT RoleID FROM `access` LEFT JOIN `roles` ON `access`.`RoleID` = `roles`.`ID` WHERE Username = '$username'";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0) {

			$this->core->logEvent("User '$username' not present in ACCESS table, perhaps added through LDAP, now adding", "3");

			if (is_numeric($username)) {
				$roleID = "10";
				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$username', '$username', '$roleID', '$passwordHashed');";
			} else {
				$roleID = "101";
				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES (NULL, '$username', '$roleID', '$passwordHashed');";
				$this->core->database->doInsertQuery($sql);
			}

			$this->core->database->doInsertQuery($sql);

		}

		$sql = "SELECT `access`.RoleID, `access`.ID FROM `access` LEFT JOIN `roles` ON `access`.`RoleID` = `roles`.`ID` WHERE Username = '$username'";
		$mbs = $this->core->database->doSelectQuery($sql);

		while ($row = $mbs->fetch_assoc()) {

			$roleID = $row['RoleID'];
			$userID = $row['ID'];
			$rolename = $this->role($roleID);

			$sql = "SELECT * FROM `basic-access` WHERE `ID` = '$userID'";
			$mkb = $this->core->database->doSelectQuery($sql);
	
			if ($mkb->num_rows == 0) {
				$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) 
					VALUES (NULL, NULL, NULL, NULL, NULL, '$id', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Employed');";

				$this->core->database->doInsertQuery($sql);
			}

		}


		return $this->authenticateSession($username, $password, $userID, $roleID, $rolename, FALSE);

	}

	public function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}

	private function authenticateSQL($username, $password, $nologin = FALSE) {
	
		$passwordHashed = $this->hashPassword($username, $password);
		$passwordOldHashed = md5($password);

		$old = FALSE;
		$sql = "SELECT `access`.Password FROM `access` WHERE `access`.Username = '$username' AND `access`.Password = ''";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$old = TRUE;
		}

		$sql = "SELECT * FROM `access` WHERE `access`.Username = '$username'";
		$run = $this->core->database->doSelectQuery($sql);
		if ($run->num_rows ==  0) {
			$old = TRUE;
		}

		if($old == FALSE){
			$sql = "SELECT access.ID as UserID, RoleID, `Status`
			FROM `access` 
			LEFT JOIN `basic-information` ON `basic-information`.`ID`=`access`.`Username` 
			WHERE `access`.Username = '$username' 
			AND `access`.Password = '$passwordHashed'";
			$run = $this->core->database->doSelectQuery($sql);
		}else{
			
			$exit = TRUE;
			//$isNotStaff=FALSE;
			// STUDENT DETAILS
			//$sql = "SELECT access.ID as UserID, RoleID, `Status` FROM `access`
			//LEFT JOIN `basic-information` ON `basic-information`.`ID`=`access`.`Username`
			//LEFT JOIN `users` ON `basic-information`.`ID`=`users`.`username` AND `users`.password = '$passwordOldHashed'
			//WHERE `access`.Username = '$username'";
			//$run = $this->core->database->doSelectQuery($sql);
			//while ($row = $run->fetch_assoc()) {
			//	$status = $fetch["Status"];
			/*	$sqlx = "UPDATE `access` SET `Password` = '$passwordHashed',`RoleID`=10 WHERE `Username` = '$username';";
				$runx = $this->core->database->doSelectQuery($sqlx);
				
				
				$isNotStaff=TRUE;
				//	Cleaning ACCESS table
					$sqlDel = "DELETE FROM `access` WHERE RoleID < 10 ";
					//echo $sqlx;
					$runDel = $this->core->database->doSelectQuery($sqlDel);
				
				$exit = FALSE;
			}
			
			if ($isNotStaff==FALSE){
				// JOOMLA CREDENTIALS
				$sql = "SELECT `basic-information`.ID as UserID, `jos_users`.password,`staff`.`schoolID`, `basic-information`.Status
				FROM `basic-information`, `jos_users`, `staff`
				WHERE `basic-information`.`ID`=`jos_users`.`id`
				AND `basic-information`.`ID`=`staff`.`EmployeeNo`
				AND `jos_users`.username =  '$username'";
				
				$roleID=null;
				$run = $this->core->database->doSelectQuery($sql);
				while ($fetch = $run->fetch_assoc()) {
					$dbpassword = $fetch["password"];
					$uid = $fetch["UserID"];
					$schoolID= $fetch["schoolID"];
					$status = $fetch["Status"];
					///added for security role  
					if($schoolID!=null || !empty($schoolID)){
						$roleID='104';
					}else{
						$roleID='106';
					}
					
					$hashparts = explode(':', $dbpassword);
		
					$userhash = md5($password.$hashparts[1]);
				}


				if($userhash == $hashparts[0]){
					$sqlx = "INSERT INTO `access` (`ID`,`Username`, `RoleID`, `Password`) VALUES ('$uid', '$username', '$roleID', '$passwordHashed')
					 ON DUPLICATE KEY UPDATE  `Password` = '$passwordHashed' AND `Username` = '$username';";
					//echo $sqlx;
					
					$runx = $this->core->database->doSelectQuery($sqlx);
					$exit = FALSE; */
				//} else {
				//	Cleaning ACCESS table
					//$sqlDel = "DELETE FROM `access` WHERE RoleID < 10 ";
					//echo $sqlx;
					//$runDel = $this->core->database->doSelectQuery($sqlDel);
					//$exit = TRUE;
				//}
			//}
		}


		$sqld = "INSERT INTO `login` (`ID`, `UserID`, `LastLogin`, `LastLogout`) VALUES (NULL, '$username', NOW(), NOW()) ON DUPLICATE KEY UPDATE `LastLogin` = now();";
		$rund = $this->core->database->doSelectQuery($sqld);

		if ($run->num_rows > 0 || $exit != TRUE) { //successful login
			$this->core->logEvent("User '$username' authenticated successfully", "4");
			
			while ($row = $run->fetch_assoc()) {
				$userID = $row['UserID'];
				$role = $row['RoleID'];
				$status = $row["Status"];
				$rolename = $this->role($role);

							
				if(empty($role) || $role==0) {
					// User does not have any permissions
					$this->core->setViewError('Unauthorized access', "You do not have permissions to access this system, please contact the academic office", "LOGIN");
					$this->core->builder->initView("fault");
					return FALSE;
				}


				if($status == "Expelled" || $status == "Removed" || $status == "Deleted" || $status == "Suspended" || $status == "Blocked") {
					// User does not have any permissions
					$this->core->setViewError('Unauthorized access', "You do not have permissions to access this system, please contact the academic office", "LOGIN");
					$this->core->builder->initView("fault");
					return FALSE;
				}

			}
			
		} else {
			$this->core->logEvent("User '$username' authentication failed", "2");
			return FALSE;
		}

		return $this->authenticateSession($username, $password, $userID, $role, $rolename, $nologin);
	}

	private function authenticateSession($username, $password, $userID, $role, $rolename, $nologin = FALSE) {

		if($role == 1001){
			$role = 1000;
		}

		if(isset($username, $password, $userID, $role, $rolename) && $nologin == FALSE){
		
			$_SESSION['path'] = $this->core->conf['conf']['path'];
			$_SESSION['userid'] = $userID;
			$_SESSION['username'] = $username;
			$_SESSION['password'] = $password;
			$_SESSION['role'] = $role;
			$_SESSION['rolename'] = $rolename;

			$_SESSION['saobjects'] = $this->getStudyInformation($userID);

			$this->core->setPath($this->core->conf['conf']['path']);
			$this->core->setUsername($username);
			$this->core->setUserID($userID);
			$this->core->setRoleName($rolename);
			$this->core->setRole($role);

			return TRUE;
		}else if($nologin == TRUE){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getStudyInformation($userID){
		$sql = "SELECT `st`.Name,  `st`.ShortName, ProgramName, `sc`.Name FROM `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl, `programmes` as pr, `schools` as sc, `basic-information` as bi
		WHERE ac.`ID` = '$userID' AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`major` = pr.`id`
		OR  ac.`ID` = '$userID'  AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`minor` = pr.`id`";

		$run = $this->core->database->doSelectQuery($sql);

		return $run->fetch_array(MYSQLI_NUM);
	}

	public function ldapChangePass($username, $oldpass, $newpass) {

		if (function_exists('ldap_connect')) {

		// Select correct organizational unit from LDAP tree configuration
		if ($this->core->role > 1) {
			$ou = $this->core->conf['ldap']['staffou'];
		} elseif ($this->core->role > 10) {
			$ou = $this->core->conf['ldap']['adminou'];
		} else {
			$ou = $this->core->conf['ldap']['studentou'];
		}

		try {
			$ldapconn = ldap_connect($this->core->conf['ldap']['server'] . "s", $this->core->conf['ldap']['port']);
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

		} catch (Exception $e) {

			$this->core->logEvent("Could not connect to LDAP server.", "1");
			$this->core->throwError("Could not connect to LDAP server.");
		}

		try {
			ldap_bind($ldapconn, "uid=" . $username . "," . $ou, $oldpass);

			$userpassword = "{SHA}" . base64_encode(pack("H*", sha1($newpass)));
			if (ldap_mod_replace($ldapconn, "uid=" . $username . "," . $ou, array('userpassword' => $userpassword))) {
				echo "<p><h2>YOUR PASSWORD IS NOW CHANGED</h2></p>";
				$this->core->logEvent("User '$username' changed password", "4");
				return TRUE;
			} else {
				return FALSE;
			}

		} catch (Exception $e) {
			$this->core->logEvent("Could not bind to LDAP server using user credentials", "1");
			$this->core->throwError("Could not bind to LDAP server using user credentials.");
		}

		} else{

			return FALSE;
		}

	}

	public function getUsername($item) {
		$sql = "SELECT * FROM `access` WHERE `ID` = $item";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return $fetch['Username'];
	}

	public function getUserID($item) {
		$sql = "SELECT * FROM `access` WHERE `Username` = '$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return $fetch['ID'];
	}


	public function mysqlChangePass($username, $oldpass, $newpass, $admin) {

		if(!is_numeric($username)){
			$id = $this->getUserID($username);
		} else {
			$id = $username;
			$username = $this->getUsername($id);
			if(empty($username)){
				$username = $id;
			}
		}

		if($admin != TRUE){
			if(!$this->authenticateSQL($username, $oldpass)){
				return false;
			}
		}

		$password = hash('sha512', $newpass . $this->core->conf['conf']['hash'] . $username);

		$sql = "UPDATE `access` SET `Password` = '$password' WHERE `ID` = '$id'";
		$run = $this->core->database->doInsertQuery($sql);


		if($this->core->database->mysqli->affected_rows == 0){	
			$roleID = "10";
			$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$id', '$username', '$roleID', '$password');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		if ($run) {
			if($this->authenticateSQL($username, $newpass, TRUE)){
				$this->core->logEvent("User '$username' changed password", "4");
				$this->core->throwSuccess("Your password has been changed! The next time you log-in you will need to use your new password.");
				return TRUE;
			}
		} else {
			return false;
		}
	}

	private function role($access) {
		$sql = "SELECT * FROM `roles` WHERE ID LIKE '$access'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			return $row['RoleName'];
		}
	}


	function logout() {
		$username = $this->core->username;

		session_destroy();
		
		$this->core->setPath(NULL);
		$this->core->setUsername(NULL);
		$this->core->setUserID(NULL);
		$this->core->setRoleName(NULL);
		$this->core->setRole(NULL);

		$ip = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}
		
		$sql = 'UPDATE acl SET `status`="LOGOUT" WHERE `user` = "'.$username.'" AND `ip` = "'.$ip.'" AND `date` = CURDATE()';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->setPage(NULL);
	}
}

?>