<?php
class sms {

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

	private function viewMenu(){
		if($this->core->role = 1000){
				echo '<div>SMS(es) Balance: <b>'.$this->unitsSms().'</b></div>';
		}
		
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/information/search">Send a bulk SMS message</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/approve/all">Approve all SMS</a>'.
		'</div>';
	}

	public function unitsSms(){

		$api_key='0387f1cb8d434b0665dce7e9f8881e05';
		$sender_id='NIPA';
		$url = 'https://bulksms.zamtel.co.zm/api/sms/balance?key='.$api_key;
	
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => FALSE,
		  CURLOPT_SSL_VERIFYPEER => FALSE,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Content-Type: application/json"
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  //echo "cURL Error #:" . $err;
		} else {
		  //echo $response;
		}
		
		$output = json_decode($response);
	
		$units=0;
		foreach($output as $arr){
			
			foreach($arr as $item => $it){
				
				if($item == "sms_balance"){
					$units = $it;					
				}elseif($item == "account_name"){
					$account = $it;
				}
			} 
			/*
			if($arr == "sms_balance"){
				$units = $val;
			}elseif($arr == "account_name"){
				$account = $val;
			}
			*/
			//echo  $units;
		}
		return $units;
	}

	public function manageSms() {
		$this->viewMenu();
		$userid = $this->core->userID;

		if($this->core->role = 1000){
			$sql = "SELECT * FROM `sms` LEFT JOIN `basic-information` ON `sms`.Author = `basic-information`.ID WHERE `sms`.Author = '$userid' ORDER BY Date DESC";
		} else {
			$sql = "SELECT * FROM `sms` LEFT JOIN `basic-information` ON `sms`.Author = `basic-information`.ID ORDER BY Date DESC";
		}

		$run = $this->core->database->doSelectQuery($sql);
		
		

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Message</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Targeted</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Delivered</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[0];
			$date = $row[1];
			$message = $row[2];
			$author = $row[3];
			$sent = $row[4];
			$received = $row[5];
			$status = $row[6];
			$guids = $row[7];

			$firstname = $row[8];
			$lastname = $row[10];

			if($status == "Awaiting approval"){
				$status = '<b><a onClick="SMS=window.open(\''. $this->core->conf['conf']['path'] .'/sms/approve/'.$id.'\',\'SMS\',\'width=600,height=300\'); return false;" href="#">Awaiting approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/sms/delete/'.$id.'">Delete message</a> <br>
					Author: ' . $firstname . ' ' . $lastname;
			} else {
				$status =  '<b>' . $status . '</b><br> Author: ' . $firstname . ' ' . $lastname;
			}
			

			$sent = substr_count($sent, ',')+1;

			
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$date.'</td>
				<td> '.$message.'</td>
				<td> '.$sent.'</td>
				<td> '.$received.'</td>
				<td> '.$status.'</td>
				</tr>';
			$results = TRUE;


		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}
		}


		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}

	}

	public function newSms($item) {
		$celphone = $item;

		$length = strlen((string)$celphone);
		if($length == 10) {
			$recipients = $celphone;
		}elseif($length == 9) {
			if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celphone; }
			$recipients = $celphone;
		}else if($length == 12) {
			$celphone = substr($celphone, 2);
			$recipients = $celphone;
		}

		include $this->core->conf['conf']['formPath'] . "newsms.form.php";
	}

	public function newbulkSms() {
		$sql = $_SESSION["recipients"];
		$run = $this->core->database->doSelectQuery($sql);

		$prefix = "26";
		//$recipients = "0978614927";
		//$guids = "2010226397";

		while ($row = $run->fetch_assoc()) {
			$firstname = $row['FirstName'];
			$lastname = $row['LastName'];
			$uid = $row['ID'];
			$status = $row['Status'];
			$celphone = $row['MobilePhone'];

			if($status == "Approved" || $status == "Employed" || $status == "New"){

				$guids = $guids . "," . $uid;

				$celphone = $this->parseCelphone($celphone);
				$recipients = $recipients . "," . $celphone;
			

				$names = $names . '<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$uid.'"><div style="border-radius: 25px; width: auto; float: left; border: 2px solid #6297BF; padding-top: 3px; padding-bottom: 3px; padding-left: 15px; padding-right: 15px; font-weight:bold; ">
				'.$firstname . " " . $lastname .'	</div></a>';
			}
					
		}

		$count = substr_count($recipients, ',')+1;
		
		echo'<div class="names" style="height: 200px; padding: 10px; overflow: scroll; overflow-x: hidden; border: 1px solid #ccc; border-bottom: 4px solid #6297BF;">'.$names.'</div>
		<div style="font-weight: bold; font-size: 14px; text-align: center;">This SMS will be sent to <u>'.$count.'</u> people</div>';

		$recipients = str_replace(",\n,", ",", $recipients); 
		$recipients = str_replace(",,", ",", $recipients);



		include $this->core->conf['conf']['formPath'] . "newsms.form.php";
	}

	private function parseCelphone($celphone){
		$celphone = preg_replace('/[^\da-z ,]/i', '', $celphone);
		$celphone = str_replace(" ", ",", $celphone);

		if (strpos($celphone, ',')) {
			$cs = explode(",", $celphone);

			foreach($cs as $celphone){
				$celphone = substr($celphone, 0, strrpos( $celphone, '/'));

				$length = strlen((string)$celphone);
				if($length == 10) {
					return $celphone;
				}elseif($length == 9) {
					if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celhphone; }
					return $celphone;
				}else if($length == 12) {
					$celphone = substr($celphone, 2);
					return $celphone;
				}else if($length > 12) {
					$celphone = substr($celphone, 0, 9);
					return $celphone;
				}
			}
		} else {
			$cs = explode("/", $celphone);
			$celphone = $cs[0];
			$length = strlen((string)$celphone);
			

			if($length == 10) {
				return $celphone;
			}elseif($length == 9) {
				if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celphone; }
				return $celphone;
			}else if($length == 12) {
				$celphone = substr($celphone, 2);
				return $celphone;
			}else if($length > 12) {
				$celphone = substr($celphone, 0, 9);
				return $celphone;
			}
		}
			
	}


	public function sendSms() {
		$message = $this->core->cleanPost['message'];
		$recipients = $this->core->cleanPost['recipients'];
		$uids = $this->core->cleanPost['uids'];
		$author = $this->core->userID;

		$rcv = explode(",", $recipients);
		
		$prefix = "26";
		$multi = FALSE;

		$reps = "";
		foreach($rcv as $number){
			$reps = $reps . "$prefix$number,";
			$multi = TRUE;
		}

		if($multi == FALSE){
			$reps = $prefix . $recipients;
		}

		$reps = rtrim($reps, ",");

		$sql = "INSERT INTO `sms` (`ID`, `Date`, `Message`, `Author`, `Receipients`, `Successful`, `Status`, `RecipientID`) 
			 VALUES (NULL, NOW(), '$message', '$author', '$reps', '', 'Awaiting approval', '$uids');";


		echo'<h2>SMS set for approval</h2>';

		$this->core->database->doInsertQuery($sql);

		$this->manageSMS();

	}

	public function approveSms($item){
		if ($item == "all"){
			$sql = 'SELECT * FROM `sms` WHERE `Status` = "Awaiting approval"';
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {
				$ids = $row[0];
				echo'<h2>SMS approved. Sending has started</h2>';

				$sqls = 'UPDATE `sms` SET `Status` = "Approved" WHERE `ID` = '.$ids.';';
				$rund = $this->core->database->doInsertQuery($sqls);

				$this->queSms($ids);
			}
		} else {
			echo'<h2>SMS approved. Sending has started</h2>';

			$sql = 'UPDATE `sms` SET `Status` = "Approved" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);

			$this->queSms($item);
		}
	}

	public function deleteSms($item){
		echo'<h2>SMS deleted.</h2>';

		$sql = 'DELETE FROM `sms` WHERE `ID` = '.$item.';';
		$run = $this->core->database->doInsertQuery($sql);

		$this->manageSMS();
	}
 
	private function queSms($item){

		ignore_user_abort(true);
		set_time_limit(0);

		$sql = 'SELECT * FROM `sms` WHERE ID = '.$item.' AND `Status` = "Approved";';
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$item = $row[0];
			$reps = $row[4];
			$message = $row[2];
			$message = urlencode($message);
			$status = $row[6];
			$guids = $row[7];

			$sql = 'UPDATE `sms` SET `Status` = "Sending" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);

			$count = substr_count($reps, ',')+1;

			$success = 0;
			$failed= 0;


			$i = 0;
			$countd = substr_count($guids, ',')+1;
			$rcv = explode(",", $guids);
			echo 'Custom parsed message, sending to parser: + ';

			while($i < $countd){
				$recipientID = $rcv[$i];

				echo $recipientID;
				$sd = $this->parseMessage($item, $recipientID, $message);

				$success = $success + $sd;
				echo $success . " - ";
				$i++;
				continue;
			}
			


			if($count > 50){
					echo '<br> Starting multiple que: <br>';
					$rcv = explode(",", $reps);
					$reps = "";

					$i = 0;
					while($i < $count){
						if($d<50){
							$reps = $rcv[$i] . "," . $reps;
							$i++; $d++;
						} else {
							$sd = $this->submitSms($item, $message, $reps);
							$success = $success + $sd;
							echo $success . " - ";
							$reps = "";
							$d = 0;
						}
					}

					$sd = $this->submitSms($item, $message, $reps);
					$success = $success + $sd;
					echo $success . " - ";

					$sql = 'UPDATE `sms` SET `Status` = "Sent", `Successful` = "'.$success.'" WHERE `ID` = '.$item.';';
					$this->core->database->doInsertQuery($sql);
					break;
				} else {
					$success = $this->submitSms($item, $message, $reps);

					$sql = 'UPDATE `sms` SET `Status` = "Sent", `Successful` = "'.$success.'" WHERE `ID` = '.$item.';';
					$this->core->database->doInsertQuery($sql);
					break;
				}
			}
		
	}

	public function parseMessage($item, $recipientID, $message){
		
		$sql = "SELECT *  FROM `basic-information` WHERE `ID` = '$recipientID'";
		$run = $this->core->database->doSelectQuery($sql);

		$success = 0;

		// PAYMENT VERIFICATION
		require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);

		while ($row = $run->fetch_assoc()) {
			
			$phone = $row['MobilePhone'];
			$phone = $this->parseCelphone($phone);
			$prefix = "26";
			$phone = $prefix . $phone;


			$balance = $payments->getBalance($recipientID);

			$message = str_replace("%25ID%25", $row['ID'], $message);
			$message = str_replace("%25NAME%25", $row['FirstName'] . " " . $row['LastName'], $message);
			$message = str_replace("%25PHONE%25", $row['MobilePhone'], $message);
			$message = str_replace("%25NRC%25", $row['GovernmentID'], $message);
			$message = str_replace("%25MODE%25", $row['StudyType'], $message);
			$message = str_replace("%25STATUS%25", $row['Status'], $message);
			$message = str_replace("%25BALANCE%25", $balance, $message);

			echo "<br>NOW SENDING $item - $message";
			$success = $this->submitSms($item, $message, $phone);
		}

		return $success; 
	}

	public function submitSms($item, $message, $reps){
				
		$url = $this->core->conf['sms']['server'];
		$username = urlencode($this->core->conf['sms']['username']);
		$password = urlencode($this->core->conf['sms']['password']);
		$sender = urlencode($this->core->conf['sms']['senderid']);
		$source = urlencode($this->core->conf['sms']['source']);
		$message = urldecode($message);

		$api_key='0387f1cb8d434b0665dce7e9f8881e05';
		$sender_id='NIPA';

		$url ='https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/'.$api_key.'/contacts/'.$reps.'/senderId/'.$sender_id.'/message/'.$message;
				
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => FALSE,
		  CURLOPT_SSL_VERIFYPEER => FALSE,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  //CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Content-Type: application/json"
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err . '</br>';
		} else {
		  echo $response . '</br>';
		}

		if($item != 0){
			$sql = 'UPDATE `sms` SET `Status` = "Sending" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);
		}

		
		$pos = strpos($response, "NOT_ENOUGH_UNITS");
		if ($pos === false) {
			$units = TRUE;
		} else {
			echo'<div class="errorpopup">NO UNITS LEFT PLEASE CALL SMS PROVIDER</div>';
			return;
		}


		$success = 0;

		$pos = strpos($response, "SUCCESS");

		if($pos === false){
		} else {
			echo 'FAILED';
			return;
		}

		if($item != 0){
			$sql = 'UPDATE `sms` SET `Status` = "Sending", `Successful` = `Successful`+'.$success.' WHERE `ID` = '.$item.';';
			$this->core->database->doInsertQuery($sql);
		}

		return $success;
	}
	public function directSms($item, $message,$author){
				
		$url = $this->core->conf['sms']['server'];
		$username = urlencode($this->core->conf['sms']['username']);
		$password = urlencode($this->core->conf['sms']['password']);
		$sender = urlencode($this->core->conf['sms']['senderid']);
		$source = urlencode($this->core->conf['sms']['source']);
		$message = urldecode($message);

		$phone = $this->parseCelphone($item);
			$prefix = "26";
			$phone = $prefix . $phone;
		//$json2 = "{\r\n    \"username\": \"mulungushiuni\",\r\n    \"password\": \"p@ssword@1\",\r\n    \"recipient\": [$phone],\r\n    \"message\": \"$message\",\r\n    \"senderid\": \"Mulungushi\",\r\n    \"source\": \"Mulungushi University\"\r\n}\r\n";
		//$obj =  json_encode($json);
		//echo $message.'  to '.$reps.' </br> '.$json.' </br> json2  => </br> '.$json2.' </br> json 3 => </br> ';
		
		
		$api_key='0387f1cb8d434b0665dce7e9f8881e05';
		$sender_id='NIPA';

		$url ='https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/'.$api_key.'/contacts/'.$phone.'/senderId/'.$sender_id.'/message/'.$message;
				
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => FALSE,
		  CURLOPT_SSL_VERIFYPEER => FALSE,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  //CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Content-Type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  //echo "cURL Error #:" . $err;
		} else {
		  //echo $response;
		}

		if($item != 0){
			$sql = 'INSERT INTO `sms` SET `Date`=NOW(), `Message`="'.$message.'", `Receipients`="'.$phone.'",`Status` = "Sent",`Author`="'.$author.'";';
			$run = $this->core->database->doInsertQuery($sql);
		}

		
		$pos = strpos($response, "NOT_ENOUGH_UNITS");
		if ($pos === false) {
			$units = TRUE;
		} else {
			echo'<div class="errorpopup">NO UNITS LEFT PLEASE CALL SMS PROVIDER</div>';
			return;
		}


		$success = 0;

		$pos = strpos($response, "SUCCESS");

		if($pos === false){
		} else {
			echo 'FAILED';
			return;
		}

		if($item != 0){
			$sql = 'UPDATE `sms` SET `Status` = "Sending", `Successful` = `Successful`+'.$success.' WHERE `ID` = '.$item.';';
			$this->core->database->doInsertQuery($sql);
		}

		return $success;
	}
	
	public function systemSms($item){
		
		if ($item == 'MuLuNgUsHi'){
			
			$message = $_GET['msg'];
			$reps = $_GET['reps'];
			
			
			$data['username'] = $username;
			$data['password'] = $password;
			$data['recipient'] = $reps;
			$data['message'] = $message;
			$data['source'] = $source;
			$data['senderid'] = $senderid;
			$obj =  json_encode($data);
			
			$url = $this->core->conf['sms']['server'];
			$username = urlencode($this->core->conf['sms']['user']);
			$password = $this->core->conf['sms']['password'];
			$sender = urlencode($this->core->conf['sms']['senderid']);
			$source = $this->core->conf['sms']['source'];
			
			
			 $json2 = "{\r\n    \"username\": \"mulungushiuni\",\r\n    \"password\": \"p@ssword@1\",\r\n    \"recipient\": [$reps],\r\n    \"message\": \"$message\",\r\n    \"senderid\": \"Mulungushi\",\r\n    \"source\": \"Mulungushi University\"\r\n}\r\n";
			$obj =  json_encode($json);
			echo $message.'  to '.$reps.' </br> '.$json.' </br> json2  => </br> '.$json2.' </br> json 3 => </br> ';
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_SSL_VERIFYHOST => FALSE,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $json2,
			  CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"Content-Type: application/json"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			  echo $response;
			}
		}else{
			echo "Invalid Request";
		}
	}
	public function zamtelSms($item){
		
		$api_key='0387f1cb8d434b0665dce7e9f8881e05';
		$sender_id='NIPA';
		$msg = isset($_GET['msg']) ? $_GET['msg'] : 'Hello Test sms from Nipa';
		
		//http://bulksms.zamtel.co.zm/api/sms/balance?key=0387f1cb8d434b0665dce7e9f8881e05
		
		//http://bulksms.zamtel.co.zm/api/sms/createSenderID?key=0387f1cb8d434b0665dce7e9f8881e05&senderId=NIPA
		
		//https:bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/0387f1cb8d434b0665dce7e9f8881e05/contacts/260978614927/senderId/NIPA/message/Hello%20world%20from%20bulksms
		//https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/0387f1cb8d434b0665dce7e9f8881e05/contacts/260978614927/senderId/NIPA/message/Hello%20world%20from%20bulksms
		
		//https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/0387f1cb8d434b0665dce7e9f8881e05/contacts/260978614927/senderId/NIPA/message/Test_from_NipaSIS
		$contact=$item;
		
		$url ='https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/'.$api_key.'/contacts/'.$contact.'/senderId/'.$sender_id.'/message/'.$msg;
		
		$balance_url= 'http://bulksms.zamtel.co.zm/api/sms/balance?key='.$api_key;
		
		//$json = file_get_contents($url);
		//$obj = json_decode($json); 
		
		//echo $json' </br>'.$url.' </br>';
		//echo ' </br>'.$url.' </br>';
		//echo ' msg='.$msg;
		//echo ' sentto='.$contact.'</br>';
		//var_dump($obj);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_SSL_VERIFYHOST => FALSE,
		  CURLOPT_SSL_VERIFYPEER => FALSE,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  //CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Content-Type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
	
	}

}

?>

