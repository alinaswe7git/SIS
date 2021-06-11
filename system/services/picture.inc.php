<?php
class picture {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function runService($core) {
		$this->core = $core;
		
		$key = $this->core->cleanGet['key'];

		$sql = "SELECT * FROM `authentication` WHERE `Key` = '$key'";
		$run = $this->core->database->doSelectQuery($sql);
		while ($row = $run->fetch_assoc()) {
			$uid = $row['StudentID'];
		}

		if($uid == ''){
			echo'NO KEY';
		}

		if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
			$filename = '/var/www/html/sis/datastore/identities/pictures/' . $uid . '.png_final.png';
		} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
			$filename = '/var/www/html/sis/datastore/identities/pictures/' . $uid . '.png';
		} else {
			$filename = '/var/www/html/sis/templates/default/images/noprofile.png';
		}

		$mime = mime_content_type($filename);

    		header("Content-type: $mime");
    		header('Content-Disposition: attachment; filename='.urlencode('profile.png'));
    		header('Content-Length: ' . filesize($filename));

		$content = readfile($filename);

	}
}
?>