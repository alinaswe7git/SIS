<?php
class security {

	public $core;
	public $view;
	public $item = NULL;


	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRaUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}


	public function buildView($core) {
		$this->core = $core;
	}


	public function encrypt($plaintext){
		$password = 'NipaEXAM';
		$method = 'aes-256-cbc';
	
		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		//$iv = "4e5Wa71fYoT7MFEX";
		$encrypted = base64_encode(openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv));
		return $encrypted;	
	}	



	public function decrypt($plaintext){
		$password = 'NipaEXAM';
		$method = 'aes-256-cbc';
	
		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		//$iv = "4e5Wa71fYoT7MFEX";
		$decrypted = openssl_decrypt(base64_decode($encrypted), $method, $password, OPENSSL_RAW_DATA, $iv);
		return $decrypted;	
	}


	public function qrSecurity($item, $sid, $courses, $name, $balance){

		$data = $sid . "-" . date('Y-m-d') . "-" . rand(10000,99999);
		//$item = $data;
		$admin = $this->core->userID;
		

		

		require_once $this->core->conf['conf']['libPath'] . 'phpqrcode/qrlib.php';
 
		$path = "datastore/output/secure/";


		$output["ID"] = $sid;
		$output["N"] = $name;
		$output["C"] = $courses;
		$output["B"] = $balance;

		//$output["domain"] = $this->core->conf['conf']['domain'];
		//$content = "https://edurole.mu.ac.zm/secure/" . $file;

		
		$content = json_encode($output);
		//$content = $this->encrypt($content);
		
		//$svgCode = QRcode::svg($content);
		$item = md5($data);
		$filename= $path.$item.'.png';
		
		QRcode::png($content, $filename, QR_ECLEVEL_L, 3); 
		
		$sql = "INSERT INTO `security` (`ID`, `Data`, `File`, `Date`, `StudentID`, `Creator`) VALUES (NULL, '$content', '$data', NOW(), '$sid', '$admin')";
		$this->core->database->doInsertQuery($sql); 
		
		return $filename;

	}
	
	
}

?>