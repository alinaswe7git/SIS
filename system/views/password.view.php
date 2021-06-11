<?php
class password {

	public $core;
	public $view;

	public function buildView($core) {
		$this->core = $core;
	}

	public function viewPassword(){

		$sql = "SELECT `Username`, `Password` FROM `access` WHERE `RoleID` > 10";
		$passd = '12345';
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$username = $fetch['Username'];
			$pass  = $fetch['Password'];
			$sd = $this->hashPassword($username, $passd);

			if($sd == $pass){
				echo $username . '<br>';
			}
		}

	}
	
	public function updatePassword(){

		$sql = "SELECT `ID`,`Username`, `Password` FROM `access` WHERE `RoleID` = 10 AND`Password` ='' ";
		$passd = '12345';
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$id = $fetch['ID'];
			$username = $fetch['Username'];
			$pass  = $fetch['Password'];
			$sd = $this->hashPassword($username, $passd);
			/*
			if($sd == $pass){
				echo $username . '<br>';
			}
			*/
			$sqlUpdate="UPDATE access SET `Password`='".$sd."' WHERE `ID` =".$id;
			echo $username . ' Updated <br> ';
			$runUpdate = $this->core->database->doInsertQuery($sqlUpdate);
		}

	}

	public function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}



	private function viewMenu(){
		if($this->core->role == 0){
                	echo '<div class="collapse navbar-collapse  navbar-ex1-collapse">
                	<ul class="nav navbar-nav side-nav">
                	<li class="active"><strong>Home menu</strong></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '">Home</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/studies">Overview of all studies</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake">Studies open for intake</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/register">Current student registration</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/password/recover">Recover lost password</a></li>
                	</ul><div id="page-wrapper">';
		}
	}

	private function generatePassword($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz0123456789') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	public function changePassword($item) {

		if($this->core->role == 1000){
			if(empty($item)){
				$item = $this->core->username;
			}			
			$admin = TRUE;
		}else {
			$item = $this->core->username;
		}

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$auth = new auth($this->core);
		
		if (!empty($newpass)) {

			if ($newpass == $newpasscheck) {

				if ($auth->ldapChangePass($item, $oldpass, $newpass) == false) {
					$ldap = false;
				}
				if ($auth->mysqlChangePass($item, $oldpass, $newpass, $admin) == false && $ldap == false) {
					$this->core->throwError("The information you have entered is incorrect.");
				}

			} else {
				echo "<h2>The entered passwords do not match</h2>";
			}

		} else {

			echo "<p>Please remember to enter all fields!</p>";
			include $this->core->conf['conf']['formPath'] . "changepass.form.php";

		}
	}

	private function randomPassword() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890#@!';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	public function resetPassword($item) {
		$admin = TRUE;
		$newpass = "12345";

		$auth = new auth($this->core);
	

		$sql = "SELECT * FROM `access` WHERE `ID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($fetch = $run->fetch_assoc()) {
			$role = $fetch['RoleID'];
		}
		
		if($role <= 10 || $this->core->role == 1000){
			if($role > 10){
				$newpass = $this->randomPassword();
			}

			if ($auth->mysqlChangePass($item, $oldpass, $newpass, $admin) == false) { }
	
			echo '<div class="successpopup">Password Changed to '.$newpass.'</div>';
		} else {
			echo '<div class="errorpopup">You do not have the rights to change this password</div>';
		}
	}


	public function recoverPassword() {
		if(isset($this->core->cleanPost['username'])){
		//if(isset($this->core->cleanPost['uid']) && isset($this->core->cleanPost['username'])){
			$uid = $this->core->cleanPost['uid'];
			$username = $this->core->cleanPost['username'];

			$sql = "SELECT * FROM `basic-information`, `access`
				WHERE `basic-information`.`ID` = `access`.ID 
				AND `access`.Username = '$username'";
				//AND `basic-information`.`GovernmentID` LIKE '%".$uid."%'";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {
				$found = TRUE;
				$this->core->throwSuccess("Your records were found");
				$phone = $fetch[14];

				if(isset($phone)){
					include $this->core->conf['conf']['viewPath'] . "sms.view.php";
					$sms = new sms($this->core);
					$sms->buildView($this->core);

					$password = $this->generatePassword(5);
					$passenc = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);

					$sql = "UPDATE `access` SET `Password` = '$passenc' WHERE `Username` = '$username';";
					$this->core->database->doInsertQuery($sql);

					$destination = $fetch[17];
					$name = $fetch[0] . ' ' . $fetch[1] .' ' . $fetch[2];
					/*
					if (filter_var($destination , FILTER_VALIDATE_EMAIL)) {

						$subject = 'EduRole - Password Reset';
						$content = '<img src="https://edurole.mu.ac.zm/templates/default/images/edurole-mail.png"><p>
							Dear user, <br> <br> Your new password is <span style="font-size: 14pt"><b>'.$password.'</b></span><br>
							</p>Kind Regards,<br><b>MU</b>';

						include $this->core->conf['conf']['classPath'] . "mailer.inc.php";
						$mailer = new mailer($this->core);

						if($mailer->sendMail($destination, $name, $subject, $content, $attachment) == TRUE){
							$this->core->throwSuccess("Your new password was sent by EMAIL to $destination");
						}

					} else {*/
						$sms->directSms($phone, "Your new password is: $password",$username);
						$this->core->throwSuccess("Your new password was sent by SMS to phone number: $phone");
					//}

					$this->core->throwSuccess("Please <a href=\"".$this->core->conf['conf']['path']."/\">log in</a> with the new password");
				} else {
					$this->core->throwError("Report to the academic office you do not have a phone number to send a password to.");
				}
			}

			if($found != TRUE){
				$this->core->throwError("NRC or Student number do not match");
				include $this->core->conf['conf']['formPath'] . "recoverpassword.form.php";	
			}
		}else {
			include $this->core->conf['conf']['formPath'] . "recoverpassword.form.php";
		}
	}
}
?>
