<?php
session_start();

class installer {

	public function install(){
		$configFile = "../system/config.inc.php";
		$core = "../system/core.inc.php";

		if(file_exists($configFile)){
			require_once $configFile;
		} else {
			echo "Please restore the base configuration file";
		}

		if(file_exists($core)){
			require_once $core;
		} else {
			echo "The EduRole core could not be loaded";
		}

		$this->core = new eduroleCore($conf, FALSE);

		$this->cssFiles = '<link href="../templates/edurole/css/style.css" rel="stylesheet" type="text/css" />';
		require_once "../templates/edurole/header.inc.php";

		echo'<div class="contentpadfull"> <form action="?save=true" method="post">
		<div class="title">Welcome to EduRole</div>
		<ul>
			<li>PHP dependencies: php5-ldap, php5-imap, php5-gd</li>
			<li>Apache dependencies: mod_rewrite</li>
		</ul>';
		
		if (!extension_loaded('gd', 'gd2')) {
			$this->core->throwError("The php5-gd extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('ldap')) {
			$this->core->throwError("The php5-ldap extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('mysql')) {
			$this->core->throwError("The php5-mysql extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('imap')) {
			$this->core->throwError("The php5-imap extension is either not installed or not loaded, try installing it!");
		}

		$this->core->throwSuccess("Please enter the following information");

		echo'<div>';
		require_once "configNamer.inc.php";

		$this->core->throwSuccess("General configuration");
		foreach($conf["conf"] as $name=>$value){
			if($fullname["conf"][$name][1]=="text"){ $input = '<input type="text" name="conf[\'conf\'][\''.$name.'\']" value="'.$value.'">'; 
			}elseif($fullname["conf"][$name][1]=="select"){ $input = '<select name="conf[\'conf\'][\''.$name.'\']"> <option value="TRUE">ON</option> <option value="FALSE">OFF</option> </select>'; }

			echo'<label for="'.$name.'">'.$fullname["conf"][$name][0].'</label>'.$input.'<br/>';
			if($name == "path"){ break; }
		}
		
		$this->core->throwSuccess("MySQL server configuration");
		foreach($conf["mysql"] as $name=>$value){
			if($fullname["mysql"][$name][1]=="text"){ $input = '<input type="text" name="conf[\'mysql\'][\''.$name.'\']" value="'.$value.'">'; 
			}elseif($fullname["mysql"][$name][1]=="select"){ $input = '<select name="conf[\'mysql\'][\''.$name.'\']"> <option value="TRUE">ON</option> <option value="FALSE">OFF</option> </select>'; }

			echo'<label for="'.$name.'">'.$fullname["mysql"][$name][0].'</label>'.$input.'<br/>';
		}
		
		$this->core->throwSuccess("LDAP server configuration");
		foreach($conf["ldap"] as $name=>$value){
			if($fullname["ldap"][$name][1]=="text"){ $input = '<input type="text" name="conf[\'ldap\'][\''.$name.'\']" value="'.$value.'">'; 
			}elseif($fullname["ldap"][$name][1]=="select"){ $input = '<select name="conf[\'ldap\'][\''.$name.'\']"> <option value="TRUE">ON</option> <option value="FALSE">OFF</option> </select>'; }
			
			echo'<label for="'.$name.'">'.$fullname["ldap"][$name][0].'</label>'.$input.'<br/>';
		}
		
		$this->core->throwSuccess("Mail configuration");
		foreach($conf["mail"] as $name=>$value){
			if($fullname["mail"][$name][1]=="text"){ $input = '<input type="text" name="conf[\'mail\'][\''.$name.'\']" value="'.$value.'">'; 
			}elseif($fullname["mail"][$name][1]=="select"){ $input = '<select name="conf[\'mail\'][\''.$name.'\']"> <option value="TRUE">ON</option> <option value="FALSE">OFF</option> </select>'; }
			
			echo'<label for="'.$name.'">'.$fullname["mail"][$name][0].'</label>'.$input.'<br/>';
		}

		$this->core->throwSuccess("Write your configuration to file!");
		echo'<label for="submit"> </label><input type="submit" value="Save settings"> </div></form>';

		clearstatcache();
		require_once "../templates/edurole/footer.inc.php";
	}
	
	public function save(){
		// Write changes to configuration
		$config = "tmp/config.inc.php";
		$fh = fopen($config, 'w+') or die("can't open configuration file");
		$start = "<?php \n // Automated configuration file\n";
		fwrite($fh, $start);
		foreach ($_POST as $key => $value){
			foreach ($value as $man => $dds){
				foreach ($dds as $sls => $val){
					$rule = '$conf['. $man .']['.$sls.'] = "'.$val.'";' . "\n";
					fwrite($fh, $rule);
				}
			}
		}
		$finish = "?>";
		fwrite($fh, $finish);
		fclose($fh);

		require_once "tmp/config.inc.php";
		
		// Set users password
		$username = "admin";
		$hash = $conf['conf']['hash'];
		$password = $conf['mysql']['password'];
		$passenc =  hash('sha512', $password . $hash . $username);
		
		require_once "../system/core.inc.php";
		$this->core = new eduroleCore($conf, FALSE);
		
		// Loading database
		if($this->createDB($conf["mysql"]["server"], $conf["mysql"]["user"], $conf["mysql"]["password"], $conf["mysql"]["database"], $passenc)){
			$this->core->throwSuccess("System has been installed. Your kingdom <a href=\"../\">awaits!</a>");
			system('mysql -u '.$conf["mysql"]["user"].' -p '.$conf["mysql"]["password"].' '.$conf["mysql"]["server"].' < sql/edurole.sql');
		} else {
			$this->core->throwError("An error occurred connecting to the database");
		}
		
		unset($this->core);
		
		// Base configuration completed, restarting core
		require_once "../system/database.inc.php";
		require_once "../system/core.inc.php";
		$this->core = new eduroleCore($conf, FALSE);
		
		// Finished
		$this->core->throwSuccess("System has been installed. Your kingdom <a href=\"../\">awaits!</a>");
		
	}
	
	public function createDB($host,$user,$pass,$db){
		$con=mysqli_connect($host,$user,$pass);
		// Check connection
		if (mysqli_connect_errno())	{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$sql="CREATE DATABASE $db";
		if (mysqli_query($con,$sql)){
			$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES (100001, 'admin', 1000, '$passenc');";
			mysqli_query($con,$sql);
			return TRUE;
		} else {
			return FALSE;
			echo "Error creating database: " . mysqli_error($con);
		}
	}
}

$installer = new installer();
if(isset($_GET['save'])){
	$installer->save();
} else {
	$installer->install();
}
?>